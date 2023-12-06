<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class JournalICS
{
    private EntityManagerInterface $entityManager;

    private string              $uid;                   //
    private DateTime            $dtStamp;               //

    private ?array              $classes = null;        //
    private ?DateTime           $created = null;        //
    private ?DateTime           $dtStart = null;        //
    private ?DateTime           $lastMod = null;        //
    private ?Organizer          $organizer = null;      //
    private ?string             $recurId = null;        //
    private ?int                $seq = null;            //
    private ?string             $status = null;         //
    private ?string             $summary = null;        //
    private ?string             $url = null;            //

    private ?EventRepetition    $rrule = null;          //

    private ?array              $attach = null;         //
    private ?Collection         $attendees = null;      //
    private ?array              $categories = null;     //
    private ?string             $comment = null;        //
    private ?Contact            $contact = null;        //
    private ?string             $description = null;    //
    private ?string             $exDate = null;         //
    private ?string             $related = null;        //
    private ?string             $rDate = null;          //
    private ?string             $rStatus = null;        //
}