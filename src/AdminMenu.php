<?php

namespace Celtic34fr\CalendarCore;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Celtic34fr\CalendarCore\Menu\MenuItem as MenuItemCalendar;
use Celtic34fr\CalendarCore\Service\ConfigService;
use Celtic34fr\CalendarCore\Traits\AdminMenuTrait;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** classe d'ajout des menu spécifiques pour le projet */
class AdminMenu implements ExtensionBackendMenuInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private ConfigService $configService;

    public function __construct(UrlGeneratorInterface $urlGenerator, ConfigService $configService) {
        $this→urlGenerator = $urlGenerator;
        $this->configService = $configService;
    }

    use AdminMenuTrait;

    public function addItems(MenuItem $menu): void
    {
        dd($this->configService);

        /** @var MenuItemCalendar $menuCalTasks */
        list($menuBefore, $menuCalTasks, $menuAfter) = $this->extractsMenus($menu, 'CalTasks');
        if (!$menuCalTasks->hasChild("Gestion des Calendriers et Taches")) {
            $menuCalTasks->addChild('Gestion des Calendriers et Taches', [
                'extras' => [
                    'name' => 'Gestion des Calendriers et Taches',
                    'type' => 'separator',
                    'group' => 'CalTasks',
                ]
            ]);
        }

        $configurationItems = [];
        $configurationItems = [
            "Les calendriers" => [
                'type' => 'menu',
                'item' => [
                    'uri' => $this->urlGenerator->generate('bolt_menupage', [
                        'slug' => 'calendars',
                    ]),
                    'extras' => [
                        'group' => 'CalTasks',
                        'name' => 'Les calendriers',
                        'slug' => 'calendars',
                        'icon' => 'fa-tools'
                    ]
                ]
            ]
        ];
        $configurationItems['Mes calendriers'] = [
            'type' => 'smenu',
            'parent' => "Les calendriers",
            'item' => [
                'uri' => $this->urlGenerator->generate('parameters-calendars'),
                'extras' => [
                    'icon' => 'fa-calendar',
                    'group' => 'CalTasks',
                ]
            ]
        ];
        $configurationItems['Les types d\'événements'] = [
            'type' => 'smenu',
            'parent' => "Les calendriers",
            'item' => [
                'uri' => $this->urlGenerator->generate('parameters-type-event'),
                'extras' => [
                    'icon' => 'fa-tools',
                    'group' => 'CalTasks',
                ]
            ]
        ];
        $menuContacts = $this->addMenu($configurationItems, $menuCalTasks);

    }
}
