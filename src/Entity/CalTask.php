<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Model\EventLocation;
use Celtic34fr\CalendarCore\Model\EventRepetition;
use Celtic34fr\CalendarCore\Model\TaskRecurrenceId;
use Celtic34fr\CalendarCore\Repository\CalTaskRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalTaskRepository::class)]
#[ORM\Table('taskevents')]
/**
 * Class CalEvent : Calendar Task
 */
class CalTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $uid;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\DateTime]
    private DateTime $dtStamp;
    
    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?array $classes = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\DateTime]
    private ?DateTime $completed = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\DateTime]
    private ?DateTime $created = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\DateTime]
    private ?DateTime $dtstart = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventLocation $location = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\DateTime]
    private ?DateTime $lastModified = null;

    #[ORM\ManyToOne(targetEntity: Organizer::class)]
    #[ORM\JoinColumn(name: 'organizer_id', referencedColumnName: 'id', nullable: true)]
    private ?Organizer $organizer = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $percentComplete = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $priority = 0;

    private ?TaskRecurrenceId $recurrenceId = null; // TODO gest structure

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $sequence = 0;

    #[ORM\Column(type: Types::TEXT, length: 64, nullable:false)]
    #[Assert\Type('string')]
    private string $status;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $url = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventRepetition $frequence;

    private ?DateTime $due = null;
    private ?string $duration = null;
    private ?string $attach = null; // TODO gest structure

    #[ORM\ManyToMany(targetEntity: Attendee::class)]
    #[ORM\JoinColumn(name: 'attendee_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\JoinTable(name: 'event_attendees')]
    #[ORM\InverseJoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[Assert\Type('string')]
    private ?Collection $attendees = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?string $categories = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id', nullable: true)]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?string $exDate = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?string $requestStatus = null; // TODO gest Structure

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $relatedTo = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?string $resources = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?string $rData = null;


    public function __construct()
    {
        $this->setStatus(StatusEnums::NeedsAction->_toString());
        $this->attendees = new ArrayCollection();
    }
    

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
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

    public function emptyClasses(): bool
    {
        return empty($this->classes);
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
     * Set the value of class
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

    public function emptyCompleted(): bool
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
    public function getDtstart(): ?DateTime
    {
        return $this->dtstart;
    }

    public function emptyDtStart(): bool
    {
        return empty($this->dtStamp);
    }

    /**
     * Set the value of dtstart
     */
    public function setDtstart(?DateTime $dtstart): self
    {
        $this->dtstart = $dtstart;
        return $this;
    }

    /**
     * Get the value of location
     * @return EventLocation|null
     */
    public function getLocation(): ?EventLocation
    {
        return $this->location;
    }

    public function emptyLocation(): bool
    {
        return empty($this->location);
    }

    /**
     * set the value of location
     * @param EventLocation|null $location
     * @return CalEvent
     */
    public function setLocation(?EventLocation $location): self
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

    public function emptyLastModified(): bool
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

    /**
     * @return bool
     */
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
        if ($percentComplete < 0 || $percentComplete > 100) {
            return false;
        }
        $this->percentComplete = $percentComplete;
        return $this;
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
    public function getRecurrenceId(): ?TaskRecurrenceId
    {
        return $this->recurrenceId;
    }

    /**
     * Set the value of recurrenceId
     */
    public function setRecurrenceId(TaskRecurenceId $recurrenceId): self
    {
        $this->recurrenceId = $recurrenceId;
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

    /**
     * Get the value of status of the task
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     * @param string $status
     * @return CalTask|bool
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
     * Object or Summary of task
     * @return ?string
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * set the value of Object or Summary of Task
     * @param string $summary
     * @return CalTask
     */
    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * get the Attendees of the Event
     * @return Collection<int, Attendee>
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    /**
     * add 1 attendee to the Attendees of the Event
     * @param Attendee $attendee
     * @return CalTask
     */
    public function addAttendee(Attendee $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
        }
        return $this;
    }

    /**
     * remove 1 attendee if exist in Attendees of the Event
     * @param Attendee $attendee
     * @return CalTask|bool
     */
    public function removeAttendee(Attendee $attendee): mixed
    {
        if ($this->attendees->removeElement($attendee)) {
            return $this;
        }
        return false;
    }
}