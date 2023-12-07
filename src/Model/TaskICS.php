<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalTask;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Model\EventLocation;
use Celtic34fr\CalendarCore\Model\TaskRecurrenceId;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class TaskICS
{
    private EntityManagerInterface $entityManager;

    private string              $uid;                   // *
    private DateTime            $dtStamp;               // *

    private ?array              $classes = null;        // *
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

        $this->attachs = new ArrayCollection();

        if ($calTask) {
            $this->setUid($calTask->getUid());
            $this->setDtStamp($calTask->getDtStamp());

            if (!$calTask->emptyClasses()) $this->setClasses($calTask->getClasses());
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
     * Get the value of classes
     */
    public function getClasses(): ?array
    {
        return $this->classes;
    }

    public function addClass(string $class)
    {
        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
            return $this;
        }
        return false;
    }

    public function removeClass(string $class)
    {
        if (in_array($class, $this->classes)) {
            $key = array_search($class, $this->classes);
            unset($this->classes[$key]);
            return $this;
        }
        return false;
    }

    /**
     * Set the value of classes
     */
    public function setClasses(array $classes): self
    {
        $this->classes = $classes;
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
     * get the Request Status of the Task
     * @return Collection<int, DateTime>|null
     */
    public function getRStatus(): ?Collection
    {
        return $this->rStatus;
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