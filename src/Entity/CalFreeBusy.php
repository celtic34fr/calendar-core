<?php

namespace Celtic34fr\CalendarCore\Entity;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Model\FreeBusyItem;
use Celtic34fr\CalendarCore\Repository\CalFreeBusyRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalFreeBusyRepository::class)]
#[ORM\Table('cal_free_busy')]
/**
 * Class CalFreeBusy : Calendar FreeBusy
 */
class CalFreeBusy
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

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'id', nullable: true)]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $dt_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTime $dt_end = null;

    #[ORM\ManyToOne(targetEntity: Organizer::class)]
    #[ORM\JoinColumn(name: 'organizer_id', referencedColumnName: 'id', nullable: true)]
    private ?Organizer $organizer = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\Type('string')]
    private ?string $url = null;

    #[ORM\ManyToMany(targetEntity: Attendee::class)]
    #[ORM\JoinColumn(name: 'attendee_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\JoinTable(name: 'event_attendees')]
    #[ORM\InverseJoinColumn(name: 'event_id', referencedColumnName: 'id')]
    #[Assert\Type('string')]
    private ?Collection $attendees = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::JSON, nullable:true)]
    #[Assert\Type('array')]
    private ?array $frees_busies = null;

    #[ORM\Column(type: Types::TEXT, nullable:true)]
    #[Assert\Type('string')]
    private ?string $r_status = null; // TODO gest Structure

}