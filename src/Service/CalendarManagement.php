<?php

namespace Celtic34fr\CalendarCore\Service;

use Celtic34fr\CalendarCore\Entity\Attendee;
use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Entity\Organizer;
use Celtic34fr\CalendarCore\Model\EventAlarm;
use Celtic34fr\CalendarCore\Model\EventICS;
use Celtic34fr\CalendarCore\Model\EventLocation;
use Celtic34fr\CalendarCore\Model\TaskICS;
use Celtic34fr\CalendarCore\Model\TaskRecurrenceId;
use Celtic34fr\CalendarCore\Service\IcsCalendarReader;
use DateTime;
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
                $this->generateEvents($events, $globalFuseau);
            }

            $tasks = $calArray['VTODO'] ?? [];
            if ($tasks) {
                $this->generateTasks($tasks, $globalFuseau);
            }

            $journals = $calArray['VJOUNAL'] ?? [];

            $freesbusies = $calArray['VFREEBUSY'] ?? [];

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

    private function formatDate(array $dateIcs)
    {
        return DateTime::createFromFormat("YmdThis", $dateIcs["VALUE"], $dateIcs["TZID"] ?? null);
    }

    private function generateEvents(array $events, string $globalFuseau = "")
    {
        foreach ($events as $event) {
            $eventICS = new EventICS($this->entityManager);
            $eventICS->setUid($event['UID'] ?? "");
            $eventICS->setSubject($event['SUMMARY']);
            $eventICS->setDetails(array_key_exists('DESCRIPTION', $event) ? $event['DESCRIPTION'] : null);
            $eventICS->setStatus(array_key_exists('STATUS', $event) ? $event['STATUS'] : "NEEDS-ACTION");
           
            $eventICS->setTimezone($globalFuseau);
            $eventICS->setDateStart($this->formatDate($event['DTSTART']));
            $eventICS->setDateEnd($this->formatDate($event['DTEND']));
            $eventICS->setCreatedAt($this->formatDate($event['CREATED']));
            $eventICS->setLastupdatedAt($this->formatDate($event['LAST-MODIFIED']));
            /* -> détermination du type d'événement par jour ou sur durée en fonction de DTSTART */
            $eventICS->setAllday((int) $eventICS->getDateStart()->format("His") == 0);
            
            $location = array_key_exists('LOCATION', $event) ? $event['LOCATION'] : null;
            $geo = array_key_exists('GEO', $event) ? $event['GEO'] : null;
            $eventICS->setLocation($this->formatEventLocation($location, $geo));

            $organizer = array_key_exists('ORGANIZER', $event) ? $event['ORGANIZER'] : [];
            if ($organizer) {
                $eventOrganizer = $this->entityManager->getRepository(Organizer::class)
                    ->findOneBy(["email" => $organizer["MAILTO"]]);
                if (!$eventOrganizer && array_key_exists('CN', $organizer) && !empty($organizer['CN']))
                    $eventOrganizer = $this->entityManager->getRepository(Organizer::class)
                        ->findOneBy(['fullname' => $organizer['CN']]);
                if (!$eventOrganizer) {
                    $eventOrganizer = new Organizer();
                    $eventOrganizer->setEmail($organizer['MAILTO']);
                    $eventOrganizer->setFullname($organizer['CN']);
                    if (array_key_exists("DIR", $organizer)) $eventOrganizer->setDir($organizer["DIR"]);
                    if (array_key_exists("SENDBY", $organizer)) 
                        $eventOrganizer->setSendBy($organizer["SENDBY"]);
                    if (array_key_exists("LANGUAGE", $organizer))
                        $eventOrganizer->setLanguage($organizer["LANGUAGE"]);
                    $this->entityManager->persist($eventOrganizer);
                }
                $eventICS->setOrganizer($eventOrganizer);
            }

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

            if (array_key_exists("VALARM", $event)) {
                foreach ($event["VALARM"] as $alarm) {
                    $alarmEvt = new EventAlarm();
                    $alarmEvt->setByArray($alarm);
                    $eventICS->addAlarm($alarmEvt);
                }
            }

            if (array_key_exists('UID', $event)) {
                $calEvent = $this->entityManager->getRepository(CalEvent::class)->findOneBy(['uid' => $event['UID']]);
            }

            $calEvent = $eventICS->toCalEvent($calEvent);
            if (!$calEvent->getId()) $this->entityManager->persist($calEvent);
        }
        $this->entityManager->flush();
    }

    private function generateTasks(array $tasks, string $globalFuseau = "")
    {
        foreach ($tasks as $task) {
            $taskICS = new TaskICS($this->entityManager);
            $taskICS->setUid($task["UID"]);
            $taskICS->setDtStamp($this->formatDate($task["DTSTART"]));

            if (array_key_exists("CLASS", $task)) $taskICS->setClasses($task["CLASS"]);
            if (array_key_exists("COMPLETED", $task)) {
                $taskICS->setCompleted($this->formatDate($task["COMPLETED"]));
            }
            if (array_key_exists("CREATED", $task)) {
                $taskICS->setCreated($this->formatDate($task["CREATED"]));
            }
            if (array_key_exists("DESCRIPTION", $task)) $taskICS->setDescription($task["DESCRIPTION"]);
            if (array_key_exists("DTSTART", $task)) {
                $taskICS->setDtStamp($this->formatDate($task["DTSTART"]));
            }
                                
            $location = array_key_exists('LOCATION', $task) ? $task['LOCATION'] : null;
            $geo = array_key_exists('GEO', $task) ? $task['GEO'] : null;
            $taskICS->setLocation($this->formatEventLocation($location, $geo));

            if (array_key_exists("LAST-MODIFIED", $task)) {
                $taskICS->setLastModified($this->formatDate($task["LAST-MODIFIED"]));
            }

            $organizer = array_key_exists('ORGANIZER', $task) ? $task['ORGANIZER'] : [];
            if ($organizer) {
                $taskOrganizer = $this->entityManager->getRepository(Organizer::class)
                    ->findOneBy(["email" => $organizer["MAILTO"]]);
                if (!$taskOrganizer && array_key_exists('CN', $organizer) && !empty($organizer['CN']))
                    $taskOrganizer = $this->entityManager->getRepository(Organizer::class)
                        ->findOneBy(['fullname' => $organizer['CN']]);
                if (!$taskOrganizer) {
                    $taskOrganizer = new Organizer();
                    $taskOrganizer->setEmail($organizer['MAILTO']);
                    $taskOrganizer->setFullname($organizer['CN']);
                    if (array_key_exists("DIR", $organizer)) $taskOrganizer->setDir($organizer["DIR"]);
                    if (array_key_exists("SENDBY", $organizer)) 
                        $taskOrganizer->setSendBy($organizer["SENDBY"]);
                    if (array_key_exists("LANGUAGE", $organizer))
                        $taskOrganizer->setLanguage($organizer["LANGUAGE"]);
                    $this->entityManager->persist($taskOrganizer);
                }
                $taskICS->setOrganizer($taskOrganizer);
            }

            if (array_key_exists("PERCENT-COMPLETE", $task))
                $taskICS->setPercentComplete((int)$task["PERCENT-COMPLETE"]);
            if (array_key_exists("PRIORITY", $task))
                $taskICS->setPriority((int)$task["PRIORITY"]);

            if (array_key_exists("RECURRENCE-ID", $task)) {
                $recurrenceId = new TaskRecurrenceId();
                $recurrenceId->hydrateFromArray($task["RECURRENCE-ID"]);
                $taskICS->setRecurrenceId($recurrenceId);
            }

            $attendees = array_key_exists('ATTENDEE', $task) ? $task['ATTENDEE'] : [];
            if ($attendees) {
                foreach($attendees as $invite) {
                    $taskAttendee = $this->entityManager->getRepository(Attendee::class)
                        ->findOneBy(['email' => $invite['MAILTO']]);
                    if (!$taskAttendee && array_key_exists('CN', $invite) && !empty($invite['CN'])) 
                        $taskAttendee = $this->entityManager->getRepository(Attendee::class)
                            ->findOneBy(["fullaname" => $invite['CN']]);

                    // si la personne désignée n'existe pas dans Attendee : création
                    if (!$taskAttendee) {
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
                    $taskICS->addattendee($attendee);
                }
            }
        }
    }
}