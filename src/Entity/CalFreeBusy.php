<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Repository\CalFreeBusyRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalFreeBusyRepository::class)]
#[ORM\Table('cal_free_busy')]
/**
 * Class CalFreeBusy : Calendar FreeBusy
 */
class CalFreeBusy
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

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id', nullable: true)]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $dt_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $dt_end = null;

    #[ORM\ManyToOne(targetEntity: Organizer::class)]
    #[ORM\JoinColumn(name: 'organizer_id', referencedColumnName: 'id', nullable: true)]
    private ?Organizer $organizer = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $url = null;

    #[ORM\ManyToMany(targetEntity: Attendee::class)]
    #[ORM\JoinColumn(name: 'attendee_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\JoinTable(name: 'event_attendees')]
    #[ORM\InverseJoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[Assert\Type('string')]
    private ?Collection $attendees = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::ARRAY, nullable:true)]
    #[Assert\Type('array')]
    private ?array $frees_busies = null;

    #[ORM\Column(type: Types::TEXT, nullable:true)]
    #[Assert\Type('string')]
    private ?string $r_status = null; // TODO gest Structure


    public function __construct()
    {
        $this->attendees = new ArrayCollection();
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
    public function setContact(Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * Get the value of dt_start
     * @return DateTime|null
     */
    public function getDtStart(): ?DateTime
    {
        return $this->dt_start;
    }

    /**
     * @return boolean
     */
    public function emptyDtStart(): bool
    {
        return empty($this->dt_start);
    }

    /**
     * Set the value of dt_start
     */
    public function setDtStart(?DateTime $dt_start): self
    {
        $this->dt_start = $dt_start;

        return $this;
    }

    /**
     * Get the value of dt_end
     * @return DateTime|null
     */
    public function getDtEnd(): ?DateTime
    {
        return $this->dt_end;
    }

    /**
     * @return boolean
     */
    public function emptyDtEnd(): bool
    {
        return empty($this->dt_end);        
    }

    /**
     * Set the value of dt_end
     * @param DateTime $dt_end
     * @return self
     */
    public function setDtEnd(DateTime $dt_end): self
    {
        $this->dt_end = $dt_end;
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
     * get the Attendees of the Event
     * @return Collection<int, Attendee>|null
     */
    public function getAttendees(): ?Collection
    {
        return $this->attendees;
    }

    /**
     * add 1 attendee to the Attendees of the Event
     * @param Attendee $attendee
     * @return self|bool
     */
    public function addAttendee(Attendee $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
            return $this;
        }
        return false;
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
        return $this->frees_busies;
    }

    /**
     * @return boolean
     */
    public function emptyFreesBusies(): bool
    {
        return empty($this->frees_busies);
    }

    /**
     * add 1 Freebusy to the FreesBusies of the CalFreesBusies
     * @param string $freebusy
     * @return self|bool
     */
    public function addFreeBusy(string $freebusy): self
    {
        if (!in_array($freebusy, $this->frees_busies)) {
            $this->frees_busies[] = $freebusy;
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
        if (in_array($freebusy, $this->frees_busies)) {
            $idx = array_search($freebusy, $this->frees_busies);
            unset($this->frees_busies[$idx]);
            return $this;
        }
        return false;
    }


    /**
     * Get the value of r_status
     * @return string|null
     */
    public function getRStatus(): ?string
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
     * Set the value of r_status
     * @param string $r_status
     * @return self
     */
    public function setRStatus(?string $r_status): self
    {
        $this->r_status = $r_status;
        return $this;
    }
}