<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Entity\Parameter;
use Celtic34fr\CalendarCore\EntityRedefine\ParameterCalEvent;
use Celtic34fr\CalendarCore\Enum\ClassificationEnums;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Model\EventAlarm;
use Celtic34fr\CalendarCore\Model\EventLocation;
use Celtic34fr\CalendarCore\Model\EventRepetition;
use Celtic34fr\CalendarCore\Traits\Model\ExtractDateTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatAttendeeTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatContactTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatLocationTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatOrganizerTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatRRuleTrait;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class EventICS
{
    use ExtractDateTrait;
    use FormatAttendeeTrait;
    use FormatOrganizerTrait;
    use FormatContactTrait;
    use FormatRRuleTrait;
    use FormatLocationTrait;

    private EntityManagerInterface $entityManager;

    private DateTime            $created_at;            // *
    private DateTime            $lastupdated_at;        // *
    private DateTime            $dateStart;             // *
    private DateTime            $dateEnd;               // *
    private ?string             $subject = null;        // *
    private ?string             $details = null;        // *
    private ?string             $bg_color = null;       //
    private ?string             $bd_color = null;       //
    private ?string             $tx_color = null;       //
    private bool                $allday;                // *
    private string              $status;                // *
    private ?Collection         $attendees = null;      // *
    private ?string             $uid = null;            // *
    private ?string             $classification = null; // *
    private ?EventLocation      $location = null;       // *
    private ?string             $timezone = null;       // *
    private ?EventRepetition    $frequence = null;      // *
    private ?Organizer          $organizer = null;      // *
    private ?Collection         $alarms = null;         // *

    private ?DateTime           $dtStamp = null;        //
    private ?string             $priority = null;       //
    private ?int                $seq = null;            //
    private ?string             $transp = null;         //
    private ?string             $url = null;            //
    private ?string             $recurId = null;        //
    private ?string             $duration = null;       //
    private ?Collection         $attachs = null;        //
    private ?Collection         $categories = null;     //
    private ?Contact            $contact = null;        //
    private ?DateTime           $exDate = null;         //
    private ?string             $rStatus = null;        //
    private ?string             $related = null;        //
    private ?string             $resources = null;      //
    private ?DateTime           $rDate = null;          // 

    public function __construct(EntityManagerInterface $entityManager, CalEvent $calEvent = null)
    {
        $this->entityManager = $entityManager;
        $this->setClassification(ClassificationEnums::Public->_toString());
        $this->setStatus(StatusEnums::NeedsAction->_toString());
        $this->attendees = new ArrayCollection();
        $this->alarms = new ArrayCollection();
        $this->attachs = new ArrayCollection();

        if ($calEvent) {
            $this->setCreatedAt($calEvent->getCreatedAt());
            $this->setLastupdatedAt($calEvent->getLastUpdated());
            $this->setDateStart($calEvent->getStartAt());
            $this->setDateEnd($calEvent->getEndAt());
            $this->setSubject($calEvent->getSubject());
            $this->setDetails($calEvent->getDetails());
            $this->setBgColor($calEvent->getBgColor());
            $this->setBdColor($calEvent->getBdColor());
            $this->setTxColor($calEvent->getTxColor());
            $this->setAllday($calEvent->getAllDay());
            $this->setStatus($calEvent->getStatus());
            foreach ($calEvent->getAttendees() as $attendee) {
                $this->addAttendee($attendee);
            }
            $this->setUid($calEvent->getUid());
            $this->setLocation($calEvent->getLocation());
            /** affectation du fuseau horaire si DateStart ne l'a pas fait */
            if (!$this->emptyTimezone()) $this->setTimezone($calEvent->getTimezone());
            $this->setFrequence($calEvent->getFrequence());
            if (!$calEvent->emptyOrganizer()) $this->setOrganizer($calEvent->getOrganizer());
            if (!$calEvent->emptyAlarms()) $this->setAlarms($calEvent->getAlarms());

            if ($calEvent->emptyDtStamp()) $this->setDtStamp($calEvent->getDtStamp());
            if (!$calEvent->emptyPriority()) $this->setPriority($calEvent->getPriority());
            if (!$calEvent->emptySeq()) $this->setSeq($calEvent->getSeq());
            if (!$calEvent->emptyTransp()) $this->setTransp($calEvent->getTransp());
            if (!$calEvent->emptyUrl()) $this->setUrl($calEvent->getUrl());
            if (!$calEvent->emptyRecurId()) $this->setRecurId($calEvent->getRecurId());
            if (!$calEvent->emptyDuration()) $this->setDuration($calEvent->getDuration());
            if (!$calEvent->emptyAttachs()) {
                foreach ($calEvent->getAttachs() as $attach) {
                    $this->addAttach($attach);
                }
            }
            if (!$calEvent->emptyCategories()) {
                foreach ($calEvent->getCategories() as $category) {
                    $this->addCategory($category);
                }
            }
            if (!$calEvent->emptyContact()) $this->setContact($calEvent->getContact());
            if (!$calEvent->emptyExDate()) $this->setExDate($calEvent->getExDate());
            if (!$calEvent->emptyRStatus()) $this->setRStatus($calEvent->getRStatus());
            if (!$calEvent->emptyRelated()) $this->setRelated($calEvent->getRelated());
            if (!$calEvent->emptyResources()) $this->setResources($calEvent->getResources());
            if (!$calEvent->emptyRDate()) $this->setRDate($calEvent->getRDate());
        }
    }

    /**
     * Build EventICS object from array generated by IcsCalendarReader::load()
     *
     * @param array $calArray
     * @param string $globalTimezone
     * @return EventICS
     */
    public function buildFromArray(array $calArray, string $globalTimezone = null): EventICS
    {
        /** initialisatio du fuseau horaire local au global */
        $globalTimezone = $globalTimezone ?? date_default_timezone_get();

        $this->setUid($calArray['UID'] ?? null);
        $this->setSubject($calArray['SUMMARY'] ?? null);
        $this->setDetails(array_key_exists('DESCRIPTION', $calArray) ? $calArray['DESCRIPTION'] : null);
        $this->setStatus(array_key_exists('STATUS', $calArray) ? $calArray['STATUS'] : "NEEDS-ACTION");

        $location = array_key_exists('LOCATION', $calArray) ? $calArray['LOCATION'] : null;
        $geo = array_key_exists("GEO", $calArray) ? $calArray['GEO'] : null;
        $location = $this->formatLocation($location, $geo);
        if ($location) {
            $this->setLocation($location);
        }

        $dtStart = $this->extractDateMutable($calArray['DTSTART'], $globalTimezone);
        $this->setDateStart($dtStart);
        
        $created = array_key_exists('CREATED', $calArray) ? $this->extractDate($calArray['CREATED'], $globalTimezone) : new DateTime('now');
        $this->setCreatedAt($created);
        $lastUpdated = array_key_exists('LAST-MODIFIED', $calArray) ? $this->extractDate($calArray['LAST-MODIFIED'], $globalTimezone) : null;
        if ($lastUpdated) $this->setLastupdatedAt($lastUpdated);

        /** date de fin événement : DTEND ou DURATION ou rien */
        if (array_key_exists('DURATION', $calArray) && !array_key_exists('DTEND', $calArray)) {
            $dtEnd = $this->calcEndDate($dtStart, $calArray['DURATION']);
        } elseif (array_key_exists('DTEND', $calArray)) {
            $dtEnd = $this->extractDateMutable($calArray['DTEND'], $globalTimezone);
        } else {
            $dtEnd = $dtStart;
        }
        $this->setDateEnd($dtEnd);
        
        if ($this->emptyTimezone() && $globalTimezone) $this->setTimezone($globalTimezone);

        $attendees = array_key_exists('ATTENDEE', $calArray) ? $calArray['ATTENDEE'] : null;
        if ($attendees) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($attendees as $attendee) {
                $this->addAttendee($this->formatAttendee($attendee));
            }
        }

        /* -> détermination du type d'événement par jour ou sur durée en fonction de DTSTART */
        $this->setAllday((int) $dtStart->format("His") == 0);

        /** -> intégration de la règle de répétition si présente */
        $rrule = array_key_exists('RRULE', $calArray) ? $calArray['RRULE'] : [];
        if ($rrule) {
            $rruleItem = $this->formatRRule($rrule);
            $this->setFrequence($rruleItem);
        }

        $organizer = array_key_exists('ORGANIZER', $calArray) ? $calArray['ORGANIZER'] : [];
        if ($organizer) {
            $taskOrganizer = $this->formatOrganizer($organizer);
            $this->setOrganizer($taskOrganizer);
        }

        if (array_key_exists("VALARM", $calArray)) {
            foreach ($calArray["VALARM"] as $valarm) {
                $alarm = new EventAlarm($valarm);
                $this->addAlarm($alarm);
            }
        }

        if (array_key_exists("CLASS", $calArray)) $this->setClassification($calArray["CLASS"]);

        $dtStamp = array_key_exists('DTSTAMP', $calArray) ? $this->extractDate($calArray['DTSTAMP'], $globalTimezone) : new DateTime('now');
        $this->setDtStamp($dtStamp);
        
        if (array_key_exists("PRIORITY", $calArray)) $this->setPriority($calArray["PRIORITY"]);
        $this->setSeq(array_key_exists("SEQUENCE", $calArray) ? (int) $calArray["SEQUENCE"] : 0);
        if (array_key_exists("TRANSP", $calArray)) $this->setTransp($calArray["TRANSP"]);
        if (array_key_exists("URL", $calArray)) $this->setUrl($calArray["URL"]);
        if (array_key_exists("RECURID", $calArray)) $this->setRecurId($calArray["RECURID"]);

        $attachs = array_key_exists('ATTACH', $calArray) ? $calArray['ATTACH'] : null;
        if ($attachs) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($attachs as $attach) {
                $this->addAttach($attach);
            }
        }
        $categories = array_key_exists('CATEGORY', $calArray) ? $calArray['CATEGORY'] : null;
        if ($categories) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($categories as $category) {
                $this->addCategory($category);
            }
        }

        $contact = array_key_exists('CONTACT', $calArray) ? $calArray['CONTACT'] : [];
        if ($contact) {
            $fbContact = $this->formatContact($contact);
            $this->setContact($fbContact);
        }

        if (array_key_exists("EXDATE", $calArray)) $this->setExDate($calArray["EXDATE"]);
        if (array_key_exists("RSTATUS", $calArray)) $this->setRStatus($calArray["RSTATUS"]);
        if (array_key_exists("RELATED", $calArray)) $this->setRelated($calArray["RELATED"]);
        if (array_key_exists("RESOURCES", $calArray)) $this->setResources($calArray["RESOURCES"]);
        if (array_key_exists("RDATE", $calArray)) $this->setRDate($calArray["RDATE"]);

        return $this;
    }

    public function toCalEvent(CalEvent $calEvent = null) : CalEvent
    {
        if (!$calEvent) $calEvent = new CalEvent();
        $calEvent->setCreatedAt($this->getCreatedAt());
        if (!$this->emptyLastupdatedAt()) $calEvent->setLastupdated($this->getLastUpdatedAt());
        if (!$this->emptyDateStart()) $calEvent->setStartAt($this->getDateStart());
        if (!$this->emptyDateEnd()) $calEvent->setEndAt($this->getDateEnd());
        if (!$this->emptySubject()) $calEvent->setSubject($this->getSubject());
        if (!$this->emptyDetails()) $calEvent->setDetails($this->getDetails());
        if (!$this->emptyBgColor()) $calEvent->setBgColor($this->getBgColor());
        if (!$this->emptyBdColor()) $calEvent->setBdColor($this->getBdColor());
        if (!$this->emptyTxColor()) $calEvent->setTxColor($this->getTxColor());
        $calEvent->setAllday($this->isAllday());
        $calEvent->setStatus($this->getStatus());
        if (!$this->emptyUid()) $calEvent->setUid($this->getUid());
        if (!$this->emptyClassification()) $calEvent->setClassification($this->getClassification());
        if (!$this->emptyLocation()) $calEvent->setLocation($this->getLocation());
        if (!$this->emptyTimezone()) $calEvent->setTimezone($this->getTimezone());
        if (!$this->emptyFrequence()) $calEvent->setFrequence($this->getFrequence());
        if (!$this->emptyAttendees()) {
            foreach ($this->getAttendees() as $attendee) {
                $calEvent->addAttendee($attendee);
            }
        }
        if (!$this->emptyOrganizer()) $calEvent->setOrganizer($this->getOrganizer());
        if (!$this->emptyAlarms()) {
            foreach ($this->getAlarms() as $alarm) {
                $calEvent->addAlarm($alarm);
            }
        }
        if (!$this->emptyDtStamp()) $calEvent->setDtStamp($this->getDtStamp());
        if (!$this->emptyPriority()) $calEvent->setPriority($this->getPriority());
        if (!$this->emptySeq()) $calEvent->setSeq($this->getSeq());
        if (!$this->emptyTransp()) $calEvent->setTransp($this->getTransp());
        if (!$this->emptyUrl()) $calEvent->setUrl($this->getUrl());
        if (!$this->emptyRecurId()) $calEvent->setRecurId($this->getRecurId());
        if (!$this->emptyDuration()) $calEvent->setDuration($this->getDuration());
        if (!$this->emptyAttachs()) {
            foreach ($this->getAttachs() as $attach) {
                $calEvent->addAttach($attach);
            }
        }
        if (!$this->emptyCategories()) {
            foreach ($this->getCategories() as $category) {
                $calEvent->addCategory($category);
            }
        }
        if (!$this->emptyContact()) $calEvent->setContact($this->getContact());
        if (!$this->emptyExDate()) $calEvent->setExDate($this->getExDate());
        if (!$this->emptyRStatus()) $calEvent->setRStatus($this->getRStatus());
        if (!$this->emptyRelated()) $calEvent->setRelated($this->getRelated());
        if (!$this->emptyResources()) $calEvent->setResources($this->getResources());
        if (!$this->emptyRDate()) $calEvent->setRDate($this->getRDate());

        return $calEvent;
    }

    /**
     * get date of Creation of Event
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * set date of Creation of Event
     * @param array|DateTime|string $created_at
     * @return EventICS|bool
     */
    public function setCreatedAt(mixed $created_at): self
    {
        if (is_array($created_at)) {
            $fuseau = $created_at['TZID'];
            $created_at = $created_at['VALUE'];
            $created_at = $this->extractDateMutable($created_at, $fuseau);
        } elseif (is_string($created_at)) {
            $dateStart = $this->extractDateMutable($created_at, $this->getTimezone());
        } elseif (!is_a($created_at, 'DateTime')) {
            return false;
        }
        $this->created_at = $created_at;
        if ($this->emptyDateStart() && !empty($created_at->getTimezone())) {
            $this->setTimezone($created_at->getTimezone());
        }
        return $this;
    }

    /**
     * get date of Lsr Update of Event
     * @return DateTime
     */
    public function getLastupdatedAt(): DateTime
    {
        return $this->lastupdated_at;
    }

    /**
     * @return boolean
     */
    public function emptyLastupdatedAt(): bool
    {
        return empty($this->lastupdated_at);
    }

    /**
     * setDate of Last Update of Event
     * @param array|DateTime|string $lastupdated_at
     * @return EventICS|bool
     */
    public function setLastupdatedAt(mixed $lastupdated_at): mixed
    {
        if (is_array($lastupdated_at)) {
            $fuseau = $lastupdated_at['TZID'];
            $lastupdated_at = $lastupdated_at['VALUE'];
            $lastupdated_at = $this->extractDateMutable($lastupdated_at, $fuseau);
        } elseif (is_string($lastupdated_at)) {
            $dateStart = $this->extractDateMutable($lastupdated_at, $this->getTimezone());
        } elseif (!is_a($lastupdated_at, 'DateTime')) {
            return false;
        }
        $this->lastupdated_at = $lastupdated_at;
        if ($this->emptyDateStart() && !empty($lastupdated_at->getTimezone())) {
            $this->setTimezone($lastupdated_at->getTimezone());
        }
        return $this;
    }

    /**
     * get date when Start the Event
     * @return DateTime
     */
    public function getDateStart(): DateTime
    {
        return $this->dateStart;
    }

    /**
     * @return boolean
     */
    public function emptyDateStart(): bool
    {
        return empty($this->dateStart);        
    }

    /**
     * set date when Start the Event
     * @param array|DateTime|string $dateStart
     * @return EventICS|bool
     */
    public function setDateStart(mixed $dateStart): mixed
    {
        if (is_array($dateStart)) {
            $fuseau = $dateStart['TZID'];
            $dateStart = $dateStart['VALUE'];
            $dateStart = $this->extractDateMutable($dateStart, $fuseau);
        } elseif (is_string($dateStart)) {
            $dateStart = $this->extractDateMutable($dateStart, $this->getTimezone());
        } elseif (!is_a($dateStart, 'DateTime')) {
            return false;
        }
        $this->dateStart = $dateStart;
        if (!empty($dateStart->getTimezone())) {
            $timezoneTmp = $dateStart->getTimezone()->getName();
            $this->setTimezone($timezoneTmp);
        }
        return $this;
    }

    /**
     * get date when End the Event
     * @return DateTime
     */
    public function getDateEnd(): DateTime
    {
        return $this->dateEnd;
    }

    /**
     * @return boolean
     */
    public function emptyDateEnd(): bool
    {
        return empty($this->dateEnd);
    }

    /**
     * set date when End the Event
     * @param array|DateTime|string $dateEnd
     * @return EventICS|bool
     */
    public function setDateEnd(DateTime $dateEnd): self
    {
        if (is_array($dateEnd)) {
            $fuseau = $dateEnd['TZID'];
            $dateEnd = $dateEnd['VALUE'];
            $dateEnd = $this->extractDateMutable($dateEnd, $fuseau);
        } elseif (is_string($dateEnd)) {
            $dateEnd = $this->extractDateMutable($dateEnd, $this->getTimezone());
        } elseif (!is_a($dateEnd, 'DateTime')) {
            return false;
        }
        $this->dateEnd = $dateEnd;
        if ($this->emptyDateStart() && !empty($dateEnd->getTimezone())) {
            $this->setTimezone($dateEnd->getTimezone());
        }
       return $this;
    }

    /**
     * get the Object or Subject of the Event
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @return boolean
     */
    public function emptySubject(): bool
    {
        return empty($this->subject);
    }

    /**
     * set the Object or Subject of the Event
     *
     * @param string $subject
     * @return EventICS
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * get the Details of the Event
     * @return string|null
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @return boolean
     */
    public function emptyDetails(): bool
    {
        return empty($this->details);
    }

    /**
     * set the Deatils of th Event
     * @param string $details
     * @return EventICS
     */
    public function setDetails(string $details): self
    {
        $this->details = $details;
        return $this;
    }

    /**
     * get the Custom BackgroudColor associate at the Event
     * @return string|null
     */
    public function getBgColor(): ?string
    {
        return $this->bg_color;
    }

    /**
     * @return boolean
     */
    public function emptyBgColor(): bool
    {
        return empty($this->bg_color);
    }

    /**
     * set the Custom backgroundColor associate at the Event
     * @param string|null $bg_color
     * @return EventICS
     */
    public function setBgColor(?string $bg_color): self
    {
        $this->bg_color = $bg_color;
        return $this;
    }

    /**
     * get the Custom BorderColor associate at the Event
     * @return string|null
     */
    public function getBdColor(): ?string
    {
        return $this->bd_color;
    }

    /**
     * @return boolean
     */
    public function emptyBdColor(): bool
    {
        return empty($this->bd_color);
    }

    /**
     * set the Custom BorderColor associate at the Event
     * @param string|null $bd_color
     * @return EventICS
     */
    public function setBdColor(?string $bd_color): self
    {
        $this->bd_color = $bd_color;
        return $this;
    }

    /**
     * get the Custom TextColor associate at the Event
     * @return string|null
     */
    public function getTxColor(): ?string
    {
        return $this->tx_color;
    }

    /**
     * @return boolean
     */
    public function emptyTxColor(): bool
    {
        return empty($this->tx_color);
    }

    /**
     * set the Custom TextColor associate at the Event
     * @param string|null $tx_color
     * @return EventICS
     */
    public function setTxColor(?string $tx_color): self
    {
        $this->tx_color = $tx_color;
        return $this;
    }

    /**
     * get Allday
     * @return bool
     */
    public function isAllday(): bool
    {
        return $this->allday;
    }

    /**
     * set Allday
     * @param bool $allday
     * @return EventICS
     */
    public function setAllday(bool $allday): self
    {
        $this->allday = $allday;
        return $this;
    }

    /**
     * get Status of the Event
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * set Status of the Event
     * @param string $status
     * @return EventICS|bool
     */
    public function setStatus(string $status): mixed
    {
        if (StatusEnums::isValidVevent($status)) {
            $this->status = $status;
            return $this;
        }
        return false;
    }

    /**
     * get the Persons concerned by the Event
     * @return Collection<int, Attendee>|null
     */
    public function getAttendees(): ?Collection
    {
        return $this->attendees;
    }

    /**
     * @return boolean
     */
    public function emptyAttendees(): bool
    {
        return empty($this->attendees);
    }

    /**
     * add one Person concerned by the Event
     * @param Attendee $attendee
     * @return EventICS
     */
    public function addAttendee(Attendee $attendee)
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees[] = $attendee;
        }
        return $this;
    }

    /**
     * remove one Person concerned by the Event
     * @param Attendee $attendee
     * @return EventICS
     */
    public function removeAttendee(Attendee $attendee)
    {
        $this->attendees->removeElement($attendee);
        return $this;
    }

    /**
     * get the Unique Identifier associate at the Event
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @return boolean
     */
    public function emptyUid(): bool
    {
        return empty($this->uid);
    }

    /**
     * set the Unique Identifier associate at the Event
     * @param string $uid
     * @return EventICS
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * get the classification of the Event
     * @return string|null
     */
    public function getClassification(): ?string
    {
        return $this->classification;
    }

    /**
     * @return boolean
     */
    public function emptyClassification(): bool
    {
        return empty($this->classification);
    }

    /**
     * set the classification of the Event
     * @param string|null $classification
     * @return EventICS|bool
     */
    public function setClassification(string $classification): mixed
    {
        if (ClassificationEnums::isValid($classification)) {
            $this->classification = $classification;
            return $this;
        }
        return false;
    }

    /**
     * get the Location of the Event (object EventLocation)
     * @return EventLocation|null
     */
    public function getLocation(): ?EventLocation
    {
        return $this->location;
    }

    /**
     * @return boolean
     */
    public function emptyLocation(): bool
    {
        return empty($this->location);
    }

    /**
     * set the Location of the Event (object EventLocation)
     * @param EventLocation $location
     * @return EventICS
     */
    public function setLocation(EventLocation $location): self
    {
        $this->location = $location;
        return $this;
    }

    /**
     * get Timezone for all Dates of the Event
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @return bool
     */
    public function emptyTimezone(): bool
    {
        return empty($this->timezone);
    }

    /**
     * set Timezone for all Dates of the Event
     * @param string $timezone
     * @return EventICS
     */
    public function setTimezone(string $timezone): self
    {
        /** affectation du fuseau horaire si DateStart vide ou si vide */
        if (empty($this->getDateStart()) || empty($this->getTimezone())) {
            $this->timezone = $timezone;
        } 
        return $this;
    }

    private function calcEndDate(DateTime $dtStart, string $duration) : DateTime
    {
        return $dtStart->add(new DateInterval($duration));;
    }

    /**
     * get the Frequence of the Event
     * @return EventRepetition|null
     */
    public function getFrequence(): ?EventRepetition
    {
        return $this->frequence;
    }

    /**
     * @return boolean
     */
    public function emptyFrequence(): bool
    {
        return empty($this->frequence);
    }
    
    /**
     * Set the value of frequence
     * @param array $rrule
     * @param ?string $globalTimezone
     * @return EventICS|bool
     */
    public function setFrequence(mixed $rrule, string $globalTimezone = null): mixed
    {
        if (is_array($rrule)) {
            // traitement des répétitions de l'événement
            $frequence = new EventRepetition();
            
            $freq = $rrule['FREQ'];
            $periodSet = ["", 'SECONDLY', 'MINUTELY', 'HOURLY', 'DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY'];
            $frequence->setPeriod(array_search($freq, $periodSet));

            $interval = array_key_exists('INTERVAL', $rrule) ? (int)$rrule['INTERVAL'] : 1;
            $frequence->setInterval($interval);

            $untilDate = null;
            if (array_key_exists('UNTIL', $rrule)) {
                $untilDate = $this->extractDate($rrule['UNTIL'], $globalTimezone);
            }
            $frequence->setUntilDate($untilDate);

            if (array_key_exists('COUNT', $rrule)) $frequence->setCount((int)$rrule['COUNT']);

            $this->frequence = $frequence;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of organizer
     * @return Organizer|null
     */
    public function getOrganizer(): ?Organizer
    {
        return $this->organizer;
    }

    /**
     * @return bool
     */
    public function emptyOrganizer(): bool
    {
        return empty($this->organizer);
    }
    /**
     * Set the value of organizer
     * @param Organizer $organizer
     * @return EventICS
     */
    public function setOrganizer(Organizer $organizer): self
    {
        $this->organizer = $organizer;
        return $this;
    }

    /**
     * Get the value of alarms
     * get Persons, Contacts, Prospects, Customers
     * @return Collection|EventAlarm[]|null
     */
    public function getAlarms(): ?Collection
    {
        return $this->alarms;
    }

    /**
     * @return boolean
     */
    public function emptyAlarms(): bool
    {
        return empty($this->alarms);
    }

    /**
     * add one Alamr to the Event
     * @param EventAlarm $alarm
     * @return CalEvent
     */
    public function addAlarm(EventAlarm $alarm): self
    {
        if (!$this->alarms->contains($alarm)) {
            $this->alarms[] = $alarm;
        }
        return $this;
    }

    /**
     * remove one Alarm to the Event
     * @param EventAlarm $alarm
     * @return CalEvent
     */
    public function removeAlarm(EventAlarm $alarm): self
    {
        $this->alarms->removeElement($alarm);
        return $this;
    }

    /**
     * Set the value of alarms
     * @param Collection $alarms
     * @return self
     */
    public function setAlarms(Collection $alarms): self
    {
        $this->alarms = $alarms;

        return $this;
    }

    /**
     * Get the value of dtstamp
     * @return DateTime|null
     */
    public function getDtStamp(): ?DateTime
    {
        return $this->dtStamp;
    }

    /**
     * @return boolean
     */
    public function emptyDtStamp(): bool
    {
        return empty($this->dtStamp);
    }

    /**
     * Set the value of dtStamp
     * @param DateTime $dtStamp
     * @return self
     */
    public function setDtStamp(DateTime $dtStamp): self
    {
        $this->dtStamp = $dtStamp;
        return $this;
    }

    /**
     * Get the value of priority
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @return boolean
     */
    public function emptyPriority(): bool
    {
        return empty($this->priority);
    }

    /**
     * Set the value of priority
     * @param string $priority
     * @return self
     */
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get the value of seq
     * @return int|null
     */
    public function getSeq(): ?int
    {
        return $this->seq;
    }

    /**
     * @return boolean
     */
    public function emptySeq(): bool
    {
        return empty($this->seq);
    }

    /**
     * Set the value of seq
     * @param int $seq
     * @return self
     */
    public function setSeq(string $seq): self
    {
        $this->seq = $seq;
        return $this;
    }

    /**
     * Get the value of transp
     * @return string|null
     */
    public function getTransp(): ?string
    {
        return $this->transp;
    }

    /**
     * @return boolean
     */
    public function emptyTransp(): bool
    {
        return empty($this->transp);
    }

    /**
     * Set the value of transp
     * @param string $transp
     * @return self
     */
    public function setTransp(string $transp): self
    {
        $this->transp = $transp;
        return $this;
    }

    /**
     * Get the value of url
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return boolean
     */
    public function emptyUrl(): bool
    {
        return empty($this->url);
    }

    /**
     * Set the value of url
     * @param string $url
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get the value of recur_id
     * @return string|null
     */
    public function getRecurId(): ?string
    {
        return $this->recurId;
    }

    /**
     * @return boolean
     */
    public function emptyRecurId(): bool
    {
        return empty($this->recurId);
    }

    /**
     * Set the value of recurId
     * @param string $recurId
     * @return self
     */
    public function setRecurId(string $recurId): self
    {
        $this->recurId = $recurId;
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
     * @return boolean
     */
    public function emptyDuration(): bool
    {
        return empty($this->duration);
    }

    /**
     * Set the value of duration
     * @param string $duration
     * @return self
     */
    public function setDuration(string $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * get the Attachs of the Event
     * @return Collection<int, array>|null
     */
    public function getAttachs(): ?Collection
    {
        return $this->attachs;
    }

    /**
     * @return boolean
     */
    public function emptyAttachs(): bool
    {
        return empty($this->attachs);
    }

    /**
     * add 1 attach to the Attachs of the Event
     * @param array $attach
     * @return self
     */
    public function addAttach(array $attach): self
    {
        if (!$this->attachs->contains($attach)) {
            $this->attachs->add($attach);
        }
        return $this;
    }

    /**
     * remove 1 attach if exist in Attachs of the Event
     * @param array $attach
     * @return self|bool
     */
    public function removeAttach(array $attach): mixed
    {
        if ($this->attachs->removeElement($attach)) {
            return $this;
        }
        return false;
    }

    /**
     * get the Categories of the Event
     * @return Collection<int, string>|null
     */
    public function getCategories(): ?Collection
    {
        return $this->categories;
    }

    /**
     * @return boolean
     */
    public function emptyCategories(): bool
    {
        return empty($this->categories);
    }

    /**
     * add 1 category to the Categories of the Event
     * @param string $category
     * @return self
     */
    public function addCategory(string $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    /**
     * remove 1 category if exist in Categories of the Event
     * @param string $category
     * @return self|bool
     */
    public function removeCategory(string $category): mixed
    {
        if ($this->categories->removeElement($category)) {
            return $this;
        }
        return false;
    }

    /**
     * Get the value of contact
     * @return Contact|null
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @return boolean
     */
    public function emptyContact(): bool
    {
        return empty($this->contact);
    }

    /**
     * Set the value of contact
     * @param Contact $contact
     * @return self
     */
    public function setContact(Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * Get the value of ex_date
     * @return DateTime|null
     */
    public function getExDate(): ?DateTime
    {
        return $this->exDate;
    }

    /**
     * @return boolean
     */
    public function emptyExDate(): bool
    {
        return empty($this->exDate);
    }

    /**
     * Set the value of exDate
     * @param DateTime $exDate
     * @return self
     */
    public function setExDate(DateTime $exDate): self
    {
        $this->exDate = $exDate;
        return $this;
    }

    /**
     * Get the value of rStatus
     * @return string|null
     */
    public function getRStatus(): ?string
    {
        return $this->rStatus;
    }

    /**
     * @return boolean
     */
    public function emptyRStatus(): bool
    {
        return empty($this->rStatus);
    }

    /**
     * Set the value of rStatus
     * @param string $rStatus
     * @return self
     */
    public function setRStatus(string $rStatus): self
    {
        $this->rStatus = $rStatus;
        return $this;
    }

    /**
     * Get the value of related
     * @return string |null
     */
    public function getRelated(): ?string
    {
        return $this->related;
    }

    /**
     * @return boolean
     */
    public function emptyRelated(): bool
    {
        return empty($this->related);
    }

    /**
     * Set the value of related
     * @param string $related
     * @return self
     */
    public function setRelated(string $related): self
    {
        $this->related = $related;
        return $this;
    }

    /**
     * Get the value of resources
     * @return string|null
     */
    public function getResources(): ?string
    {
        return $this->resources;
    }

    /**
     * @return boolean
     */
    public function emptyResources(): bool
    {
        return empty($this->resources);
    }

    /**
     * Set the value of resources
     * @param string $resources
     * @return self
     */
    public function setResources(string $resources): self
    {
        $this->resources = $resources;
        return $this;
    }

    /**
     * Get the value of rDate
     * @return DateTime|null
     */
    public function getRDate(): ?DateTime
    {
        return $this->rDate;
    }

    /**
     * @return boolean
     */
    public function emptyRDate(): bool
    {
        return empty($this->rDate);
    }

    /**
     * Set the value of rDate
     * @param DateTime $rDate
     * @return self
     */
    public function setRDate(DateTime $rDate): self
    {
        $this->rDate = $rDate;
        return $this;
    }
}
