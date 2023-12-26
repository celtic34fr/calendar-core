<?php

namespace Celtic34fr\CalendarCore\Controller\Backend;

use Celtic34fr\CalendarCore\Entity\Parameter;
use Celtic34fr\CalendarCore\Form\CalEventItemsType;
use Celtic34fr\CalendarCore\FormEntity\CalEventItem;
use Celtic34fr\CalendarCore\FormEntity\CalEventItems;
use Celtic34fr\CalendarCore\Repository\CalendarRepository;
use Celtic34fr\CalendarCore\Repository\CalEventRepository;
use Celtic34fr\CalendarCore\Repository\CalTypeRepository;
use Celtic34fr\CalendarCore\Repository\ParameterRepository;
use Celtic34fr\CalendarCore\Traits\FormErrorsTrait;
use Celtic34fr\CalendarCore\Traits\UtilitiesTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

#[Route('parameters', name: 'parameters-')]
class ParametersController extends AbstractController
{
    use UtilitiesTrait;
    use FormErrorsTrait;

    const PARAM_CLE = "SysCalNature"; // nom de la liste de valeur dans Parameters
    const PARAM_VALEUR = "Liste des types d'événements de calendrier";

    private $schemaManager;

    public function __construct(
        private EntityManagerInterface $em,         // entity manager
        private CalEventRepository $calEventRepo,   // CalEvent repository
        private ParameterRepository $parameterRepo, // Parameter repository
        private CalTypeRepository $calTypeRepo,     // CelType repository
    ) {
        $this->schemaManager = $em->getConnection()->getSchemaManager();
    }

    #[Route('/calendars', name: 'calendars')]
    public function calendars(CalendarRepository $calendarRepo): Response
    {
        $calendars = $calendarRepo->findActiveCalendars();
        return $this->render('parameters/calendars.html.twig', [
            'calendars' => $calendars,
        ]);
    }

    #[Route('/cal_types', name: 'cal-types')]
    public function cal_types(): Response
    {
        $calTypes = $this->calTypeRepo->findAll();
        return $this->render('parameters/cal-types.html.twig', [
            'cal_types' => $calTypes,
        ]);
    }

    #[Route('type_event', name: 'type-event')]
    /** Gestion des types d'événements, rendez-vous : ajout - modification - suppression
     *
     * @param Request $request
     * @return void
     */
    public function eventTypeGest(Request $request)
    {
        /** récupération du préfix de création des table dans Bolt CMS */
        $dbPrefix = $this->getParameter('bolt.table_prefix');
        $twig_context = [];
        $dbEvtKeys = [];

        /** contrôle existance table nécessaire à la méthode 'parameters' */
        if ($this->existsTable($dbPrefix . 'parameters') == true) {
            /** recherche des informations de base */
            $calEventEntete = $this->parameterRepo->findCurrentOneBy(['cle' => self::PARAM_CLE, 'ord' => 0]);
            $calEventItems = $this->parameterRepo->findItemsByCle(self::PARAM_CLE);
            $errors = [];

            if (!$calEventEntete) {
                /** pas encore de liste de paramètres SysCalEvent => création entête */
                $calEventEntete = new Parameter();
                $calEventEntete->setCle(self::PARAM_CLE)->setOrd(0)->setValeur(self::PARAM_VALEUR);
                $this->em->persist($calEventEntete);
                $this->em->flush();
            }

            /** mise en place du formulaire à partir de $calEventItems trouvés en base */
            $items = new CalEventItems();
            if ($calEventItems) {
                foreach ($calEventItems as $calEventItem) {
                    $item = new CalEventItem();
                    $item->hydrateFromJson($calEventItem->getValeur());
                    $item->setId($calEventItem->getId());
                    $items->addItem($item);
                    $dbEvtKeys[$item->getCle()] = $item->getId();
                }
            }
            $form = $this->createForm(CalEventItemsType::class, $items);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    /** traitement du formulaire soumis et validé par Symfony */
                    $idx = 0;
                    $formItems = $this->getFormItems($_POST);

                    /** @var CalEventItem $item */
                    foreach ($formItems->getItems() as $item) {
                        $idx++;
                        /* recherche de l'item de la liste de paramètres pour modification */
                        $calEvtItem = $this->parameterRepo->findByPartialFields(['valeur' => $item->getCle()]);
                        if (!$calEvtItem) {
                            $calEvtItem = new Parameter();
                            $calEvtItem->setCle(self::PARAM_CLE);
                        } else {
                            if (sizeof($calEvtItem) > 1) {
                                throw new Exception("Evt Type {$item->getCle()} existe plusieurs fois : incohérent");
                            }
                            $calEvtItem = $calEvtItem[0];
                            unset($dbEvtKeys[$item->getCle()]);
                        }
                        $calEvtItem->setOrd($idx);
                        $calEvtItem->setValeur($item->getValeur());
                        if (!$calEvtItem->getId()) {
                            $this->em->persist($calEvtItem);
                        }
                    }
                    $this->em->flush();

                    /** traitement des clé non reconduites */
                    if ($dbEvtKeys) {
                        foreach ($dbEvtKeys as $dbEvtKey => $dbId) {
                            $item = $this->parameterRepo->find($dbId);
                            if ($this->calEventRepo->findEventsByCategory($item)) {
                                /** duppression impossible => existe des événement avec cet type */
                                $this->addFlash('warning', "Le type d'évèment $dbEvtKey est utilisé, suppression impossible");
                                $idx++;
                                $item->setOrd($idx);
                            } else {
                                $this->em->remove($item);
                            }
                        }
                        $this->em->flush();
                    }

                    $this->addFlash('success', "Table des types d'évèments de calendrier a été bien enregitrée en base");
                    return $this->redirectToRoute('bolt_dashboard', [], 303);
                } else {
                    /** recherche des erreurs dans les sous formulaires */
                    $errors = $this->formatErrors($this->getErrors($form));
                }
            }

            $twig_context['entete'] = $calEventEntete;
            $twig_context['form'] = $form->createView();
            $twig_context['errors'] = $errors;
        } else {
            $this->addFlash('danger', "La table {$dbPrefix}parameters n'existe pas, veuillez en avertir l'administrateur");
            $twig_context['entete'] = null;
            $twig_context['form'] = null;
            $twig_context['errors'] = null;
        }

        return $this->render("@calendar-core/cal_types/type_gest.html.twig", $twig_context);
    }

    /**
     * @param array $post
     * @return CalEventItems
     */
    private function getFormItems(array $post): CalEventItems
    {
        $formItems = new CalEventItems();

        $post = $post['cal_event_items'] ?? [];
        $post = $post['items'] ?? [];
        foreach ($post as $type) {
            $item = new CalEventItem();
            $item->hydratefromArray($type);
            $formItems->addItem($item);
        }

        return $formItems;
    }

    /**
     * @param array $rawErrors
     * @return array
     */
    private function formatErrors(array $rawErrors): array
    {
        $formatedErrors = [];
        $rawErrors = $rawErrors['Liste des catégories'];
        foreach ($rawErrors as $occurs => $errorsOccurs) {
            foreach ($errorsOccurs as $field => $errors) {
                $formatedFieldErrors = "";
                foreach ($errors as $error) {
                    $formatedFieldErrors .= "<p>" . $error . "</p>";
                }
                if (!array_key_exists($occurs, $formatedErrors)) $formatedErrors[$occurs] = [];
                $formatedErrors[$occurs][$field] = $formatedFieldErrors;
            }
        }
        return $formatedErrors;
    }
}