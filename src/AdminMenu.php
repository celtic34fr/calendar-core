<?php

namespace Celtic34fr\CalendarCore;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Celtic34fr\CalendarCore\Menu\MenuItem as MenuItemCalTasks;
use Celtic34fr\CalendarCore\Traits\AdminMenuTrait;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** classe d'ajout des menu spécifiques pour le projet */
class AdminMenu implements ExtensionBackendMenuInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    use AdminMenuTrait;

    public function addItems(MenuItem $menu): void
    {
        /** @var MenuItemCalTasks $menuCalTasks */
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
        if (!$menuCalTasks->hasChild("Paramètres")) {
            $configurationItems = [
                "Paramètres" => [
                    'type' => 'menu',
                    'item' => [
                        'uri' => $this->urlGenerator->generate('bolt_menupage', [
                            'slug' => 'parametres',
                        ]),
                        'extras' => [
                            'group' => 'CalTasks',
                            'name' => 'Paramètres',
                            'slug' => 'parametres',
                            'icon' => 'fa-tools'
                        ]
                    ]
                ]
            ];
        }
        $configurationItems['Types d\'événements'] = [
            'type' => 'smenu',
            'parent' => "Paramètres",
            'item' => [
                'uri' => $this->urlGenerator->generate('parameters-type-event'),
                'extras' => [
                    'icon' => 'fa-tools',
                    'group' => 'CalTasks',
                ]
            ]
        ];
        $menuContacts = $this->addMenu($configurationItems, $menuCalTasks);

        /*
        $utilitairesItems = [];
        if (!$menuContacts->hasChild("Utilitaires")) {
            $utilitairesItems = [
                "Utilitaires" => [
                    'type' => 'menu',
                    'item' => [
                        'uri' => $this->urlGenerator->generate('bolt_menupage', [
                            'slug' => 'utilitaires',
                        ]),
                        'extras' => [
                            'group' => 'Contact',
                            'name' => 'Utilitaires',
                            'slug' => 'utilitaires',
                            'icon' => 'fa-toolbox'
                        ]
                    ]
                ]
            ];
        }
        $utilitairesItems['Gestion des courriels'] = [
            'type' => 'smenu',
            'parent' => "Utilitaires",
            'item' => [
                'uri' => $this->urlGenerator->generate('courriel_list'),
                'extras' => [
                    'icon' => 'fa-envelope',
                    'group' => 'Contact',
                ]
            ]
        ];
        $menuContacts = $this->addMenu($utilitairesItems, $menuContacts);

        $menu = $this->rebuildMenu($menu, $menuBefore, $menuContacts, $menuAfter);

        if (!$menu->getChild("Gestion des Contacts")) {
            $menu->addChild('Gestion des Contacts', [
                'extras' => [
                    'name' => 'Gestion des Contacts',
                    'type' => 'separator',
                    'group' => 'Contact',
                ]
            ]);

            if (!$menu->getChild("Utilitaires")) {
                $menu->addChild('Utilitaires', [
                    'uri' => $this->urlGenerator->generate('bolt_menupage', [
                        'slug' => 'utilitaires',
                    ]),
                    'extras' => [
                        'group' => 'Contact',
                        'name' => 'Utilitaires',
                        'slug' => 'utilitaires',
                    ]
                ]);
                $menu['Utilitaires']->addChild('Gestion des courriels', [
                    'uri' => $this->urlGenerator->generate('courriel_list'),
                    'extras' => [
                        'icon' => 'fa-envelope-circle-check',
                        'group' => 'Contact',
                    ]
                ]);
            }
        }
        */
    }
}
