<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Person;
use Celtic34fr\CalendarCore\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
/**
 * Class Contacts of the Event spécifics fields
 * 
 * added fields to Person entity
 * - altrep     : ALTREP parameter
 * - language   : LANGUAGE parameter
 */
class Contact extends Person
{
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $altrep = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $language;

    /**
     * Get the value of altrep
     * @return string|null
     */
    public function getAltrep(): ?string
    {
        return $this->altrep;
    }

    /**
     * Set the value of altrep
     * @param string $altrep
     * @return self
     */
    public function setAltrep(string $altrep): self
    {
        $this->altrep = $altrep;
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
    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }
}