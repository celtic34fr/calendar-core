<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\CalType;
use Celtic34fr\CalendarCore\Entity\Person;
use Celtic34fr\CalendarCore\Repository\CalendarRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** ****************
 * Classe Calendar *
 * *****************
 * 
 * id
 * created_at   datetime    date de création du calendrier
 * closed_at    datetime    date de clôture du calendrier (nullable)
 * name         string      nom affecté au calendrier
 * type         relation    typologie associé au calendrier, ManyToOne vers table CalType
 * owner        relation    propriétaire du calendrier, ManyToOne vers table Person
 */

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
#[ORM\Table('calendars')]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\DateTime]
    private DateTime $created_at;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Assert\DateTime]
    private DateTime $closed_at;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: CalType::class, inversedBy: 'calendars')]
    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id', nullable: true)]
    private ?CalType $type = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true)]
    private ?Person $owner = null;


    /**
     * @return int
     */
    public function getId(): ?int
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
     * @param DateTime $creates_at
     * @return Calendar
     */
    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getClosedAt(): DateTime
    {
        return $this->closed_at;
    }

    /**
     * @param DateTime $closed_at
     * @return Calendar
     */
    public function setClosedAt(DateTime $closed_at): self
    {
        $this->closed_at = $closed_at;
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
     * @return Calendar
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return CalType|null
     */
    public function getType(): ?CalType
    {
        return $this->type;
    }

    /**
     * Get type name in table CalType if exist
     *
     * @return string|null
     */
    public function getTypeName(): ?string
    {
        $type = $this->getType();
        if ($type) return $type->getName();
        return $type;
    }

    /**
     * @param CalType $type
     * @return Calendar
     */
    public function setType(CalType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Person|null
     */
    public function getOwner(): ?Person
    {
        return $this->owner;
    }

    /**
     * get owner display name if exist
     *
     * @return string|null
     */
    public function getOwnerName(): ?string
    {
        $owner = $this->getOwner();
        if ($owner) return $owner->getFullname();
        return $owner;
    }

    /**
     * @param Person $owner
     * @return Calendar
     */
    public function setOwner(Person $owner): self
    {
        $this->owner = $owner;
        return $this;
    }
}
