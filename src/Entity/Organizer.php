<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Person;
use Celtic34fr\CalendarCore\Repository\OrganizerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizerRepository::class)]
/**
 * Class Organizer of the Event spécifics fields
 * 
 * added fields to Person entity
 * - dir      : DIR
 * - sendBy   : SENDBY
 * - language : LANGUAGE
 */
class Organizer extends Person
{
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $dir;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $sendBy;
    
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $language;

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
     * @param string $sendBy
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