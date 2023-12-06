<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Entity\Contact;
use Celtic34fr\CalendarCore\Entity\Organizer;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class FreeBusyICS
{
    private EntityManagerInterface $entityManager;

    private string              $uid;                   //
    private DateTime            $dtStamp;               //

    private ?Contact            $contact = null;        //
    private ?DateTime           $dtStart = null;        //
    private ?DateTime           $dtEnd = null;          //
    private ?Organizer          $organizer = null;      //
    private ?string             $url = null;            //

    private ?Collection         $attendees = null;      //
    private ?string             $comment = null;        //
    private ?Collection         $freesBusies = null;    //
    private ?string             $rStatus = null;        //
    
    public function __construct(EntityManagerInterface $entityManager, CalFreeBusy $calFreeBusy = null)
    {
        $this->entityManager = $entityManager;
    }

}