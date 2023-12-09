<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalJournal;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Enum\ClassificationEnums;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Traits\Model\ExtractDateTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatAttendeeTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatContactTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatOrganizerTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatRRuleTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class JournalICS
{
    use ExtractDateTrait;
    use FormatAttendeeTrait;
    use FormatOrganizerTrait;
    use FormatContactTrait;
    use FormatRRuleTrait;

    private EntityManagerInterface $entityManager;

    private string              $uid;                   //
    private DateTime            $dtStamp;               //

    private ?string             $classification = null; //
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

        $this->setClassification(ClassificationEnums::Public->_toString());
        $this->setStatus(StatusEnums::NeedsAction->_toString());
        $this->attachs = new ArrayCollection();
        $this->attendees = new ArrayCollection();

        if ($calJourn) {
            $this->setUid($calJourn->getUid());
            $this->setDtStamp($calJourn->getDtStamp());

            $this->setClassification($calJourn->getClassification());
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
    }

    public function buildFromArray(array $calArray, string $globalFuseau = null): JournalICS
    {
        /** initialisatio du fuseau horaire local au global */
        $globalTimezone = $globalTimezone ?? date_default_timezone_get();

        $this->setUid($calArray['UID']);
        $this->setDtStamp($this->extractDateMutable($calArray["DTSTAMP"], $globalFuseau));

        if (array_key_exists("CLASS", $calArray)) $this->setClassification($calArray["CLASS"]);
        if (array_key_exists("CREATED", $calArray)) {
            $created = $this->extractDateMutable($calArray['CREATED'], $globalTimezone);
            $this->setCreated($created);
        }
        if (array_key_exists("DTSTART", $calArray)){
            $dtStart = $this->extractDateMutable($calArray['DTSTART'], $globalTimezone);
            $this->setDtStart($dtStart);
        }

        if (array_key_exists("LAST-MOD", $calArray)){
            $lastModified = $this->extractDateMutable($calArray['LAST-MOD'], $globalTimezone);
            $this->setLastMod($lastModified);
        }

        $organizer = array_key_exists('ORGANIZER', $calArray) ? $calArray['ORGANIZER'] : [];
        if ($organizer) {
            $taskOrganizer = $this->formatOrganizer($organizer);
            $this->setOrganizer($taskOrganizer);
        }

        if (array_key_exists("RECURID", $calArray)) $this->setRecurId($calArray["RECURID"]);
        if (array_key_exists("SEQ", $calArray)) $this->setSeq((int) $calArray["seq"]);
        if (array_key_exists("STATUS", $calArray)) $this->setStatus($calArray["STATUS"]);
        if (array_key_exists("SUMMARY", $calArray)) $this->setSummary($calArray["SUMMARY"]);
        if (array_key_exists("URL", $calArray)) $this->setUrl($calArray["URL"]);

        /** -> intégration de la règle de répétition si présente */
        $rrule = array_key_exists('RRULE', $calArray) ? $calArray['RRULE'] : [];
        if ($rrule) {
            $rruleItem = $this->formatRRule($rrule);
            $this->setRRule($rruleItem);
        }

        $attachs = array_key_exists('ATTACH', $calArray) ? $calArray['ATTACH'] : null;
        if ($attachs) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($attachs as $attach) {
                $this->addAttach($attach);
            }
        }
        $attendees = array_key_exists('ATTENDEE', $calArray) ? $calArray['ATTENDEE'] : null;
        if ($attendees) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($attendees as $attendee) {
                $this->addAttendee($this->formatAttendee($attendee));
            }
        }
        $categories = array_key_exists('CATEGORY', $calArray) ? $calArray['CATEGORY'] : null;
        if ($categories) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($categories as $category) {
                $this->addCategory($category);
            }
        }

        if (array_key_exists("COMMENT", $calArray)) $this->setComment($calArray["COMMENT"]);

        $contact = array_key_exists('CONTACT', $calArray) ? $calArray['CONTACT'] : [];
        if ($contact) {
            $fbContact = $this->formatContact($contact);
            $this->setContact($fbContact);
        }
        if (array_key_exists("DESCRIPTION", $calArray)) $this->setDescription($calArray["DESCRIPTION"]);

        $exDates = array_key_exists('EXDATE', $calArray) ? $calArray['EXDATE'] : null;
        if ($exDates) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($exDates as $exDate) {
                $this->addExDate($exDate);
            }
        }
        if (array_key_exists("RELATED", $calArray)) $this->setRelated($calArray["RELATED"]);
        $rDates = array_key_exists('RDATES', $calArray) ? $calArray['RDATES'] : null;
        if ($rDates) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($rDates as $rDate) {
                $this->addRDate($rDate);
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

    public function toCalJournal(CalJournal $calJourn = null): CalJournal
    {
        if (!$calJourn) $calJourn = new CalJournal();

        $calJourn->setUid($this->getUid());
        $calJourn->setDtStamp($this->getDtStamp());
        if (!$this->emptyClassification()) $calJourn->setClassification($this->getClassification());
        if (!$this->emptyCreated()) $calJourn->setCreated($this->getCreated());
        if (!$this->emptyDtStart()) $calJourn->setDtStart($this->getDtStart());
        if (!$this->emptyLastMod()) $calJourn->setLastModified($this->getlastmod());
        if (!$this->emptyOrganizer()) $calJourn->setOrganizer($this->getOrganizer());
        if (!$this->emptyRecurId()) $calJourn->setRecurId($this->getRecurId());
        $calJourn->setSeq($this->getSeq());
        $calJourn->setStatus($this->getStatus());
        if (!$this->emptySummary()) $calJourn->setSummary($this->getSummary());
        if (!$this->emptyUrl()) $calJourn->setUrl($this->getUrl());
        if (!$this->emptyRRule()) $calJourn->setRRule($this->getRRule());
        if (!$this->emptyAttachs()) {
            foreach ($this->getAttachs() as $attach) {
                $calJourn->addAttach($attach);
            }
        }
        if (!$this->emptyAttendees()) {
            foreach ($this->getAttendees() as $attendee) {
                $calJourn->addAttendee($attendee);
            }
        }
        if (!$this->emptyCategories()) {
            foreach ($this->getCategories() as $category) {
                $calJourn->addCategory($category);
            }
        }
        if (!$this->emptyComment()) $calJourn->setComment($this->getComment());
        if (!$this->emptyContact()) $calJourn->setContact($this->getContact());
        if (!$this->emptyDescription()) $calJourn->setDescription($this->getDescription());
        if (!$this->emptyExDates()) {
            foreach ($this->getExDates() as $exDate) {
                $calJourn->addExDate($exDate);
            }
        }
        if (!$this->emptyRelated()) $calJourn->setRelated($this->getRelated());
        if (!$this->emptyRDates()) {
            foreach ($this->getRDates() as $rDate) {
                $calJourn->addRDate($rDate);
            }
        }
        if (!$this->emptyRStatus()) {
            foreach ($this->getRStatus() as $rStatus) {
                $calJourn->addRStatus($rStatus);
            }
        }

        return $calJourn;
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
     * Get the value of classification
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
     * set the value of classification
     * @param string $classification
     * @return self|bool
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
     * Get the value of created
     * @return DateTime|null
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * @return boolean
     */
    public function emptyCreated(): bool
    {
        return empty($this->created);
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
     * Get the value of lastModified
     * @return DateTime|null
     */
    public function getLastMod(): ?DateTime
    {
        return $this->lastMod;
    }

    /**
     * @return boolean
     */
    public function emptyLastMod(): bool
    {
        return empty($this->lastMod);
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
     * Get the value of recurId
     * @return TaskRecurrenceId|null
     */
    public function getRecurId(): ?TaskRecurrenceId
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
     * @return boolean
     */
    public function emptySummary(): bool
    {
        return empty($this->summary);
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
     * @return boolean
     */
    public function emptyRRule(): bool
    {
        return empty($this->rrule);
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
     * @return boolean
     */
    public function emptyAttachs(): bool
    {
        return empty($this->attachs);
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
     * @return boolean
     */
    public function emptyCategories(): bool
    {
        return empty($this->categories);
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
     * Get the value of description
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function emptyDescription(): bool
    {
        return empty($this->description);
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
     * @return boolean
     */
    public function emptyExDates(): bool
    {
        return empty($this->exDates);
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
     * get the Recurrence-DateTimes of the Journal
     * @return Collection<int, DateTime>|null
     */
    public function getRDates(): ?Collection
    {
        return $this->rDates;
    }

    /**
     * @return boolean
     */
    public function emptyRDates(): bool
    {
        return empty($this->rDates);
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
     * @return boolean
     */
    public function emptyRStatus(): bool
    {
        return empty($this->rStatus);
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