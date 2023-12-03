<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Enum\AlarmTypeEnums;

class EventAlarm
{
    private string $action;
    private string $trigger;

    private string $duration;
    private int $repeat;

    private string $attach;
    private string $description;
    private string $summary;
    private array $attendees;


    public function __construct(?array $valarm = null)
    {
        if ($valarm) $this->setByArray($valarm);
    }
    
    public function setByArray(array $valarm)
    {
        $this->setAction($valarm["ACTION"]);
        $this->setTrigger($valarm["TRIGGER"]);

        switch($valarm["ACTION"]) {
            case "AUDIO":
                if (array_key_exists("REPEAT", $valarm)) $this->setRepeat($valarm["REPEAT"]);
                if (array_key_exists("DURATION", $valarm)) $this->setDuration($valarm["DURATION"]);
                if (array_key_exists("ATTACH", $valarm)) $this->setAttach($valarm["ATTACH"]);
                break;
            case "DISPLAY":
                $this->setDescription($valarm["DESCRIPTION"]);
                if (array_key_exists("REPEAT", $valarm)) $this->setRepeat($valarm["REPEAT"]);
                if (array_key_exists("DURATION", $valarm)) $this->setDuration($valarm["DURATION"]);
                break;
            case "EMAIL":
                $this->setDescription($valarm["DESCRIPTION"]);
                $this->setSummary($valarm["SUMMARY"]);
                if (array_key_exists("ATTENDEE", $valarm)) {
                    foreach ($$valarm["ATTENDEE"] as $attendee) {
                        $this->addAttendee($attendee);
                    }
                }
                if (array_key_exists("REPEAT", $valarm)) $this->setRepeat($valarm["REPEAT"]);
                if (array_key_exists("DURATION", $valarm)) $this->setDuration($valarm["DURATION"]);
                break;
        }
    }

    /**
     * Get the value of action
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set the value of action
     */
    public function setAction(string $action): self
    {
        if (AlarmTypeEnums::isValid($action)) {
            $this->action = $action;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of trigger
     */
    public function getTrigger(): string
    {
        return $this->trigger;
    }

    /**
     * Set the value of trigger
     */
    public function setTrigger(string $trigger): self
    {
        $this->trigger = $trigger;
        return $this;
    }

    /**
     * Get the value of duration
     */
    public function getDuration(): string
    {
        return $this->duration;
    }

    /**
     * Set the value of duration
     */
    public function setDuration(string $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get the value of repeat
     */
    public function getRepeat(): int
    {
        return $this->repeat;
    }

    /**
     * Set the value of repeat
     */
    public function setRepeat(int $repeat): self
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * Get the value of attach
     */
    public function getAttach(): string
    {
        return $this->attach;
    }

    /**
     * Set the value of attach
     */
    public function setAttach(string $attach): self
    {
        $this->attach = $attach;
        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of summary
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * Set the value of summary
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * Get the value of attendees
     */
    public function getAttendees(): array
    {
        return $this->attendees;
    }

    public function addAttendee(Attendee $attendee)
    {
        if (!in_array($attendee, $this->attendees)) {
            $this->attendees[] = $attendee;
            return $this;
        }
        return false;
    }

    public function removeAttendee(Attendee $attendee)
    {
        if (in_array($attendee, $this->attendees)) {
            $idx = array_search($attendee, $this->attendees);
            unset($this->attendees[$idx]);
            return $this;
        }
        return false;
    }

    /**
     * Set the value of attendees
     */
    public function setAttendees(array $attendees): self
    {
        $this->attendees = $attendees;
        return $this;
    }
}