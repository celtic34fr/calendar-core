<?php

namespace Celtic34fr\CalendarCore\Service;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Model\EventICS;
use Celtic34fr\CalendarCore\Model\EventLocation;
use Celtic34fr\CalendarCore\Service\IcsCalendarReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Calendar Service Class
 * 
 * manage calendar in this extension
 * 
 * -> save/restore/init Calendar Event table (CaleEvent) to/with ICS files
 */
class CalendarManagement
{
    public function __construct(private EntityManagerInterface $entityManager, private KernelInterface $appKernel)
    {
    }

    public function restore(string $dirpath, string $filename)
    {
        if (file_exists($dirpath)) {
            if (chdir($dirpath)) {
                $allFiles = scandir($dirpath);
                $test = in_array($filename, $allFiles);
                if ($test) {
                    $calendar = new IcsCalendarReader();
                    $calArray = $calendar->load(file_get_contents($filename));
                }
            }

            /** récupération du fuseau horaire */
            $globalFuseau = null;
            if (array_key_exists('VTIMEZONE',$calArray)) {
                $vtimezone = $calArray['VTIMEZONE'];
                $globalFuseau = array_key_exists('TZID', $vtimezone) ? $vtimezone["TZID"] : '';
            }

            $events = $calArray['VEVENT'] ?? [];
            if ($events) {
                foreach ($events as $event) {
                    $eventICS = new EventICS($this->entityManager);
                    $eventICS->setUid($event['UID'] ?? "");
                    $eventICS->setSubject($event['SUMMARY']);
                    $eventICS->setDetails(array_key_exists('DESCRIPTION', $event) ? $event['DESCRIPTION'] : null);
                    $eventICS->setStatus(array_key_exists('STATUS', $event) ? $event['STATUS'] : "NEEDS-ACTION");
                   
                    $eventICS->setTimezone($globalFuseau);
                    $eventICS->setDateStart($event['DTSTART']);
                    $eventICS->setDateEnd($event['DTEND']);
                    $eventICS->setCreatedAt($event['CREATED']);
                    $eventICS->setLastupdatedAt($event['LAST-MODIFIED']);
                    /* -> détermination du type d'événement par jour ou sur durée en fonction de DTSTART */
                    $eventICS->setAllday((int) $eventICS->getDateStart()->format("His") == 0);
                    
                    $location = array_key_exists('LOCATION', $event) ? $event['LOCATION'] : null;
                    $geo = array_key_exists('GEO', $event) ? $event['GEO'] : null;
                    $eventICS->setLocation($this->formatEventLocation($location, $geo));

                    $attendees = array_key_exists('ATTENDEE', $event) ? $event['ATTENDEE'] : [];
                    if ($attendees) {
                        foreach($attendees as $invite) {
                            $calAttendee = $this->entityManager->getRepository(Attendee::class)
                                ->findOneBy(['email' => $invite['MAILTO']]);
                            if (!$calAttendee && array_key_exists('CN', $invite) && !empty($invite['CN'])) 
                                $calAttendee = $this->entityManager->getRepository(Attendee::class)
                                    ->findOneBy(["fullaname" => $invite['CN']]);

                            // si la personne désignée n'existe pas dans Attendee : création
                            if (!$calAttendee) {
                                $attendee = new Attendee();
                                $attendee->setEmail($invite['MAILTO']);
                                $attendee->setFullname($invite["CN"]);

                                if (array_key_exists("CUTYPE", $invite)) $attendee->setCuType($invite["CUTYPE"]);
                                if (array_key_exists("MEMBER", $invite)) $attendee->setMember($invite["MEMBER"]);
                                if (array_key_exists("ROLE", $invite)) $attendee->setRole($invite["ROLE"]);
                                if (array_key_exists("PARTSTAT", $invite)) $attendee->setPartStat($invite["PARTSTAT"]);
                                if (array_key_exists("RSVP", $invite)) $attendee->setRsvp($invite["RSVP"]);
                                if (array_key_exists("DELAGATETO", $invite)) $attendee->setDelegatedTo($invite["DELEGATETO"]);
                                if (array_key_exists("DELEGATEFROM", $invite)) $attendee->setDelegatedFrom($invite["DELEGATEFROM"]);
                                if (array_key_exists("SENDBY", $invite)) $attendee->setSendBy($invite["SENDBY"]);
                                if (array_key_exists("DIR", $invite)) $attendee->setDir($invite["DIR"]);
                                if (array_key_exists("LANGUAGE", $invite)) $attendee->setLanguage($invite["LANGUAGE"]);

                                $this->entityManager->persist($attendee);
                            }
                            $eventICS->addattendee($attendee);
                        }
                    }
                    if (array_key_exists('RRULE', $event)) {
                        $eventICS->setFrequence($event['RRULE']);
                    }

                    if (array_key_exists('UID', $event)) {
                        $calEvent = $this->entityManager->getRepository(CalEvent::class)->findOneBy(['uid' => $event['UID']]);
                    }

                    $calEvent = $eventICS->toCalEvent($calEvent);
                    if (!$calEvent->getId()) $this->entityManager->persist($calEvent);
                }
            }
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    private function formatEventLocation(string $location = null, array $geo = null): EventLocation
    {
        $eventLocation = new EventLocation();
        if (!$location && !$geo) return false;
        if ($location) $eventLocation->setLocation($location);
        if ($geo) {
            $eventLocation->setLatitude((float) $geo['LATITUDE']);
            $eventLocation->setLongitude((float) $geo['LONGITUDE']);
        }
        return $eventLocation;
    }
}