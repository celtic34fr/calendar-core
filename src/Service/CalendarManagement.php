<?php

namespace Celtic34fr\CalendarCore\Service;

use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Entity\CalTask;
use Celtic34fr\CalendarCore\Model\EventICS;
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
            if ($journals) {
                $this->generateJournal($journals);
            }

            $freesbusies = $calArray['VFREEBUSY'] ?? [];

            return true;
        }
        return false;
    }

    private function formatDate(array $dateIcs)
    {
        return DateTime::createFromFormat("YmdThis", $dateIcs["VALUE"], $dateIcs["TZID"] ?? null);
    }

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
}