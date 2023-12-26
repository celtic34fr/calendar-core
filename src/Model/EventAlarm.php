<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Enum\AlarmTypeEnums;
use DateTime;

class EventAlarm
{
    const RELATED_BEFORE = "START";
    const RELATED_AFTER = "END";

    private string $action;

    //private array $trigger;
    private string $related = "START";
    private ?string $duration = null;
    private ?DateTime $absDatetime = null;

    private ?int $repeat = null;

    private ?array $attach = null;
    private ?string $description = null;
    private ?string $summary = null;
    private ?array $attendees = null;


    public function __construct(?array $valarm = null)
    {
        if ($valarm) $this->setByArray($valarm);
    }
    
    public function setByArray(array $valarm)
    {
        $this->setAction($valarm["ACTION"] ?? AlarmTypeEnums::Display);
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
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set the value of action
     * @param string $action
     * @return self|bool
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
     * @return array
     */
    public function getTrigger(): array
    {
        return [
            "RELATED" => $this->getRelated(),
            "DURATION" => $this->getDuration(),
            "DATE-TIME" => $this->getAbsDatetime()
        ];
    }

    /**
     * Set the value of trigger
     * @param array $trigger
     * @return self
     */
    public function setTrigger(array $trigger): self
    {
        $this->setRelated(self::RELATED_BEFORE);
        if (array_key_exists("RELATED", $trigger)) $this->setRelated($trigger["RELATED"]);
        if (array_key_exists("DURATION", $trigger)) $this->setDuration($trigger["DURATION"]);
        if (array_key_exists("DATE-TIME", $trigger)) $this->setAbsDatetime($trigger["DATE-TIME"]);

        return $this;
    }

    /**
     * Get the value of related in trigger
     * @return string|null
     */
    public function getRelated(): ?string
    {
        return $this->related;
    }

    /**
     * Set the value of related in trigger
     * @param string $related
     */
    public function setRelated(string $related): self
    {
        $this->related = $related;
        return $this;
    }

    /**
     * Get the value of duration
     * @return string|null
     */
    public function getDuration(): ?string
    {
        return $this->duration;
    }

    /**
     * Set the value of duration
     * @param string $duration
     */
    public function setDuration(string $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get the value of absDatetime
     * @return DateTime|null
     */
    public function getAbsDatetime(): ?DateTime
    {
        return $this->absDatetime;
    }

    /**
     * Set the value of absDatetime
     * @param DateTime|string $absDatetime
     * @return self|bool
     */
    public function setAbsDatetime(mixed $absDatetime): self
    {
        if (is_string($absDatetime)) {
            $absDatetime = DateTime::createFromFormat("Ymd\THis\Z", $absDatetime);
            $this->absDatetime = $absDatetime;
        } elseif ($absDatetime instanceof DateTime) {
            $this->absDatetime = $absDatetime;
        } else {
            return false;
        }
        return $this;
    }

    /**
     * Get the value of repeat
     * @return int|null
     */
    public function getRepeat(): ?int
    {
        return $this->repeat;
    }

    /**
     * Set the value of repeat
     * @param int $repeat
     * @return self
     */
    public function setRepeat(int $repeat): self
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * Get the value of attach
     * @return array|null
     */
    public function getAttach(): ?array
    {
        return $this->attach;
    }

    /**
     * Set the value of attach
     * @param array $attach
     * @return self
     */
    public function setAttach(array $attach): self
    {
        $this->attach = $attach;
        return $this;
    }

    /**
     * Get the value of description
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of summary
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * Set the value of summary
     * @param string $summary
     * @return self
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * Get the value of attendees
     * @return array|null
     */
    public function getAttendees(): ?array
    {
        return $this->attendees;
    }

    /**
     * @param Attendee $attendee
     * @return self|bool
     */
    public function addAttendee(Attendee $attendee): mixed
    {
        if (!in_array($attendee, $this->attendees)) {
            $this->attendees[] = $attendee;
            return $this;
        }
        return false;
    }

    /**
     * @param Attendee $attendee
     * @return sekf|bool
     */
    public function removeAttendee(Attendee $attendee): mixed
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
     * @param array $attendees
     * @return self
     */
    public function setAttendees(array $attendees): self
    {
        $this->attendees = $attendees;
        return $this;
    }
}
