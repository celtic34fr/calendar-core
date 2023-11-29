<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Parameter;
use Celtic34fr\CalendarCore\Enum\StatusEnums;
use Celtic34fr\CalendarCore\Enum\VisibiliteEnums;
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
 * - visibilite     : visibilité de l'événement (CLASS dans la norme RFC) : Privé, Public ou Confidentiel en base
 * - location       : précisiot de l'emplacement ou se déroule l'événement (objet EventLocation)
 * - timezone       : chaîne de caractère précisant le fuseau hpraire de référence pour l'événement
 * - attendees      : participant à l'événement
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

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $subject;

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

    #[ORM\Column(type: Types::TEXT, length: 4, nullable:false)]
    #[Assert\Type('string')]
    private string $status;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $uid = null;

    #[ORM\Column(type: Types::TEXT, length: 2, nullable:false)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 7,     minMessage: "La taille minimale est de 2 caractères",
        max: 7,     maxMessage: "La taille maximale est de 2 caractères"
    )]
    private ?string $visibilite;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventLocation $location = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $timezone = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventRepetition $frequence;

    #[ORM\ManyToMany(targetEntity: Attendee::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\JoinTable(name: 'event_attendees')]
    #[ORM\InverseJoinColumn(name: 'attendee_id', referencedColumnName: 'id')]
    #[Assert\Type('string')]
    private ?Collection $attendees = null;

    public function __construct()
    {
        $this->setStatus(StatusEnums::WaitResponse->_toString());
        $this->setVisibilite(VisibiliteEnums::Public->_toString());
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
     * @return string
     */
    public function getSubject(): string
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
     * @param string|null $bg_color
     * @return  CalEvent
     */ 
    public function setBgColor(?string $bg_color): self
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
     * @param string|null $tx_color
     * @return CalEvent
     */ 
    public function setTxColor(?string $tx_color): self
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
        return $this->all_day;
    }

    /**
     * Set the value of all_day
     * @param bool $all_day
     * @return CalEvent
     */ 
    public function setAllDay(bool $all_day): self
    {
        $this->all_day = $all_day;

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
        if (StatusEnums::isValid($status)) {
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
     * @param string|null $uid
     * @return CalEvent
     */
    public function setUid(?string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Get the value of visibilite
     * @return string|null
     */
    public function getVisibilite(): ?string
    {
        return $this->visibilite;
    }

    /**
     * set the value of visibilite
     * @param string|null $visibilite
     * @return CalEvent|bool
     */
    public function setVisibilite(?string $visibilite): mixed
    {
        if (VisibiliteEnums::isValid($visibilite)) {
            $this->visibilite = $visibilite;
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
     * @param EventLocation|null $location
     * @return CalEvent
     */
    public function setLocation(?EventLocation $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of Timezone
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * Set the value of Timezone
     */
    public function setTimezone(?string $timezone): self
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
    public function setFrequence(?EventRepetition $frequence): self
    {
        $this->frequence = $frequence;
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
}