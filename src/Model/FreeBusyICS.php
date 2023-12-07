<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalFreeBusy;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class FreeBusyICS
{
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
    private ?string             $rStatus = null;        //
    
    public function __construct(EntityManagerInterface $entityManager, CalFreeBusy $calFreeBusy = null)
    {
        $this->entityManager = $entityManager;

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
        if (!$calFreeBusy->emptyRStatus()) $this->setRStatus($calFreeBusy->getRStatus());
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
     * Get the value of rStatus
     * @return string|null
     */
    public function getRStatus(): ?string
    {
        return $this->rStatus;
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
}