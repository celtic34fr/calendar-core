<?php

namespace Celtic34fr\CalendarCore\Service;

use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Entity\CalFreeBusy;
use Celtic34fr\CalendarCore\Entity\CalTask;
use Celtic34fr\CalendarCore\Model\EventICS;
use Celtic34fr\CalendarCore\Model\FreeBusyICS;
use Celtic34fr\CalendarCore\Model\JournalICS;
use Celtic34fr\CalendarCore\Model\TaskICS;
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

    /**
     * @param string $dirpath
     * @param string $filename
     * @return bool
     */
    public function restore(string $dirpath, string $filename): bool
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

            $journals = $calArray['VJOURNAL'] ?? [];
            if ($journals) {
                $this->generateJournal($journals);
            }

            $freesbusies = $calArray['VFREEBUSY'] ?? [];
            if ($freesbusies) {
                $this->generateFreeBusy($freesbusies);
            }

            return true;
        }
        return false;
    }

    /**
     * @param array $dateIcs
     * @return DateTime
     */
    private function formatDate(array $dateIcs): DateTime
    {
        return DateTime::createFromFormat("YmdThis", $dateIcs["VALUE"], $dateIcs["TZID"] ?? null);
    }

    /**
     * @param array $events
     * @return void
     */
    private function generateEvents(array $events): void
    {
        foreach ($events as $event) {
            if (array_key_exists("UID", $event)) 
                $eventBD = $this->entityManager->getRepository(CalEvent::class)->findOneBy(["uid" => $event['UID']]);
            if (!$eventBD) {
                $eventICS = new EventICS($this->entityManager);
            } else {
                $eventICS = new EventICS($this->entityManager, $eventBD);
            } 
            
            $eventICS->buildFromArray($event);
        }
        $this->entityManager->flush();
    }

    /**
     * @param array $tasks
     * @return void
     */
    private function generateTasks(array $tasks): void
    {
        foreach ($tasks as $task) {
            if (array_key_exists("UID", $task)) 
                $taskBD = $this->entityManager->getRepository(CalTask::class)->findOneBy(["uid" => $task['UID']]);
            if (!$taskBD) {
                $taskICS = new TaskICS($this->entityManager);
            } else {
                $taskICS = new TaskICS($this->entityManager, $taskBD);
            } 
            
            $taskICS->buildFromArray($task);
        }
        $this->entityManager->flush();
    }

    /**
     * @param array $journals
     * @return void
     */
    private function generateJournal(array $journals): void
    {
        foreach ($journals as $journal) {
            if (array_key_exists("UID", $journal)) 
                $journalBD = $this->entityManager->getRepository(CalTask::class)->findOneBy(["uid" => $journal['UID']]);
            if (!$journalBD) {
                $journalICS = new JournalICS($this->entityManager);
            } else {
                $journalICS = new JournalICS($this->entityManager, $journalBD);
            } 
            
            $journalICS->buildFromArray($journal);
        }
        $this->entityManager->flush();
    }

    /**
     * @param array $freesbusies
     * @return void
     */
    private function generateFreeBusy(array $freesbusies): void
    {
        foreach ($freesbusies as $freebusy) {
            if (array_key_exists("UID", $freebusy)) 
                $freebusyBD = $this->entityManager->getRepository(CalFreeBusy::class)->findOneBy(["uid" => $freebusy['UID']]);
            if (!$freebusyBD) {
                $freebusyICS = new FreeBusyICS($this->entityManager);
            } else {
                $freebusyICS = new FreeBusyICS($this->entityManager, $freebusyBD);
            } 
            
            $freebusyICS->buildFromArray($freebusy);
        }
        $this->entityManager->flush();
    }
}