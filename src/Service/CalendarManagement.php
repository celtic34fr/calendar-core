<?php

namespace Celtic34fr\CalendarCore\Service;

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
                   
                    $eventICS->setFuseauHoraire($globalFuseau);
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
                            $calAttendee = $this->entityManager->getRepository(Clientele::class)
                                ->findOneBy(['courriel' => $invite['MAILTO']]);
                            if (!$calAttendee && array_key_exists('CN', $invite) && !empty($invite['CN'])) 
                                $calAttendee = $this->entityManager->getRepository(CliInfos::class)
                                    ->findFullname($invite['CN']);

                            // si la personne désignée n'existe pas dans CliInfo/Clientele : création en prospect
                            if (!$calAttendee) {
                                $clientele = new Clientele();
                                $clientele->setCourriel($invite['MAILTO']);
                                $clientele->setType(CustomerEnums::Prospect->_toString());
                                $this->entityManager->persist($clientele);

                                $calAttendee = new CliInfos();
                                $calAttendee->setClient($clientele);
                                if (!array_key_exists('CN', $invite) || empty($invite['CN'])) {
                                    $calAttendee->setNom(uniqid("Prospect"));
                                } else {
                                    $names = explode(' ', $invite['CN']);
                                    if (empty($names[0])) {
                                        $calAttendee->setNom(uniqid("Prospect"));
                                    } else {
                                        $calAttendee->setNom($names[0]);
                                        $calAttendee->setPrenom($names[1]);
                                    }
                                }
                                $this->entityManager->persist($calAttendee);
                                $clientele->addCliInfos($calAttendee);                                
                            } else { // la personne existe => on a trouvé son email.
                                if ($calAttendee instanceof CliInfos) {
                                    $calAttendee = $calAttendee->getClient();
                                } else {
                                    /** @var Clientele $calAttendee */
                                    if (!$calAttendee->isCliInfo(['fullname' => $invite['CN']])) {
                                        $cliInfo = new CliInfos();
                                        $cliInfo->setClient($calAttendee);
                                        $names = explode(' ', $invite['CN']);
                                        if (empty($names[0])) {
                                            $cliInfo->setNom(uniqid("Prospect"));
                                        } else {
                                            $cliInfo->setNom($names[0]);
                                            $cliInfo->setPrenom($names[1]);
                                        }
                                        $this->entityManager->persist($cliInfo);
                                        $calAttendee->addCliInfos($cliInfo);
                                    }
                                }
                            }
                            $eventICS->addInvite($calAttendee);
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