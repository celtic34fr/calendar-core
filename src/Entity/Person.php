<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Repository\PersonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\Table('persons')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['person' => Person::class, 'contact' => Contact::class,
    'organizer' => Organizer::class, 'attendee' => Attendee::class])]
/**
 * Class Person : Attendees, Contacts, Organizer of the Event common fields
 * 
 * - fullname      : person's full name
 * - addInfos      : additionnal informations
 * - email         : person's email address
 */ 
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $fullname;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $email;

    /**
     * Get the value of id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of fullname
     * @return string
     */
    public function getFullname(): string
    {
        return $this->fullname;
    }

    /**
     * Set the value of fullname
     * @param string $fullname
     * @return self
     */
    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get the value of email
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}