<?php

namespace Celtic34fr\CalendarCore\Menu;

use Knp\Menu\FactoryInterface;
use Knp\menu\MenuItem as MenuItemBase;

class MenuItem extends MenuItemBase
{
    /**
     * return external array with all options declared for the urrent MenuItem
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'linkAttributes' => $this->linkAttributes,
            'childrenAttributes' => $this->childrenAttributes,
            'labelAttributes' => $this->labelAttributes,
            'uri' => $this->uri,
            'attributes' => $this->attributes,
            'extras' => $this->extras,
            'display' => $this->display,
            'displayChildren' => $this->displayChildren,
        ];
    }

    /**
     * set by external array with all options declared for the urrent MenuItem
     * @param array $options
     * @return MenuItem
     */
    public function setOptions(array $options): MenuItem
    {
        $this->setName((string) $options['name'] ?? "");
        $this->setLabel((string) $options['label'] ?? "");
        $this->setLinkAttributes($options['linkAttributes'] ?? []);
        $this->setChildrenAttributes($options['childrenAttributes'] ?? []);
        $this->setLabelAttributes($options['labelAttributes'] ?? []);
        $this->setUri((string) $options['uri'] ?? "");
        $this->setAttributes($options['attributes'] ?? []);
        $this->setExtras($options['extras'] ?? []);
        $this->setDisplay((bool) $options['display'] ?? true);
        $this->setDisplayChildren((bool) $options['displayChildren'] ?? true);

        return $this;
    }

    /**
     * return MenuItem $name if exists, else false
     * @param string $name
     * @return MenuItem|false
     */
    public function getMenuItem(string $name): mixed
    {
        if ($this->hasChild($name)) return $this->children[$name];
        return false;
    }

    /**
     * update the MenuItem named $name xith contains of MenuItem $menuItem
     * @param string $name
     * @param MenuItm $menuItem
     * @return MenuItem|false
     */
    public function setMenuItem(string $name, MenuItem $menuItem): mixed
    {
        if ($this->hasChild($name)) {
            $this->children[$name] = $menuItem;
            return $this;
        }
        return false;
    }

    /**
     * return factory insdtance that had been use for creating MenuItem
     * @return FactoryInterface
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * test if current menu contains $childname MenuItm
     * @param string $childname
     * @return bool
     */
    public function hasChild(string $childName): bool
    {
        return array_key_exists($childName, $this->children);
    }
}
