<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Repository\CalTypeRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** ***************
 * Classe CalType *
 * ****************
 * 
 * id
 * created_at [datetime] :  date de création du calendrier
 * name       [string]   :  nom affecté au calendrier
 * calendars  [relation] :  ensemble des calendriers du type défini par 'name', OneToMany vers la table Calendar
 */

#[ORM\Entity(repositoryClass: CalTypeRepository::class)]
#[ORM\Table('caltypes')]
class CalType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\DateTime]
    private DateTime $created_at;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $name;

    #[ORM\OneToMany(targetEntity: Calendar::class, mappedBy: 'type')]
    private Collection $calendars;


    public function __construct() {
        $this->calendars = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @param DateTime $created_at
     * @return CalType
     */
    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CalType
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    /**
     * @param Collection $calendars
     * @return CalType
     */
    public function setCalendars(Collection $calendars): self
    {
        $this->calendars = $calendars;
        return $this;
    }
}