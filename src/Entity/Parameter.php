<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Repository\ParameterRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParameterRepository::class)]
#[ORM\Table(name: 'parameters')]
#[ORM\HasLifecycleCallbacks]
/**
 * Class Parameter
 * 
 * cle          :
 * ord          :
 * valeur       :
 * created_at   : date de création
 * updated_at   : date dernière mise à jour
 */
class Parameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 16)]
    #[Assert\Length(
        min: 4,
        minMessage: "Une clé d'accès doit comporter au moins 4 caractères",
        max: 16,
        maxMessage: "Une clé d'accès doit comporter au plus 16 caractères"
    )]
    #[Assert\Type('string')]
    protected string $cle;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Type('integer')]
    protected int $ord = 0;

    #[ORM\Column(type: Types::JSON, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected ?string $valeur = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\DateTime]
    protected DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Assert\DateTime]
    protected ?DateTimeImmutable $updated_at;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable('now');
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of cle
     */
    public function getCle(): string
    {
        return $this->cle;
    }

    /**
     * Set the value of cle
     * @return  self
     */
    public function setCle(string $cle): self
    {
        $this->cle = $cle;
        return $this;
    }

    /**
     * Get the value of ord
     */
    public function getOrd(): int
    {
        return $this->ord;
    }

    /**
     * Set the value of ord
     * @return  self
     */
    public function setOrd(int $ord): self
    {
        $this->ord = $ord;
        return $this;
    }

    /**
     * Get the value of valeur
     * @return  null|string|array
     */
    public function getValeur(): mixed
    {
        return $this->valeur;
    }

    /**
     * Set the value of valeur
     * @param string|array $valeyur
     * @return  self
     */
    public function setValeur(mixed $valeur): self
    {
        $this->valeur = $valeur;
        return $this;
    }

    /**
     * Get the value of created_at
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     * @return  self
     */
    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Get the value of updated_at
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function isEmptyUpdatedAt()
    {
        return empty($this->updated_at);
    }

    /**
     * Set the value of updated_at
     * @return  self
     */
    public function setUpdatedAt(DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
