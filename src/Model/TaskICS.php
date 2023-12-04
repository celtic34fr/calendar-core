<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\CalTask;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Model\EventLocation;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class TaskICS
{
    private EntityManagerInterface $entityManager;

    private string $uid;
    private DateTime $dtStamp;

    private ?string $class = null;
    private ?DateTime $completed = null;
    private ?DateTime $created = null;
    private ?string $description = null;
    private ?DateTime $dtStart = null;
    private ?EventLocation $location = null;
    private ?DateTime $lastModified = null;
    private ?Organizer $organizer = null;
    private int $percentComplete = 0;
    private int $priority = 0;
    private ?string $reccurenceId = null; // TODO gest structure
    private int $sequence = 0;
    private string $status;
    private ?string $summary = null;
    
    private ?string $attach = null; // TODO gest structure
    private ?Collection $attendees = null;
    private ?string $categories = null;
    private ?string $comment = null;
    private ?Contact $contact = null;
    private ?string $exDate = null;
    private ?string $requestStatus = null; // TODO gest Structure
    private ?string $relatedTo = null;
    private ?string $resources = null;
    private ?string $rData = null;

    public function __construct(EntityManagerInterface $entityManager, CalTask $calTask = null)
    {
        $this->entityManager = $entityManager;

        if ($calTask) {
            $this->setUid($calTask->getUid());
            $this->setDtStamp($calTask->getDtStamp());

            if (!$calTask->emptyCompleted()) $this->setCompleted($calTask->getCompleted());
            if (!$calTask->emptyCreated()) $this->setCreated($calTask->getCreated());
            if (!$calTask->emptyDescription()) $this->setDescription($calTask->getDescription());
            if (!$calTask->emptyDtStart()) $this->setDtStamp($calTask->getDtStamp());
            if (!$calTask->emptyLastModified()) $this->setLastModified($calTask->getLastModified());
            if (!$calTask->emptyOrganizer()) $this->setOrganizer($calTask->getOrganizer());
            if ($calTask->getPercentComplete() > -1 && $calTask->getPercentComplete() < 101 ) {
                $this->setPercentComplete($calTask->getPercentComplete());
            }
            if ($calTask->getPriority() > -1 && $calTask->getPriority() < 10) {
                $this->setPriority($calTask->getPriority());
            }
            if (is_numeric($calTask->getSequence()) && $calTask->getSequence() > -1) {
                $this->setSequence($calTask->getSequence());
            }
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
     * Get the value of sequence
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * Set the value of sequence
     */
    public function setSequence(int $sequence): self
    {
        if (!is_numeric($sequence)) return false;

        $this->sequence = $sequence;
        return $this;
    }
}