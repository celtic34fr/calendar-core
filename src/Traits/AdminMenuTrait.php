<?php

namespace Celtic34fr\CalendarCore\Traits;

use Celtic34fr\CalendarCore\Menu\MenuItem;
use Exception;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem as KnpMenuItem;

trait AdminMenuTrait
{
    private function extractsMenus(KnpMenuItem $menu, string $group): array
    {
        $factory = new MenuFactory();
        $menuBefore = new MenuItem('before', $factory);
        $menuGroups = new MenuItem($group, $factory);
        $menuAfter = new MenuItem('after', $factory);
        $children = $menu->getChildren();
        $isGroup = false;
        $idx = 0;

        /** @var MenuItem $child */
        foreach ($children as $name => $child) {
            if ((!$child->getExtra('group') || $child->getExtra('group') != $group) && !$isGroup) {
                $menuBefore->addChild($name, $this->getMenuOptions($child));
                if ($child->getChildren()) {
                    /** @var MenuItem $childChild */
                    foreach ($child->getChildren() as $childName => $childChild) {
                        $menuBefore[$name]->addChild($childName, $this->getMenuOptions($childChild));
                    }
                }
                $idx += 1;
            } elseif (!$isGroup || $child->getExtra('group') == $group) {
                $isGroup = true;
                $menuGroups->addChild($name, $this->getMenuOptions($child));
                if ($child->getChildren()) {
                    /** @var MenuItem $childChild */
                    foreach ($child->getChildren() as $childName => $childChild) {
                        $menuGroups[$name]->addChild($childName, $this->getMenuOptions($childChild));
                    }
                }
                $idx += 1;
            } else {
                $menuAfter->addChild($name, $this->getMenuOptions($child));
                if ($child->getChildren()) {
                    /** @var MenuItem $childChild */
                    foreach ($child->getChildren() as $childName => $childChild) {
                        $menuAfter[$name]->addChild($childName, $this->getMenuOptions($childChild));
                    }
                }
                break;
            }
        }

        return [$menuBefore, $menuGroups, $menuAfter];
    }

    private function addMenu(array $menusToAdd, MenuItem $menu): MenuItem
    {
        foreach ($menusToAdd as $name => $datas) {
            switch (true) {
                case (!array_key_exists($name, $menu->getChildren()) && $datas['type'] === "menu"):
                    $menu->addChild($name, $datas['item']);
                    break;
                case ($datas['type'] === "smenu"):
                    $menuParent = $datas['parent'];
                    if (empty($menuParent)) {
                        throw new Exception("SouMenu $name sans menu parent");
                    } else if (!empty($menuParent) && (!array_key_exists($menuParent, $menu->getChildren()))) {
                        if (!array_key_exists($menuParent, $menusToAdd)) {
                            throw new Exception("SousMenu $name dont le menu parent $menuParent est introuvable");
                        } else {
                            $menu->addChild($menuParent, $menusToAdd[$menuParent]['item']);
                        }
                    }
                    if (array_key_exists($menuParent, $menu->getChildren())) {
                        $menu[$menuParent]->addChild($name, $datas['item']);
                    }
                    break;
            }
        }
        return $menu;
    }

    private function emptyMenuItem(MenuItem $menu): MenuItem
    {
        $children = $menu->getChildren();
        foreach ($children as $name => $child) {
            $menu->removeChild($name);
        }
        return $menu;
    }

    private function rebuildMenu(KnpMenuItem $menu, MenuItem $menuBefore, MenuItem $menuGroups, MenuItem $menuAfter): KnpMenuItem
    {
        $children = array_merge($menuBefore->getChildren(), $menuGroups->getChildren(), $menuAfter->getChildren());
        $menu->setChildren($children);
        return $menu;
    }

    public function getMenuOptions(KnpMenuItem $menu): array
    {
        return [
            'name' => $menu->getName(),
            'label' => $menu->getLabel(),
            'linkAttributes' => $menu->getLinkAttributes(),
            'childrenAttributes' => $menu->getChildrenAttributes(),
            'labelAttributes' => $menu->getLabelAttributes(),
            'uri' => $menu->getUri(),
            'attributes' => $menu->getAttributes(),
            'extras' => $menu->getExtras(),
            'displayChildren' => $menu->getDisplayChildren(),
        ];
    }
}
