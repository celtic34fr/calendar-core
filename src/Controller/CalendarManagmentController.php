<?php

namespace Celtic34fr\CalendarCore\Controller;

use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Form\FormEventType;
use Celtic34fr\CalendarCore\Repository\CalEventRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/calendar/managment', name: 'calendar-managment-')]
class CalendarManagmentController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,  // entity manager
        private CalEventRepository $calEventRepo,       // CalEvent table Repository
    ) {
    }

    #[Route('/add_event_form', name: 'add-form')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(FormEventType::class);
        return $this->render('@calendar-core/managment/form_event.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/add_event', name: 'add')]
    public function add(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $title = $request->request->get('title');
            $start = $request->request->get('start');
            $end   = $request->request->get('end');
        }

        // $response = $this->checkValidRequest($request);
        $response->setStatusCode(500);
        return $response;
    }

    #[Route('/fetch_event', name: 'fetch')]
    public function fetch(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $json = array();
            $now = new DateTime('now');
            $firstDayOfMonth = DateTime::createFromFormat("Y-m-d", $now->format("Y-m")."-01");
            $lastDayOfMonth = $firstDayOfMonth->add(new DateInterval("P1M"))->sub(new DateInterval("P1D"));
            $events = $this->calEventRepo->findAllEventFromToDate($firstDayOfMonth, $lastDayOfMonth);
            if (!$events) $events = [];
            return $response->setData(['events' => $events ]);
        }

        // $response = $this->checkValidRequest($request);
        $response->setStatusCode(500);
        return $response;
    }

    #[Route('/edit_event', name: 'edit')]
    public function edit(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $title = (string) $request->request->get('title');
            $start = (string) $request->request->get('start');
            $end   = (string) $request->request->get('end');
            $idEvt = (int) $request->request->get('id');

            $event = $this->entityManager->getRepository(CalEvent::class)->find($idEvt);
        }

        // $response = $this->checkValidRequest($request);
        $response->setStatusCode(500);
        return $response;
    }

    #[Route('/delt_event', name: 'delt')]
    public function delt(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $idEvt = (int) $request->request->get('id');

            $event = $this->entityManager->getRepository(CalEvent::class)->find($idEvt);
            $this->entityManager->remove($event);
        }

        // $response = $this->checkValidRequest($request);
        $response->setStatusCode(500);
        return $response;
    }
}