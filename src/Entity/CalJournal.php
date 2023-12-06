<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Model\EventRepetition;
use Celtic34fr\CalendarCore\Model\TaskRecurrenceId;
use Celtic34fr\CalendarCore\Repository\CalJournalRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalJournalRepository::class)]
#[ORM\Table('cal_tasks')]
/**
 * Class CalJournal : Calendar Journal
 */
class CalJournal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $uid;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\DateTime]
    private DateTime $dtStamp;
    
    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?array $classes = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $dtstart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $lastModified = null;

    #[ORM\ManyToOne(targetEntity: Organizer::class)]
    #[ORM\JoinColumn(name: 'organizer_id', referencedColumnName: 'id', nullable: true)]
    private ?Organizer $organizer = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?TaskRecurrenceId $recur_id = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $seq = 0;

    #[ORM\Column(type: Types::TEXT, length: 64, nullable:false)]
    #[Assert\Type('string')]
    private string $status;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $url = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?EventRepetition $rrule;

    #[ORM\Column(type: Types::TEXT, length: 64, nullable:false)]
    #[Assert\Type('string')]
    private ?string $attach = null; // TODO gest structure

    #[ORM\ManyToMany(targetEntity: Attendee::class)]
    #[ORM\JoinColumn(name: 'attendee_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\JoinTable(name: 'event_attendees')]
    #[ORM\InverseJoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[Assert\Type('string')]
    private ?Collection $attendees = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?string $categories = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id', nullable: true)]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable:true)]
    #[Assert\Type('string')]
    private ?string $ex_date = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $related = null;

    #[ORM\Column(type: Types::TEXT, nullable:true)]
    #[Assert\Type('string')]
    private ?string $r_date = null;

    #[ORM\Column(type: Types::TEXT, nullable:true)]
    #[Assert\Type('string')]
    private ?string $r_status = null; // TODO gest Structure

}