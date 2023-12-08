<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Entity\Parameter;
use Celtic34fr\CalendarCore\Enum\ClassesEnums;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Model\EventAlarm;
use Celtic34fr\CalendarCore\Model\EventLocation;
use Celtic34fr\CalendarCore\Model\EventRepetition;
use Celtic34fr\CalendarCore\Repository\CalEventRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalEventRepository::class)]
#[ORM\Table('cal_events')]
/**
 * Class CalEvent : Calendar Event
 * 
 * - created_at     : date de création de l'événement
 * - last_updated   : date de dernière modification de l'événement
 * - start_at       : date / date-heure de début de l'évènement 
 * - end_at         : date /date-heure de fin d l'événement
 * - subject        : objet ou titre de l'événement
 * - details        : informations complémentaires ou détails de l'événement
 * - nature         : type ou nature de l'événement
 * - bg_color       : couleur de fond de l'affichage de l'événement sans un calendrier (facultatif)
 * - bd_color       : couleur de bordure de l'affichage de l'événement sans un calendrier (facultatif)
 * - tx_color       : couleur d'écriture de l'affichage de l'événement sans un calendrier (facultatif)
 * - all_day        : top booléen indiquant si l'événement est en jour (true) ou en durée (false), à false par défaut
 * - status         : statut de l'événement
 * - uid            : UID associé à l'événement
 * - classes        : visibilité de l'événement (CLASS dans la norme RFC) : Privé, Public ou Confidentiel en base
 * - location       : précisiot de l'emplacement ou se déroule l'événement (objet EventLocation)
 * - timezone       : chaîne de caractère précisant le fuseau hpraire de référence pour l'événement
 * - frequence      : précise si renseigné, si lévénement doit êtrŒe répété et comment (RRULE componant)
 * - attendees      : participant à l'événement
 * - organizer      : organisateur de l'événement
 * - alarms         : défini et précise les alarmes paramétrées sur l'événement
 * - dt_stamp       :
 * - priority       :
 * - seq            :
 * - transp         :
 * - url            :
 * - recur_id       :
 * - duration       : durée de l'événement DateTimeInterval
 * - attachs        :
 * - categories     :
 * - contact        :
 * - ex_date        :
 * - r_status       :
 * - related        :
 * - resources      :
 * - rDate          :

 */
class CalEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\DateTime]
    private DateTime $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $last_updated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $start_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $end_at = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $details = null;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[ORM\JoinColumn(name: 'nature_id', referencedColumnName: 'id', nullable: true)]
    #[Assert\Type('string')]
    private ?Parameter $nature = null;

    #[ORM\Column(type: Types::TEXT, length: 7, nullable:true)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 7,     minMessage: "La taille minimale est de 7 caractères",
        max: 7,     maxMessage: "La taille maximale est de 7 caractères"
    )]
    private ?string $bg_color = null;

    #[ORM\Column(type: Types::TEXT, length: 7, nullable:true)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 7,     minMessage: "La taille minimale est de 7 caractères",
        max: 7,     maxMessage: "La taille maximale est de 7 caractères"
    )]
    private ?string $bd_color = null;

    #[ORM\Column(type: Types::TEXT, length: 7, nullable:true)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 7,     minMessage: "La taille minimale est de 7 caractères",
        max: 7,     maxMessage: "La taille maximale est de 7 caractères"
    )]
    private ?string $tx_color = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type('boolean')]
    private bool $all_day = false;

    #[ORM\Column(type: Types::TEXT, length: 64, nullable:false)]
    #[Assert\Type('string')]
    private string $status;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $uid = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable:true)]
    #[Assert\Type('string')]
    private ?string $classes = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventLocation $location = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $timezone = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventRepetition $frequence = null;

    #[ORM\ManyToMany(targetEntity: Attendee::class)]
    #[ORM\JoinColumn(name: 'attendee_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\JoinTable(name: 'event_attendees')]
    #[ORM\InverseJoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[Assert\Type('string')]
    private ?Collection $attendees = null;

    #[ORM\ManyToOne(targetEntity: Organizer::class)]
    #[ORM\JoinColumn(name: 'organizer_id', referencedColumnName: 'id', nullable: true)]
    private ?Organizer $organizer = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?Collection $alarms = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $dt_stamp = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $priority = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $seq = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $transp = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $recur_id = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $duration = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?Collection $attachs = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?Collection $categories = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id', nullable: true)]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $ex_date = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $r_status = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $related = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $resources = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $r_date = null; 

    public function __construct()
    {
        $this->setStatus(StatusEnums::NeedsAction->_toString());
        $this->setClasses(ClassesEnums::Public->_toString());
        $this->attendees = new ArrayCollection();
        $this->alarms = new ArrayCollection();
        $this->attachs = new ArrayCollection();
    }

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Creation's Date of Event 
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * Set the value of Creation's Date of Event
     * @param DateTime $created_at
     * @return CalEvent
     */
    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * LastUpdated's Date of Event
     * @return DateTime|null
     */
    public function getLastUpdated(): ?DateTime
    {
        return $this->last_updated;
    }

    /**
     * set the value of LastUpdated's Date of Event
     * @param DateTime|null $last_updated
     * @return CalEvent
     */
    public function setLastUpdated(?DateTime $last_updated): self
    {
        $this->last_updated = $last_updated;
        return $this;
    }

    /**
     * Start's Date of Event
     * @return DateTime|null
     */
    public function getStartAt(): ?DateTime
    {
        return $this->start_at;
    }

    /**
     * set th value of Start's Date of Event
     * @param DateTime $start_at
     * @return CalEvent
     */
    public function setStartAt(DateTime $start_at): self
    {
        $this->start_at = $start_at;
        return $this;
    }

    /**
     * End's Date of Event
     * @return DateTime|null
     */
    public function getEndAt(): ?DateTime
    {
        return $this->end_at;
    }

    /**
     * set the value Start's Date of Event
     * @param DateTime $end_at
     * @return CalEvent
     */
    public function setEndAt(DateTime $end_at): self
    {
        $this->end_at = $end_at;
        return $this;
    }

    /**
     * Object or Subject of Event
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * set the value of Object or Subject of Event
     * @param string $subject
     * @return CalEvent
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * get the value of Details of Event
     * @return string|null
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * set the value of Details of Event
     * @param string|null $details
     * @return CalEvent
     */
    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }


    /**
     * Nature or Type of Event
     * @return Parameter|null
     */
    public function getNature(): ?Parameter
    {
        return $this->nature;
    }

    /**
     * set the value of Nature or Type of Event
     * @param Parameter $nature
     * @return CalEvent
     */
    public function setNature(Parameter $nature): self
    {
        $this->nature = $nature;
        return $this;
    }

    /**
     * Get the value of bg_color
     * @return string|null
     */ 
    public function getBgColor(): ?string
    {
        return $this->bg_color;
    }

    /**
     * Set the value of bg_color
     * @param string $bg_color
     * @return  CalEvent
     */ 
    public function setBgColor(string $bg_color): self
    {
        $this->bg_color = $bg_color;
        return $this;
    }

    /**
     * Get the value of bd_color
     * @return string|null
     */ 
    public function getBdColor(): ?string
    {
        return $this->bd_color;
    }

    /**
     * Set the value of bd_color
     * @param string $bd_color
     * @return CalEvent
     */ 
    public function setBdColor(string $bd_color): self
    {
        $this->bd_color = $bd_color;
        return $this;
    }

    /**
     * Get the value of tx_color
     * @return string|null
     */ 
    public function getTxColor(): ?string
    {
        return $this->tx_color;
    }

    /**
     * Set the value of tx_color
     * @param string $tx_color
     * @return CalEvent
     */ 
    public function setTxColor(string $tx_color): self
    {
        $this->tx_color = $tx_color;
        return $this;
    }

    /**
     * Get the value of all_day
     * @return bool
     */ 
    public function getAllDay(): bool
    {
        return (bool) $this->all_day;
    }

    /**
     * Set the value of all_day
     * @param bool $all_day
     * @return CalEvent
     */ 
    public function setAllDay(bool $all_day): self
    {
        $this->all_day = (bool) $all_day;
        return $this;
    }

    /**
     * Get the value of status of the event
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     * @param string $status
     * @return CalEvent|bool
     */
    public function setStatus(string $status): mixed
    {
        if (StatusEnums::isValidVevent($status)) {
            $this->status = $status;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of uid
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * set the value of uid
     * @param string $uid
     * @return CalEvent
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;
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
     * @return CalEvent|bool
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
     * Get the value of location
     * @return EventLocation|null
     */
    public function getLocation(): ?EventLocation
    {
        return $this->location;
    }

    /**
     * set the value of location
     * @param EventLocation $location
     * @return CalEvent
     */
    public function setLocation(EventLocation $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of Timezone
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * Set the value of Timezone
     * @param string $timezone
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * get the Repetition of Event object
     * @return EventRepetition|null
     */
    public function getFrequence(): ?EventRepetition
    {
        return $this->frequence;
    }

    /**
     * set the Repetition of Event object
     * @param EventRepetition $frequence
     * @return CalEvent
     */
    public function setFrequence(EventRepetition $frequence): self
    {
        $this->frequence = $frequence;
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
     * @return boolean
     */
    public function emptyAttendees(): bool
    {
        return empty($this->attendees);
    }

    /**
     * add 1 attendee to the Attendees of the Event
     * @param Attendee $attendee
     * @return CalEvent
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
     * @return CalEvent|bool
     */
    public function removeAttendee(Attendee $attendee): mixed
    {
        if ($this->attendees->removeElement($attendee)) {
            return $this;
        }
        return false;
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
     * Get the value of alarms
     * get Persons, Contacts, Prospects, Customers
     * @return Collection|EventAlarm[]|null
     */
    public function getAlarms(): ?Collection
    {
        return $this->alarms;
    }

    /**
     * @return bool
     */
    public function emptyAlarms(): bool
    {
        return empty($this->alarms);
    }

    /**
     * add one Alamr to the Event
     * @param EventAlarm $alarm
     * @return CalEvent
     */
    public function addAlarm(EventAlarm $alarm): self
    {
        if (!$this->alarms->contains($alarm)) {
            $this->alarms[] = $alarm;
        }
        return $this;
    }

    /**
     * remove one Alarm to the Event
     * @param EventAlarm $alarm
     * @return CalEvent
     */
    public function removeAlarm(EventAlarm $alarm): self
    {
        $this->alarms->removeElement($alarm);
        return $this;
    }

    /**
     * Set the value of alarms
     * @param Collection $alarms
     * @return self
     */
    public function setAlarms(?Collection $alarms): self
    {
        $this->alarms = $alarms;
        return $this;
    }

    /**
     * Get the value of dt_stamp
     * @return DateTime|null
     */
    public function getDtStamp(): ?DateTime
    {
        return $this->dt_stamp;
    }

    public function emptyDtStamp(): bool
    {
        return empty($this->dt_stamp);
    }

    /**
     * Set the value of dt_stamp
     * @param DateTime $dt_stamp
     * @return self
     */
    public function setDtStamp(DateTime $dt_stamp): self
    {
        $this->dt_stamp = $dt_stamp;
        return $this;
    }

    /**
     * Get the value of priority
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @return boolean
     */
    public function emptyPriority(): bool
    {
        return empty($this->priority);
    }

    /**
     * Set the value of priority
     * @param string $priority
     * @return self
     */
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get the value of seq
     * @return int|null
     */
    public function getSeq(): ?int
    {
        return $this->seq;
    }

    /**
     * @return boolean
     */
    public function emptySeq(): bool
    {
        return empty($this->seq);
    }

    /**
     * Set the value of seq
     * @param int $seq
     * @return self
     */
    public function setSeq(string $seq): self
    {
        $this->seq = $seq;
        return $this;
    }

    /**
     * Get the value of transp
     * @return string|null
     */
    public function getTransp(): ?string
    {
        return $this->transp;
    }

    /**
     * @return boolean
     */
    public function emptyTransp(): bool
    {
        return empty($this->transp);
    }

    /**
     * Set the value of transp
     * @param string $transp
     * @return self
     */
    public function setTransp(string $transp): self
    {
        $this->transp = $transp;
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
     * Get the value of recur_id
     * @return string|null
     */
    public function getRecurId(): ?string
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
     * @param string $recur_id
     * @return self
     */
    public function setRecurId(string $recur_id): self
    {
        $this->recur_id = $recur_id;
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
     * @return self
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
     * add 1 attach to the Attachs of the Event
     * @param array $attach
     * @return CalEvent
     */
    public function addAttach(array $attach): self
    {
        if (!$this->attachs->contains($attach)) {
            $this->attachs->add($attach);
        }
        return $this;
    }

    /**
     * remove 1 attach if exist in Attachs of the Event
     * @param array $attach
     * @return CalEvent|bool
     */
    public function removeAttach(array $attach): mixed
    {
        if ($this->attachs->removeElement($attach)) {
            return $this;
        }
        return false;
    }

    /**
     * get the Categories of the Event
     * @return Collection<int, string>|null
     */
    public function getCategories(): ?Collection
    {
        return $this->categories;
    }

    /**
     * add 1 category to the Categories of the Event
     * @param string $category
     * @return CalEvent
     */
    public function addCategory(string $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function emptyCategories(): bool
    {
        return empty($this->categories);
    }

    /**
     * remove 1 category if exist in Categories of the Event
     * @param string $category
     * @return CalEvent|bool
     */
    public function removeCategory(string $category): mixed
    {
        if ($this->categories->removeElement($category)) {
            return $this;
        }
        return false;
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
     * Get the value of ex_date
     * @return DateTime|null
     */
    public function getExDate(): ?DateTime
    {
        return $this->ex_date;
    }

    /**
     * @return boolean
     */
    public function emptyExDate(): bool
    {
        return empty($this->ex_date);
    }

    /**
     * Set the value of ex_date
     * @param DateTime $ex_date
     * @return self
     */
    public function setExDate(DateTime $ex_date): self
    {
        $this->ex_date = $ex_date;
        return $this;
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
    public function setRStatus(string $r_status): self
    {
        $this->r_status = $r_status;
        return $this;
    }

    /**
     * Get the value of related
     * @return string |null
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
     * Get the value of resources
     * @return string|null
     */
    public function getResources(): ?string
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
     * Set the value of resources
     * @param string $resources
     * @return self
     */
    public function setResources(string $resources): self
    {
        $this->resources = $resources;
        return $this;
    }

    /**
     * Get the value of r_date
     * @return DateTime|null
     */
    public function getRDate(): ?DateTime
    {
        return $this->r_date;
    }

    /**
     * @return boolean
     */
    public function emptyRDate(): bool
    {
        return empty($this->r_date);
    }

    /**
     * Set the value of r_date
     * @param DateTime $r_date
     * @return self
     */
    public function setRDate(DateTime $r_date): self
    {
        $this->r_date = $r_date;
        return $this;
    }
}