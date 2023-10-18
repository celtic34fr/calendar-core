<?php

namespace Celtic34fr\CalendarCore\FormEntity;

class CalEventItems
{
    private ?array $items = null;

    /**
     * @return array|null
     */
    public function getItems(): ?array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return CalEventItems
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @param array|CalEventItem $item
     * @return CalEventItems
     */
    public function addItem($item): self
    {
        $realItem = $item;
        if (is_array($item)) {
            $realItem = new CalEventItem();
            $realItem->hydrateFromJson(json_encode($item));
        }
        $idx = $this->items ? sizeof($this->items) : 0;
        $this->items[$idx] = $realItem;
        return $this;
    }

    /**
     * param CalEventItem $item
     * return CalEventItems
     */
    /**
     * public function addItemCalEvent(CalEventItem $item): self
     * {
     *     $idx = $this->items ? sizeof($this->items) : 0;
     *     $this->items[$idx] = $item;
     *     return $this;
     * }
     */

    /**
     * @param CalEventItem $item
     * @return CalEventItems|bool
     */
    public function removeItem(CalEventItem $item): mixed
    {
        if (array_key_exists($item->getCle(), $this->items)) {
            unset($this->items[$item->getCle()]);
            return $this;
        }
        return false;
    }
}
