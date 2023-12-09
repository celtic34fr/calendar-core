<?php

namespace Celtic34fr\CalendarCore\Traits\Model;

use Celtic34fr\CalendarCore\Entity\Organizer;

trait FormatOrganizerTrait
{
    public function formatOrganizer(array $organizerArray): Organizer
    {
        $taskOrganizer = $this->entityManager->getRepository(Organizer::class)
            ->findOneBy(["email" => $organizerArray["MAILTO"]]);
        if (!$taskOrganizer && array_key_exists('CN', $organizerArray) && !empty($organizer['CN']))
            $taskOrganizer = $this->entityManager->getRepository(Organizer::class)
                ->findOneBy(['fullname' => $organizer['CN']]);
        if (!$taskOrganizer) {
            $taskOrganizer = new Organizer();
            $taskOrganizer->setEmail($organizerArray['MAILTO']);
            $taskOrganizer->setFullname($organizerArray['CN']);
            if (array_key_exists("DIR", $organizerArray)) $taskOrganizer->setDir($organizerArray["DIR"]);
            if (array_key_exists("SENDBY", $organizerArray)) 
                $taskOrganizer->setSendBy($organizerArray["SENDBY"]);
            if (array_key_exists("LANGUAGE", $organizerArray))
                $taskOrganizer->setLanguage($organizerArray["LANGUAGE"]);
        }
        $this->entityManager->persist($taskOrganizer);
        return $taskOrganizer;
    }
}