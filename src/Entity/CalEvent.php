<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Calendar;
use Celtic34fr\CalendarCore\Entity\Parameter;
use Celtic34fr\CCalendarCore\Repository\CalEventRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** ****************
 * Classe CalEvent *
 * *****************
 * 
 * id
 * start_at     datetime    date de début de l'événement au format 'YYYY/MM/DD HH:ii:ss'
 * end_at       datetime    date de fin de l'événement au format 'YYYY/MM/DD HH:ii:ss'
 * objet        string      objet de l(événement
 * complements  string      explication, résumé de l'événement
 * nature       relation    nature de l'événement, ManyToOne vers table Paramter, clé d'accès 'SysCalNature'
 * bg_color     string      couleur de fond d'affichage de l'événement dans un calendrier graphique
 * bd_color     string      couleur de bordure d'affichage de l'événement dans un calendrier graphique
 * tx_color     string      colouleur d'écriture d'affichage de l'événement dans un calendrier graphique
 * all_day      boolean     top indiquant si l'événement est sur une ou des journées entières (true) ou non (false)
 * calmendar    relation    calendrier de rattachement de l'événement, ManyToOne vers la table Calendar
 */

#[ORM\Entity(repositoryClass: CalEventRepository::class)]
#[ORM\Table('cal_events')]
class CalEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\DateTime]
    private DateTime $start_at;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\DateTime]
    private DateTime $end_at;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $objet;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $complements = null;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[ORM\JoinColumn(name: 'nature_id', referencedColumnName: 'id', nullable: true)]
    private ?Parameter $nature = null;

    #[ORM\Column(type: Types::TEXT, length: 7, nullable: true)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 7,     minMessage: "La taille minimale est de 7 caractères",
        max: 7,     maxMessage: "La taille maximale est de 7 caractères"
    )]
    private ?string $bg_color = null;

    #[ORM\Column(type: Types::TEXT, length: 7, nullable: true)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 7,     minMessage: "La taille minimale est de 7 caractères",
        max: 7,     maxMessage: "La taille maximale est de 7 caractères"
    )]
    private ?string $bd_color = null;

    #[ORM\Column(type: Types::TEXT, length: 7, nullable: true)]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 7,     minMessage: "La taille minimale est de 7 caractères",
        max: 7,     maxMessage: "La taille maximale est de 7 caractères"
    )]
    private ?string $tx_color = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\Type('boolean')]
    private ?bool $all_day = false;

    #[ORM\ManyToOne(targetEntity: Calendar::class)]
    #[ORM\JoinColumn(name: 'calendar_id', referencedColumnName: 'id', nullable: false)]
    private Calendar $calendar = null;

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getStartAt(): DateTime
    {
        return $this->start_at;
    }

    /**
     * @param DateTime $start_at
     * @return CalEcent
     */
    public function setStartAt(DateTime $start_at): self
    {
        $this->start_at = $start_at;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndAt(): DateTime
    {
        return $this->end_at;
    }

    /**
     * @param DateTime $end_at
     * @return CalEvent
     */
    public function setEndAt(DateTime $end_at): self
    {
        $this->end_at = $end_at;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjet(): string
    {
        return $this->objet;
    }

    /**
     * @param string $objet
     * @return CalEvent
     */
    public function setObjet(string $objet): self
    {
        $this->objet = $objet;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComplements(): ?string
    {
        return $this->complements;
    }

    /**
     * @param string|null $complements
     * @return self
     */
    public function setComplements(?string $complements): self
    {
        $this->complements = $complements;
        return $this;
    }

    /**
     * @return Parameter|null
     */
    public function getNature(): ?Parameter
    {
        return $this->nature;
    }

    /**
     * @param Parameter $nature
     * @return CalEvent
     */
    public function setNature(Parameter $nature): self
    {
        $this->nature = $nature;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBgColor(): ?string
    {
        return $this->bg_color;
    }

    /**
     * @param string $bg_color
     * @return CalEvent
     */
    public function setBgColor(string $bg_color): self
    {
        $this->bg_color = $bg_color;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBdColor(): ?string
    {
        return $this->bd_color;
    }

    /**
     * @param string $bd_color
     * @return CalEvent
     */
    public function setBdColor(string $bd_color): self
    {
        $this->bd_color = $bd_color;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTxColor(): ?string
    {
        return $this->tx_color;
    }

    /**
     * @param string $tx_color
     * @return CalEvent
     */
    public function setTxColor(string $tx_color): self
    {
        $this->tx_color = $tx_color;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAllDay(): bool
    {
        return $this->all_day;
    }

    /**
     * @param boolean $all_day
     * @return CalEvent
     */
    public function setAllDay(bool $all_day): self
    {
        $this->all_day = $all_day;
        return $this;
    }

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->calendar;
    }

    /**
     * @param Calendar $calendar
     * @return CalEvent
     */
    public function setCalendar(Calendar $calendar): self
    {
        $this->calendar = $calendar;
        return $this;
    }
}