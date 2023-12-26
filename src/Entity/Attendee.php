<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Person;
use Celtic34fr\CalendarCore\Enum\PartStatEnums;
use Celtic34fr\CalendarCore\Repository\AttendeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AttendeeRepository::class)]
/**
 * Class Attendees of the Event spécifics fields
 * 
 * added fields to Person entity
 * - cuType        : CUTYPE 
 * - member        : MEMBER
 * - role          : ROLE
 * - partStat      : PARTSTAT
 * - rsvp          : RSVP
 * - delegatedTo   : DELEGATED-TO
 * - delegatedFrom : DELEGATED-FROM
 * - sendBy        : SENDBY
 * - dir           : DIR
 * - language      : LANGUAGE
 */
class Attendee extends Person
{
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $cuType;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $member;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $role;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private string $partStat;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    #[Assert\Type('boolean')]
    private bool $rsvp = false;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $delegatedTo;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $delegatedFrom;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $dir;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $sendBy;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $language;


    public function __construct() {
        $this->setPartStat(PartStatEnums::NeedsAction->_toString());
    }

    /**
     * Get the value of cuType
     * @return string|null
     */
    public function getCuType(): ?string
    {
        return $this->cuType;
    }

    /**
     * Set the value of cuType
     * @param string $cuType
     * @return self
     */
    public function setCuType(string $cuType): self
    {
        $this->cuType = $cuType;
        return $this;
    }

    /**
     * Get the value of member
     * @return string|null
     */
    public function getMember(): ?string
    {
        return $this->member;
    }

    /**
     * Set the value of member
     * @param string $member
     * @return self
     */
    public function setMember(string $member): self
    {
        $this->member = $member;
        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Set the value of role
     */
    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get the value of partStat
     * @return string|null
     */
    public function getPartStat(): ?string
    {
        return $this->partStat;
    }

    /**
     * Set the value of partStat filtred by PartStatEnnums values
     * @param string $partStat
     * @return self|bool
     */
    public function setPartStat(string $partStat): mixed
    {
        if (PartStatEnums::isValid($partStat)) {
            $this->partStat = $partStat;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of rsvp
     * @return bool
     */
    public function isRsvp(): bool
    {
        return $this->rsvp;
    }

    /**
     * Set the value of rsvp
     * @param bool|null $rsvp
     * @return self
     */
    public function setRsvp(bool $rsvp = false): self
    {
        $this->rsvp = (bool) $rsvp;
        return $this;
    }

    /**
     * Get the value of delegatedTo
     * @return string|null
     */
    public function getDelegatedTo(): ?string
    {
        return $this->delegatedTo;
    }

    /**
     * Set the value of delegatedTo
     * @param string $delegatedTo
     * @return self
     */
    public function setDelegatedTo(string $delegatedTo): self
    {
        $this->delegatedTo = $delegatedTo;
        return $this;
    }

    /**
     * Get the value of delegatedFrom
     * @return string|null
     */
    public function getDelegatedFrom(): ?string
    {
        return $this->delegatedFrom;
    }

    /**
     * Set the value of delegatedFrom
     * @param string $delegatedFrom
     * @return self
     */
    public function setDelegatedFrom(string $delegatedFrom): self
    {
        $this->delegatedFrom = $delegatedFrom;
        return $this;
    }

    /**
     * Get the value of dir
     * @return string|null
     */
    public function getDir(): ?string
    {
        return $this->dir;
    }

    /**
     * Set the value of dir
     * @param string $dir
     * @return self
     */
    public function setDir(string $dir): self
    {
        $this->dir = $dir;
        return $this;
    }

    /**
     * Get the value of sendBy
     * @return string|null
     */
    public function getSendBy(): ?string
    {
        return $this->sendBy;
    }

    /**
     * Set the value of sendBy
     * @param string $senfdBy
     * @return self
     */
    public function setSendBy(string $sendBy): self
    {
        $this->sendBy = $sendBy;
        return $this;
    }

    /**
     * Get the value of language
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Set the value of language
     * @param string $language
     * @return self
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }
}