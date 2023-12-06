<?php

namespace Celtic34fr\CalendarCore\Model;

use DateTime;

class TaskRecurrenceId
{
    private ?string $timezone = null;
    private ?string $range = null;
    private ?DateTime $value = null;

    public function hydrateFromArray(?array $recurrenceId = null)
    {
        if ($recurrenceId) {
            $this->setValue($recurrenceId["VALUE"]);
            if (array_key_exists("TZID", $recurrenceId)) $this->setTimezone($recurrenceId["TZID"]);
            if (array_key_exists("RANGE", $recurrenceId)) $this->setRange($recurrenceId["RANGE"]);
        }
    }

    /**
     * Get the value of timezone
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * Set the value of timezone
     * @param string $timezone
     * @return self
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Get the value of range
     * @return string|null
     */
    public function getRange(): ?string
    {
        return $this->range;
    }

    /**
     * Set the value of range
     * @param string $range
     * @return self
     */
    public function setRange(string $range): self
    {
        $this->range = $range;
        return $this;
    }

    /**
     * Get the value of value
     * @return DateTime|null
     */
    public function getValue(): ?DateTime
    {
        return $this->value;
    }

    /**
     * Set the value of value
     * @param DateTime $value
     * @return self
     */
    public function setValue(DateTime $value): self
    {
        $this->value = $value;
        return $this;
    }
}