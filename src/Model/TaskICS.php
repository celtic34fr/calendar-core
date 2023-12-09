<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalTask;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Enum\ClassificationEnums;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Model\EventLocation;
use Celtic34fr\CalendarCore\Model\TaskRecurrenceId;
use Celtic34fr\CalendarCore\Traits\Model\ExtractDateTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatAttendeeTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatContactTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatLocationTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatOrganizerTrait;
use Celtic34fr\CalendarCore\Traits\Model\FormatRRuleTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class TaskICS
{
    use ExtractDateTrait;
    use FormatAttendeeTrait;
    use FormatOrganizerTrait;
    use FormatContactTrait;
    use FormatRRuleTrait;
    use FormatLocationTrait;

    private EntityManagerInterface $entityManager;

    private string              $uid;                   // *
    private DateTime            $dtStamp;               // *

    private ?string             $classification = null; // *
    private ?DateTime           $completed = null;      // *
    private ?DateTime           $created = null;        // *
    private ?string             $description = null;    // *
    private ?DateTime           $dtStart = null;        // *
    private ?EventLocation      $location = null;       // *
    private ?DateTime           $lastModified = null;   // *
    private ?Organizer          $organizer = null;      // *
    private int                 $percentComplete = 0;   // *
    private int                 $priority = 0;          // *
    private ?TaskRecurrenceId   $recurId = null;        // *
    private int                 $seq = 0;               // *
    private string              $status;                // *
    private ?string             $summary = null;        // *
    
    private ?Collection         $attachs = null;        // *
    private ?Collection         $attendees = null;      // *
    private ?array              $categories = null;     // *
    private ?string             $comment = null;        // *
    private ?Contact            $contact = null;        // *
    private ?array              $exDates = null;        // *
    private ?array              $rStatus = null;        // *
    private ?string             $related = null;        // *
    private ?array              $resources = null;      // *
    private ?array              $rDates = null;         // *

    private ?string             $url = null;            //
    private ?EventRepetition    $rrule = null;          //
    private ?string             $due = null;            //
    private ?string             $duration = null;       //

    public function __construct(EntityManagerInterface $entityManager, CalTask $calTask = null)
    {
        $this->entityManager = $entityManager;

        $this->setClassification(ClassificationEnums::Public->_toString());
        $this->setStatus(StatusEnums::NeedsAction->_toString());
        $this->attachs = new ArrayCollection();
        $this->attendees = new ArrayCollection();

        if ($calTask) {
            $this->setUid($calTask->getUid());
            $this->setDtStamp($calTask->getDtStamp());

            if (!$calTask->emptyClassification()) $this->setClassification($calTask->getClassication());
            if (!$calTask->emptyCompleted()) $this->setCompleted($calTask->getCompleted());
            if (!$calTask->emptyCreated()) $this->setCreated($calTask->getCreated());
            if (!$calTask->emptyDescription()) $this->setDescription($calTask->getDescription());
            if (!$calTask->emptyDtStart()) $this->setDtStamp($calTask->getDtStamp());
            if (!$calTask->emptyLocation()) $this->setLocation($calTask->getLocation());
            if (!$calTask->emptyLastModified()) $this->setLastModified($calTask->getLastModified());
            if (!$calTask->emptyOrganizer()) $this->setOrganizer($calTask->getOrganizer());
            if ($calTask->getPercentComplete() > -1 && $calTask->getPercentComplete() < 101 ) {
                $this->setPercentComplete($calTask->getPercentComplete());
            }
            if ($calTask->getPriority() > -1 && $calTask->getPriority() < 10) {
                $this->setPriority($calTask->getPriority());
            }
            if (!$calTask->emptyRecurId()) $this->setRecurId($calTask->getRecurId());
            if (is_numeric($calTask->getSeq()) && $calTask->getSeq() > -1) {
                $this->setSeq($calTask->getSeq());
            }
            if (!$calTask->getStatus()) $this->setStatus($calTask->getStatus());

            if (!$calTask->emptyAttendees()) {
                foreach ($calTask->getAttendees() as $attendee) {
                    $this->addAttendee($attendee);
                }
            }
            if (!$calTask->emptyCategories()) {
                foreach ($calTask->getCategories() as $category) {
                    $this->addCategory($category);
                }
            }

            if (!$calTask->emptyRRule()) $this->setRRule($calTask->getRRule());
            if (!$calTask->emptyDue()) $this->setDue($calTask->getDue());
            if (!$calTask->emptyDuration()) $this->setDuration($calTask->getDuration());
            if (!$calTask->emptyAttachs()) {
                foreach ($calTask->getAttachs() as $attach) {
                    $this->addAttach($attach);
                }
            }
            if (!$calTask->emptyComment()) $this->setComment($calTask->getComment());
            if (!$calTask->emptyContact()) $this->setContact($calTask->getContact());
            if (!$calTask->emptyExDates()) {
                foreach ($calTask->getExDates() as $exDate) {
                    $this->addExDate($exDate);
                }
            }
            if (!$calTask->emptyRStatus()) {
                foreach ($calTask->getRStatus() as $rStatus) {
                    $this->addRStatus($rStatus);
                }
            }
            if (!$calTask->emptyRelated()) $this->setRelated($calTask->getRelated());
            if (!$calTask->emptyResources()) {
                foreach ($calTask->getResources() as $resource) {
                    $this->addResource($resource);
                }
            }
            if (!$calTask->emptyRDates()) {
                foreach ($calTask->getRDates() as $rDate) {
                    $this->addRDate($rDate);
                }
            }
            if (!$calTask->emptyUrl()) $this->setUrl($calTask->getUrl());
        }
    }

    public function buildFromArray(array $calArray, string $globalTimezone = null): self
    {
        /** initialisatio du fuseau horaire local au global */
        $globalTimezone = $globalTimezone ?? date_default_timezone_get();

        $this->setUid($calArray['UID']);
        $this->setDtStamp($calArray["DTSTAMP"]);

        if (array_key_exists("CLASS", $calArray)) $this->setClassification($calArray["CLASS"]);
        if (array_key_exists("COMPLETED", $calArray)) $this->setCompleted($calArray["COMPLETED"]);

        if (array_key_exists("CREATED", $calArray)) {
            $created = $this->extractDateMutable($calArray['CREATED'], $globalTimezone);
            $this->setCreated($created);
        }
        if (array_key_exists("DESCRIPTION", $calArray)) $this->setDescription($calArray["DESCRIPTION"]);

        if (array_key_exists("DTSTART", $calArray)){
            $dtStart = $this->extractDateMutable($calArray['DTSTART'], $globalTimezone);
            $this->setDtStart($dtStart);
        }

        $location = array_key_exists('LOCATION', $calArray) ? $calArray['LOCATION'] : null;
        $geo = array_key_exists("GEO", $calArray) ? $calArray["GEO"] : null;
        $location = $this->formatLocation($location, $geo);
        if ($location) {
            $this->setLocation($location);
        }

        if (array_key_exists("LAST-MODIFIED", $calArray)){
            $lastModified = $this->extractDateMutable($calArray['LAST-MODIFIED'], $globalTimezone);
            $this->setLastModified($lastModified);
        }

        $organizer = array_key_exists('ORGANIZER', $calArray) ? $calArray['ORGANIZER'] : [];
        if ($organizer) {
            $taskOrganizer = $this->formatOrganizer($organizer);
            $this->setOrganizer($taskOrganizer);
        }

        if (array_key_exists("PERCENT-COMPLETE", $calArray)) $this->setPercentComplete((int) $calArray["PERCENT-COMPLETE"]);
        if (array_key_exists("PRIORITY", $calArray)) $this->setPriority((int) $calArray["PRIORITY"]);
        if (array_key_exists("RECURID", $calArray)) $this->setRecurId($calArray["RECURID"]);
        if (array_key_exists("SEQ", $calArray)) $this->setSeq((int) $calArray["seq"]);
        if (array_key_exists("STATUS", $calArray)) $this->setStatus($calArray["STATUS"]);
        if (array_key_exists("SUMMARY", $calArray)) $this->setSummary($calArray["SUMMARY"]);

        /** -> intégration de la règle de répétition si présente */
        $rrule = array_key_exists('RRULE', $calArray) ? $calArray['RRULE'] : [];
        if ($rrule) {
            $rruleItem = $this->formatRRule($rrule);
            $this->setRRule($rruleItem);
        }

        if (array_key_exists("DUE", $calArray)) $this->setDue($calArray["DUE"]);
        if (array_key_exists("DURATION", $calArray)) $this->setDuration($calArray["DURATION"]);

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

        $exDates = array_key_exists('EXDATE', $calArray) ? $calArray['EXDATE'] : null;
        if ($exDates) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($exDates as $exDate) {
                $this->addExDate($exDate);
            }
        }
        $rStatus = array_key_exists('RSTATUS', $calArray) ? $calArray['RSTATUS'] : null;
        if ($rStatus) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($rStatus as $item) {
                $this->addRStatus($item);
            }
        }
        if (array_key_exists("RELATED", $calArray)) $this->setRelated($calArray["RELATED"]);
        $resources = array_key_exists('RESOURCES', $calArray) ? $calArray['RESOURCES'] : null;
        if ($resources) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($resources as $resource) {
                $this->addResource($resource);
            }
        }
        $rDates = array_key_exists('RDATES', $calArray) ? $calArray['RDATES'] : null;
        if ($rDates) {
            /** traitement du tableau des personnes concernées par l'événement */
            foreach ($rDates as $rDate) {
                $this->addRDate($rDate);
            }
        }
        if (array_key_exists("URL", $calArray)) $this->setUrl($calArray["URL"]);

        return $this;
    }

    public function toCalTask(CalTask $calTask = null): CalTask
    {
        if (!$calTask) $calTask = new CalTask();

        $calTask->setUid($this->getUid());
        $calTask->setDtStamp($this->getDtStamp());
        if (!$this->emptyClassification()) $calTask->setClassification($this->getClassification());
        if (!$this->emptyCompleted()) $calTask->setCompleted($this->getCompleted());
        if (!$this->emptyCreated()) $calTask->setCreated($this->getCreated());
        if (!$this->emptyDescription()) $calTask->setDescription($this->getDescription());
        if (!$this->emptyDtStart()) $calTask->setDtStart($this->getDtStart());
        if (!$this->emptyLocation()) $calTask->setLocation($this->getLocation());
        if (!$this->emptyLastModified()) $calTask->setLastModified($this->getLastModified());
        if (!$this->emptyOrganizer()) $calTask->setOrganizer($this->getOrganizer());
        $calTask->setPercentComplete($this->getPercentComplete());
        $calTask->setPriority($this->getPriority());
        if (!$this->emptyRecurId()) $calTask->setRecurId($this->getRecurId());
        $calTask->setSeq($this->getSeq());
        $calTask->setStatus($this->getStatus());
        if (!$this->emptySummary()) $calTask->setSummary($this->getSummary());
        if (!$this->emptyRRule()) $calTask->setRRule($this->getRRule());
        if (!$this->emptyDue()) $calTask->setDue($this->getDue());
        if (!$this->emptyDuration()) $calTask->setDuration($this->getDuration());
        if (!$this->emptyAttachs()) {
            foreach ($this->getAttachs() as $attach) {
                $calTask->addAttach($attach);
            }
        }
        if (!$this->emptyAttendees()) {
            foreach ($this->getAttendees() as $attendee) {
                $calTask->addAttendee($attendee);
            }
        }
        if (!$this->emptyCategories()) {
            foreach ($this->getCategories() as $category) {
                $calTask->addCategory($category);
            }
        }
        if (!$this->emptyComment()) $calTask->setComment($this->getComment());
        if (!$this->emptyContact()) $calTask->setContact($this->getContact());
        if (!$this->emptyExDates()) {
            foreach ($this->getExDates() as $exDate) {
                $calTask->addExDate($exDate);
            }
        }
        if (!$this->emptyRStatus()) {
            foreach ($this->getRStatus() as $rStatus) {
                $calTask->addRStatus($rStatus);
            }
        }
        if (!$this->emptyRelated()) $calTask->setRelated($this->getRelated());
        if (!$this->emptyResources()) {
            foreach ($this->getResources() as $resource) {
                $calTask->addResource($resource);
            }
        }
        if (!$this->emptyRDates()) {
            foreach ($this->getRDates() as $rDate) {
                $calTask->addRDate($rDate);
            }
        }
        if (!$this->emptyUrl()) $calTask->setUrl($this->getUrl());

        return $calTask;
    }

    /**
     * Get the value of uid
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Set the value of uid
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get the value of dtStamp
     */
    public function getDtStamp(): DateTime
    {
        return $this->dtStamp;
    }

    /**
     * Set the value of dtStamp
     */
    public function setDtStamp(DateTime $dtStamp): self
    {
        $this->dtStamp = $dtStamp;
        return $this;
    }

    /**
     * Get the value of classification
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
     * Set the value of classification
     */
    public function setClassification(string $classification): self
    {
        $this->classification = $classification;
        return $this;
    }

    /**
     * Get the value of completed
     */
    public function getCompleted(): ?DateTime
    {
        return $this->completed;
    }

    public function emptyCompleted()
    {
        return empty($this->completed);
    }

    /**
     * Set the value of completed
     */
    public function setCompleted(?DateTime $completed): self
    {
        $this->completed = $completed;
        return $this;
    }

    /**
     * Get the value of created
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function emptyCreated(): bool
    {
        return empty($this->created);
    }

    /**
     * Set the value of created
     */
    public function setCreated(?DateTime $created): self
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get the value of description
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
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of dtstart
     */
    public function getDtStart(): ?DateTime
    {
        return $this->dtStart;
    }

    public function emptyDtStart(): bool
    {
        return empty($this->dtStart);
    }

    /**
     * Set the value of dtstart
     */
    public function setDtStart(?DateTime $dtStart): self
    {
        $this->dtStart = $dtStart;
        return $this;
    }

    /**
     * get the Location of the Event (object EventLocation)
     * @return EventLocation
     */
    public function getLocation(): EventLocation
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
     * @return CalendarICS
     */
    public function setLocation(EventLocation $location): self
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get the value of lastModified
     */
    public function getLastModified(): ?DateTime
    {
        return $this->lastModified;
    }

    public function emptyLastModified():bool
    {
        return empty($this->lastModified);
    }

    /**
     * Set the value of lastModified
     */
    public function setLastModified(?DateTime $lastModified): self
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * Get the value of organizer
     */
    public function getOrganizer(): ?Organizer
    {
        return $this->organizer;
    }

    public function emptyOrganizer(): bool
    {
        return empty($this->organizer);
    }

    /**
     * Set the value of organizer
     */
    public function setOrganizer(?Organizer $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get the value of percentComplete
     */
    public function getPercentComplete(): int
    {
        return $this->percentComplete;
    }

    /**
     * @return boolean
     */
    public function emptyPercentComplete(): bool
    {
        return empty($this->percentComplete);
    }

    /**
     * Set the value of percentComplete
     */
    public function setPercentComplete(int $percentComplete): self
    {
        if ($percentComplete > -1 && $percentComplete < 101) {
            $this->percentComplete = $percentComplete;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of priority
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Set the value of priority
     */
    public function setPriority(int $priority): self
    {
        if ($priority < 0 || $priority > 9) return false;

        $this->priority = $priority;
        return $this;
    }

    /**
     * Get the value of recurrenceId
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
     */
    public function setRecurId(TaskRecurrenceId $recurId): self
    {
        $this->recurId = $recurId;
        return $this;
    }

    /**
     * Get the value of seq
     */
    public function getSeq(): int
    {
        return $this->seq;
    }

    /**
     * Set the value of seq
     */
    public function setSeq(int $seq): self
    {
        if (!is_numeric($seq)) return false;

        $this->seq = (int) $seq;
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
     * @return TaskICS|bool
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
     * get the Object or Summary of the task
     * @return ?string
     */
    public function getSummary(): string
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
     * set the Object or Summary of the Task
     *
     * @param string $summary
     * @return TaskICS
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * get the Persons concerned by the Event
     * @return Collection<int, Attendee>
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
     * @return TaskICS
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
     * @return TaskICS
     */
    public function removeAttendee(Attendee $attendee)
    {
        $this->attendees->removeElement($attendee);
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
     * Get the value of due
     * @return DateTime|null
     */
    public function getDue(): ?DateTime
    {
        return $this->due;
    }

    /**
     * @return boolean
     */
    public function emptyDue(): bool
    {
        return empty($this->due);
    }

    /**
     * Set the value of due
     * @param DateTime $due
     */
    public function setDue(DateTime $due): self
    {
        $this->due = $due;
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
     * add 1 attach to the Attachs of the Journal
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
     * remove 1 attach if exist in Attachs of the Journal
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
     * get the Resouces of the task
     * @return Collection<int, string>|null
     */
    public function getResources(): ?Collection
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
     * add 1 resource to the Resources of the Task
     * @param string $resource
     * @return self
     */
    public function addResource(string $resource): self
    {
        if (!in_array($resource, $this->resources)) {
            $this->resources[] = $resource;
        }
        return $this;
    }

    /**
     * remove 1 resource if exist in Resources of the Task
     * @param string $resource
     * @return self|bool
     */
    public function removeResource(string $resource): mixed
    {
        if (in_array($resource, $this->resources)) {
            $idx = array_search($resource, $this->resources);
            unset($this->resources[$idx]);
            return $this;
        }
        return false;
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
     * get the Categories of the Task
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
     * add 1 category to the Categories of the Task
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
     * remove 1 category if exist in Categories of the Task
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
}