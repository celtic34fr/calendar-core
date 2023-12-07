<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Enum\ClassesEnums;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Model\EventRepetition;
use Celtic34fr\CalendarCore\Model\TaskRecurrenceId;
use Celtic34fr\CalendarCore\Repository\CalJournalRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalJournalRepository::class)]
#[ORM\Table('cal_tasks')]
/**
 * Class CalJournal : Calendar Journal
 */
class CalJournal
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
    
    #[ORM\Column(type: Types::TEXT, nullable:true)]
    #[Assert\Type('string')]
    private ?string $classes = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $dtstart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $lastModified = null;

    #[ORM\ManyToOne(targetEntity: Organizer::class)]
    #[ORM\JoinColumn(name: 'organizer_id', referencedColumnName: 'id', nullable: true)]
    private ?Organizer $organizer = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?TaskRecurrenceId $recur_id = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $seq = 0;

    #[ORM\Column(type: Types::TEXT, length: 64, nullable:false)]
    #[Assert\Type('string')]
    private string $status;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $url = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventRepetition $rrule;

    #[ORM\Column(type: Types::JSON, length: 64, nullable:false)]
    #[Assert\Type('array')]
    private ?Collection $attachs = null; // TODO gest structure

    #[ORM\ManyToMany(targetEntity: Attendee::class)]
    #[ORM\JoinColumn(name: 'attendee_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\JoinTable(name: 'event_attendees')]
    #[ORM\InverseJoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[Assert\Type('string')]
    private ?Collection $attendees = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?array $categories = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id', nullable: true)]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?array $ex_dates = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $related = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?array $r_dates = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?array $r_status = null; // TODO gest Structure


    public function __construct()
    {
        $this->setClasses(ClassesEnums::Public->_toString());
        $this->setStatus(StatusEnums::NeedsAction->_toString());
    }

    /**
     * Get the value of id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
    public function setUid(string $uid)
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

    public function emptyClasses(): bool
    {
        return empty($this->classes);
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
     * Get the value of dt_stamp
     * @return DateTime|null
     */
    public function getDtStart(): ?DateTime
    {
        return $this->dtstart;
    }

    public function emptyDtStart(): bool
    {
        return empty($this->dtstart);        
    }

    /**
     * Set the value of dtstart
     * @param DateTime $dtstart
     * @return self
     */
    public function setDtStart(DateTime $dtstart): self
    {
        $this->dtstart = $dtstart;
        return $this;
    }

    /**
     * Get the value of dtend
     * @return DateTime|null
     */
    public function getLastModified(): ?DateTime
    {
        return $this->lastModified;
    }

    /**
     * @return boolean
     */
    public function emptyLastModified(): bool
    {
        return empty($this->lastModified);        
    }

    /**
     * Set the value of lastModified
     * @param DateTime $lastModified
     * @return self
     */
    public function setLastModified(DateTime $lastModified): self
    {
        $this->lastModified = $lastModified;
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
     * @return bool
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
     * Get the value of recur_id
     * @return TaskRecurrenceId|null
     */
    public function getRecurId(): ?TaskRecurrenceId
    {
        return $this->recur_id;
    }

    /**
     * @return boolean
     */
    public function emptyRecurId(): bool
    {
        return empty($this->recur_id);
    }

    /**
     * Set the value of recur_id
     * @param TaskRecurrenceId $recur_id
     * @return self
     */
    public function setRecurId(?TaskRecurrenceId $recur_id): self
    {
        $this->recur_id = $recur_id;

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
     * get the RepetitionRule of Journal object
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
     * set the RepetitionRule of Journal object
     * @param EventRepetition $rrule
     * @return self
     */
    public function setRRule(EventRepetition $rrule): self
    {
        $this->rrule = $rrule;
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
     * get the Attendees of the Event
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
     * add 1 attendee to the Attendees of the Event
     * @param Attendee $attendee
     * @return self
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
     * @return self|bool
     */
    public function removeAttendee(Attendee $attendee): mixed
    {
        if ($this->attendees->removeElement($attendee)) {
            return $this;
        }
        return false;
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
        return $this->ex_dates;
    }

    /**
     * @return boolean
     */
    public function emptyExDates(): bool
    {
        return empty($this->ex_dates);
    }

    /**
     * add 1 exception-date to the ExceptionDates of the Journal
     * @param DateTime $ex_date
     * @return self
     */
    public function addExDate(DateTime $ex_date): self
    {
        if (!in_array($ex_date, $this->ex_dates)) {
            $this->ex_dates[] = $ex_date;
        }
        return $this;
    }

    /**
     * remove 1 exception-date if exist in ExceptionDates of the Journal
     * @param DateTime $ex_date
     * @return self|bool
     */
    public function removeExDate(DateTime $ex_date): mixed
    {
        if (in_array($ex_date, $this->ex_dates)) {
            $idx = array_search($ex_date, $this->ex_dates);
            unset($this->ex_dates[$idx]);
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
        return $this->r_dates;
    }

    /**
     * @return boolean
     */
    public function emptyRDates(): bool
    {
        return empty($this->r_dates);
    }

    /**
     * add 1 recurrence-dateTime to the Recurrence-DateTimes of the Journal
     * @param DateTime $r_date
     * @return self
     */
    public function addRDate(DateTime $r_date): self
    {
        if (!in_array($r_date, $this->r_dates)) {
            $this->r_dates[] = $r_date;
        }
        return $this;
    }

    /**
     * remove 1 recurrence-dateTime if exist in Recurrence-DateTimes of the Journal
     * @param DateTime $r_date
     * @return self|bool
     */
    public function removeRDate(DateTime $r_date): mixed
    {
        if (in_array($r_date, $this->r_dates)) {
            $idx = array_search($r_date, $this->r_dates);
            unset($this->r_dates[$idx]);
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
        return $this->r_status;
    }

    /**
     * @return boolean
     */
    public function emptyRStatus(): bool
    {
        return empty($this->r_status);
    }

    /**
     * add 1 request-status to the Request Status of the Journal
     * @param DateTime $r_status
     * @return self
     */
    public function addRStatus(DateTime $r_status): self
    {
        if (!in_array($r_status, $this->r_status)) {
            $this->r_status[] = $r_status;
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