<?php

namespace Celtic34fr\CalendarCore\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttendeeRepository::class)]
#[ORM\Table('attendees')]
/**
 * Class Attendee : Attendees of the Event
 * 
 * - fullname : person's full name
 * - email    : person's email address
 */ 
class Attendee
{
    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $fullname;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $email;

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
     * @return Attendee
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
     * @return Attendee
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}