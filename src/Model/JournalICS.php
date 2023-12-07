<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalJournal;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Enum\ClassesEnums;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class JournalICS
{
    private EntityManagerInterface $entityManager;

    private string              $uid;                   //
    private DateTime            $dtStamp;               //

    private ?array              $classes = null;        //
    private ?DateTime           $created = null;        //
    private ?DateTime           $dtStart = null;        //
    private ?DateTime           $lastMod = null;        //
    private ?Organizer          $organizer = null;      //
    private ?TaskRecurrenceId   $recurId = null;        //
    private int                 $seq = 0;               //
    private string              $status;                //
    private ?string             $summary = null;        //
    private ?string             $url = null;            //

    private ?EventRepetition    $rrule = null;          //

    private ?Collection         $attachs = null;        //
    private ?Collection         $attendees = null;      //
    private ?array              $categories = null;     //
    private ?string             $comment = null;        //
    private ?Contact            $contact = null;        //
    private ?string             $description = null;    //
    private ?array              $exDates = null;        //
    private ?string             $related = null;        //
    private ?array              $rDates = null;         //
    private ?array              $rStatus = null;        //

    public function __construct(EntityManagerInterface $entityManager, CalJournal $calJourn = null)
    {
        $this->entityManager = $entityManager;

        $this->setClasses(ClassesEnums::Public->_toString());
        $this->setStatus(StatusEnums::NeedsAction->_toString());
        $this->attachs = new ArrayCollection();
        $this->attendees = new ArrayCollection();

        $this->setUid($calJourn->getUid());
        $this->setDtStamp($calJourn->getDtStamp());

        $this->setClasses($calJourn->getClasses());
        if (!$calJourn->emptyCreated()) $this->setCreated($calJourn->getCreated());
        if (!$calJourn->emptyDtStart()) $this->setDtStamp($calJourn->getDtStart());
        if (!$calJourn->emptyLastModified()) $this->setLastMod($calJourn->getLastModified());
        if (!$calJourn->emptyOrganizer()) $this->setOrganizer($calJourn->getOrganizer());
        if (!$calJourn->emptyRecurId()) $this->setRecurId($calJourn->getRecurId());
        $this->setSeq($calJourn->getSeq());
        $this->setStatus($calJourn->getStatus());
        if (!$calJourn->emptySummary()) $this->setSummary($calJourn->getSummary());
        if (!$calJourn->emptyUrl()) $this->setUrl($calJourn->getUrl());

        if (!$calJourn->emptyRRule()) $this->setRRule($calJourn->getRRule());

        if (!$calJourn->emptyAttachs()) {
            foreach ($calJourn->getAttachs() as $attach) {
                $this->addAttach($attach);
            }
        }
        if (!$calJourn->emptyAttendees()) {
            foreach ($calJourn->getAttendees() as $attendee) {
                $this->addAttendee($attendee);
            }
        }
        if (!$calJourn->emptyCategories()) {
            foreach ($calJourn->getCategories() as $category) {
                $this->addCategory($category);
            }
        }
        if (!$calJourn->emptyComment()) $this->setComment($calJourn->getComment());
        if (!$calJourn->emptyContact()) $this->setContact($calJourn->getContact());
        if (!$calJourn->emptyDescription()) $this->setDescription($calJourn->getDescription());
        if (!$calJourn->emptyExDates()) {
            foreach ($calJourn->getExDates() as $exDate) {
                $this->addExDate($exDate);
            }
        }
        if (!$calJourn->emptyRelated()) $this->setRelated($calJourn->getRelated());
        if (!$calJourn->emptyRDates()) {
            foreach ($calJourn->getRDates() as $rDate) {
                $this->addRDate($rDate);
            }
        }
        if (!$calJourn->emptyRStatus()) {
            foreach ($calJourn->getRStatus() as $rStatus) {
                $this->addRStatus($rStatus);
            }
        }
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
     * Get the value of classes
     * @return string|null
     */
    public function getClasses(): ?string
    {
        return $this->classes;
    }

    /**
     * set the value of classes
     * @param string $classes
     * @return self|bool
     */
    public function setClasses(string $classes): mixed
    {
        if (ClassesEnums::isValid($classes)) {
            $this->classes = $classes;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of created
     * @return DateTime|null
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Set the value of created
     * @param DateTime|null
     */
    public function setCreated(DateTime $created): self
    {
        $this->created = $created;
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
     * Get the value of lastModified
     * @return DateTime|null
     */
    public function getLastMod(): ?DateTime
    {
        return $this->lastMod;
    }

    /**
     * Set the value of dtStart
     */
    public function setLastMod(?DateTime $lastMod): self
    {
        $this->lastMod = $lastMod;
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
     * Get the value of recurId
     * @return TaskRecurrenceId|null
     */
    public function getRecurId(): ?TaskRecurrenceId
    {
        return $this->recurId;
    }

    /**
     * Set the value of recurId
     * @param TaskRecurrenceId $recurId
     * @return self
     */
    public function setRecurId(?TaskRecurrenceId $recurId): self
    {
        $this->recurId = $recurId;
        return $this;
    }

    /**
     * Get the value of seq
     * @return int
     */
    public function getSeq(): int
    {
        return $this->seq;
    }

    /**
     * Set the value of seq
     * @param int $seq
     * @return self
     */
    public function setSeq(int $seq): self
    {
        $this->seq = $seq;

        return $this;
    }

    /**
     * Get the value of status
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     * @param string $status
     * @return self|bool
     */
    public function setStatus(string $status): mixed
    {
        if (StatusEnums::isValidVtodo($status)) {
            $this->status = $status;
            return $this;
        }
        return false;
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
    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;
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
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of rrule
     * @return EventRepetition|null
     */
    public function getRRule(): ?EventRepetition
    {
        return $this->rrule;
    }

    /**
     * Set the value of rrule
     * @param EventRepetition $rrule
     * @return self
     */
    public function setRRule(?EventRepetition $rrule): self
    {
        $this->rrule = $rrule;
        return $this;
    }

    /**
     * get the Attachs concerned by the Journal
     * @return Collection<int, array>|null
     */
    public function getAttachs(): ?Collection
    {
        return $this->attachs;
    }

    /**
     * add one Attach concerned by the Journal
     * @param array $attach
     * @return self
     */
    public function addAttach(array $attach)
    {
        if (!$this->attachs->contains($attach)) {
            $this->attachs->add($attach);
        }
        return $this;
    }

    /**
     * remove one Attach concerned by the Journal
     * @param array $attach
     * @return self
     */
    public function removeAttach(array $attach)
    {
        $this->attachs->removeElement($attach);
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
            $this->attendees->add($attendee);
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
     * get the Categories of the Journal
     * @return Collection<int, string>|null
     */
    public function getCategories(): ?Collection
    {
        return $this->categories;
    }

    /**
     * add 1 category to the Categories of the Journal
     * @param string $category
     * @return self
     */
    public function addCategory(string $category): self
    {
        if (!in_array($category, $this->categories)) {
            $this->categories[] = $category;
        }
        return $this;
    }

    /**
     * remove 1 category if exist in Categories of the Journal
     * @param string $category
     * @return self|bool
     */
    public function removeCategory(string $category): mixed
    {
        if (in_array($category, $this->categories)) {
            $idx = array_search($category, $this->categories);
            unset($this->categories[$idx]);
            return $this;
        }
        return false;
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
    public function setContact(Contact $contact): self
    {
        $this->contact = $contact;
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
     * get the Exception-Date of the Journal
     * @return Collection<int, DateTime>|null
     */
    public function getExDates(): ?Collection
    {
        return $this->exDates;
    }

    /**
     * add 1 exception-date to the ExceptionDates of the Journal
     * @param DateTime $exDate
     * @return self
     */
    public function addExDate(DateTime $exDate): self
    {
        if (!in_array($exDate, $this->exDates)) {
            $this->exDates[] = $exDate;
        }
        return $this;
    }

    /**
     * remove 1 exception-date if exist in ExceptionDates of the Journal
     * @param DateTime $exDate
     * @return self|bool
     */
    public function removeExDate(DateTime $exDate): mixed
    {
        if (in_array($exDate, $this->exDates)) {
            $idx = array_search($exDate, $this->exDates);
            unset($this->exDates[$idx]);
            return $this;
        }
        return false;
    }

    /**
     * Get the value of related
     * @return string|null
     */
    public function getRelated(): ?string
    {
        return $this->related;
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
     * get the Recurrence-DateTimes of the Journal
     * @return Collection<int, DateTime>|null
     */
    public function getRDates(): ?Collection
    {
        return $this->rDates;
    }

    /**
     * add 1 recurrence-dateTime to the Recurrence-DateTimes of the Journal
     * @param DateTime $rDate
     * @return self
     */
    public function addRDate(DateTime $rDate): self
    {
        if (!in_array($rDate, $this->rDates)) {
            $this->rDates[] = $rDate;
        }
        return $this;
    }

    /**
     * remove 1 recurrence-dateTime if exist in Recurrence-DateTimes of the Journal
     * @param DateTime $rDate
     * @return self|bool
     */
    public function removeRDate(DateTime $rDate): mixed
    {
        if (in_array($rDate, $this->rDates)) {
            $idx = array_search($rDate, $this->rDates);
            unset($this->rDates[$idx]);
            return $this;
        }
        return false;
    }

    /**
     * get the Request Status of the Journal
     * @return Collection<int, DateTime>|null
     */
    public function getRStatus(): ?Collection
    {
        return $this->rStatus;
    }

    /**
     * add 1 request-status to the Request Status of the Journal
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
     * remove 1 request-status if exist in Request Status of the Journal
     * @param DateTime $r_status
     * @return self|bool
     */
    public function removeRStatus(DateTime $r_status): mixed
    {
        if (in_array($r_status, $this->r_status)) {
            $idx = array_search($r_status, $this->r_status);
            unset($this->r_status[$idx]);
            return $this;
        }
        return false;
    }
}