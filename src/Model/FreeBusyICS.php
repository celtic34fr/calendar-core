<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalFreeBusy;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Traits\Model\ExtractDateTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatAttendeeTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatContactTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatOrganizerTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class FreeBusyICS
{
    use ExtractDateTrait;
    use FormatAttendeeTrait;
    use FormatOrganizerTrait;
    use FormatContactTrait;
    
    private EntityManagerInterface $entityManager;

    private string              $uid;                   //
    private DateTime            $dtStamp;               //

    private ?Contact            $contact = null;        //
    private ?DateTime           $dtStart = null;        //
    private ?DateTime           $dtEnd = null;          //
    private ?Organizer          $organizer = null;      //
    private ?string             $url = null;            //

    private ?Collection         $attendees = null;      //
    private ?string             $comment = null;        //
    private ?array              $freesBusies = null;    //
    private ?array              $rStatus = null;        // *
    
    public function __construct(EntityManagerInterface $entityManager, CalFreeBusy $calFreeBusy = null)
    {

        $this->entityManager = $entityManager;

        $this->attendees = new ArrayCollection();

        $this->setUid($calFreeBusy->getUid());
        $this->setDtStamp($calFreeBusy->getDtStamp());

        if (!$calFreeBusy->emptyContact()) $this->setContact($calFreeBusy->getContact());
        if (!$calFreeBusy->emptyDtStart()) $this->setDtStart($calFreeBusy->getDtStart());
        if (!$calFreeBusy->emptyDtEnd()) $this->setDtEnd($calFreeBusy->getDtEnd());
        if (!$calFreeBusy->emptyOrganizer()) $this->setOrganizer($calFreeBusy->getOrganizer());
        if (!$calFreeBusy->emptyUrl()) $this->setUrl($calFreeBusy->getUrl());

        foreach ($calFreeBusy->getAttendees() as $attendee) {
            $this->addAttendee($attendee);
        }
        if (!$calFreeBusy->emptyComment()) $this->setComment($calFreeBusy->getComment());
        if (!$calFreeBusy->emptyFreesBusies()) {
            foreach ($calFreeBusy->getFreesBusies() as $value) {
                $this->addFreeBusy($value);
            }
        }
        if (!$calFreeBusy->emptyRStatus()) {
            foreach ($calFreeBusy->getRStatus() as $rStatus) {
                $this->addRStatus($rStatus);
            }
        }
}

    public function buildFromArray(array $calArray, string $globalFuseau = null): self
    {
        /** initialisatio du fuseau horaire local au global */
        $globalTimezone = $globalTimezone ?? date_default_timezone_get();

        $this->setUid($calArray['UID']);
        $this->setDtStamp($calArray["DTSTAMP"]);

        $contact = array_key_exists('CONTACT', $calArray) ? $calArray['CONTACT'] : [];
        if ($contact) {
            $fbContact = $this->formatContact($contact);
            $this->setContact($fbContact);
        }

        if (array_key_exists("DTSTART", $calArray)){
            $dtStart = $this->extractDateMutable($calArray['DTSTART'], $globalTimezone);
            $this->setDtStart($dtStart);
        }
        if (array_key_exists("DTEND", $calArray)){
            $dtEnd = $this->extractDateMutable($calArray['DTEND'], $globalTimezone);
            $this->setDtEnd($dtEnd);
        }


        $organizer = array_key_exists('ORGANIZER', $calArray) ? $calArray['ORGANIZER'] : [];
        if ($organizer) {
            $taskOrganizer = $this->formatOrganizer($organizer);
            $this->setOrganizer($taskOrganizer);
        }

        if (array_key_exists("URL", $calArray)) $this->setUrl($calArray["URL"]);

        $attendees = array_key_exists('ATTENDEE', $calArray) ? $calArray['ATTENDEE'] : null;
        if ($attendees) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($attendees as $attendee) {
                $this->addAttendee($this->formatAttendee($attendee));
            }
        }
        if (array_key_exists("COMMENT", $calArray)) $this->setComment($calArray["COMMENT"]);

        $freesBusies = array_key_exists('FREEBUSY', $calArray) ? $calArray['FREEBUSY'] : null;
        if ($freesBusies) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($freesBusies as $freebusy) {
                $this->addFreeBusy($freebusy);
            }
        }
        $rStatus = array_key_exists('RSTATUS', $calArray) ? $calArray['RSTATUS'] : null;
        if ($rStatus) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($rStatus as $item) {
                $this->addRStatus($item);
            }
        }

        return $this;
    }

    public function toCalFreeBusy(CalFreeBusy $calFreeBusy = null): CalFreeBusy
    {
        if (!$calFreeBusy) $calFreeBusy = new CalFreeBusy();

        $calFreeBusy->setUid($this->getUid());
        $calFreeBusy->setDtStamp($this->getDtStamp());
        if (!$this->emptyContact()) $calFreeBusy->setContact($this->getContact());
        if (!$this->emptyDtStart()) $calFreeBusy->setDtStart($this->getDtStart());
        if (!$this->emptyDtEnd()) $calFreeBusy->setDtEnd($this->getDtEnd());
        if (!$this->emptyOrganizer()) $calFreeBusy->setOrganizer($this->getOrganizer());
        if (!$this->emptyUrl()) $calFreeBusy->setUrl($this->getUrl());
        if (!$this->emptyAttendees()) {
            foreach ($this->getAttendees() as $attendee) {
                $calFreeBusy->addAttendee($attendee);
            }
        }
        if (!$this->emptyComment()) $calFreeBusy->setComment($this->getComment());
        if (!$this->emptyFreesBusies()) {
            foreach ($this->getFreesBusies() as $freebusy) {
                $calFreeBusy->addFreeBusy($freebusy);
            }
        }
        if (!$this->emptyRStatus()) {
            foreach ($this->getRStatus() as $rStatus) {
                $calTask->addRStatus($rStatus);
            }
        }
        
        return $calFreeBusy;
    }


    /**
     * Get the value of uid
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Set the value of uid
     * @param string $uid
     * @return self
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get the value of dtStamp
     * @return DateTime
     */
    public function getDtStamp(): DateTime
    {
        return $this->dtStamp;
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
    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * Get the value of dtStart
     * @return DateTime|null
     */
    public function getDtStart(): ?DateTime
    {
        return $this->dtStart;
    }

    /**
     * @return boolean
     */
    public function emptyDtStart(): bool
    {
        return empty($this->dtStart);
    }

    /**
     * Set the value of dtStart
     * @param DateTime $dtStart
     * @return self
     */
    public function setDtStart(DateTime $dtStart): self
    {
        $this->dtStart = $dtStart;
        return $this;
    }

    /**
     * Get the value of dtEnd
     * @return DateTime|null
     */
    public function getDtEnd(): ?DateTime
    {
        return $this->dtEnd;
    }

    /**
     * @return boolean
     */
    public function emptyDtEnd(): bool
    {
        return empty($this->dtEnd);
    }

    /**
     * Set the value of dtEnd
     * @param DateTime $dtEnd
     * @return self
     */
    public function setDtEnd(DateTime $dtEnd): self
    {
        $this->dtEnd = $dtEnd;
        return $this;
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
     * @return boolean
     */
    public function emptyOrganizer(): bool
    {
        return empty($this->organizer);
    }

    /**
     * Set the value of organizer
     * @param Organizer $organizer
     * @return self
     */
    public function setOrganizer(Organizer $organizer): self
    {
        $this->organizer = $organizer;
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
     * @return self
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
     * @return self
     */
    public function removeAttendee(Attendee $attendee)
    {
        $this->attendees->removeElement($attendee);
        return $this;
    }

    /**
     * Get the value of comment
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return boolean
     */
    public function emptyComment(): bool
    {
        return empty($this->comment);
    }

    /**
     * Set the value of comment
     * @param string $comment
     * @return self
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * get the FreesBusies of the CalFressBusies
     * @return array<int, string>|null
     */
    public function getFreesBusies(): ?Collection
    {
        return $this->freesBusies;
    }

    /**
     * @return boolean
     */
    public function emptyFreesBusies(): bool
    {
        return empty($this->freesBusies);
    }

    /**
     * add 1 Freebusy to the FreesBusies of the CalFreesBusies
     * @param string $freebusy
     * @return self|bool
     */
    public function addFreeBusy(string $freebusy): self
    {
        if (!in_array($freebusy, $this->freesBusies)) {
            $this->freesBusies[] = $freebusy;
            return $this;
        }
        return false;
    }

    /**
     * remove 1 freebusy if exist in FreesBusies of the calFreeBusy
     * @param string $freebusy
     * @return self|bool
     */
    public function removeFreeBusy(string $freebusy): mixed
    {
        if (in_array($freebusy, $this->freesBusies)) {
            $idx = array_search($freebusy, $this->freesBusies);
            unset($this->freesBusies[$idx]);
            return $this;
        }
        return false;
    }

    /**
     * get the Request Status of the Task
     * @return Collection<int, DateTime>|null
     */
    public function getRStatus(): ?Collection
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
     * add 1 request-status to the Request Status of the Task
     * @param DateTime $rStatus
     * @return self
     */
    public function addRStatus(DateTime $rStatus): self
    {
        if (!in_array($rStatus, $this->rStatus)) {
            $this->rStatus[] = $rStatus;
        }
        return $this;
    }

    /**
     * remove 1 request-status if exist in Request Status of the Task
     * @param DateTime $rStatus
     * @return self|bool
     */
    public function removeRStatus(DateTime $rStatus): mixed
    {
        if (in_array($rStatus, $this->rStatus)) {
            $idx = array_search($rStatus, $this->rStatus);
            unset($this->rStatus[$idx]);
            return $this;
        }
        return false;
    }
}