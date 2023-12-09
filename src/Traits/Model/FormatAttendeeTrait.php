<?php

namespace Celtic34fr\CalendarCore\Traits\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;

trait FormatAttendeeTrait
{
    
    private function formatAttendee(array $attendee): Attendee
    {
        $attendeeDb = new Attendee();

        /** recherche de la personne dans CliInfos / Clientele si existe */
        $calAttendee = $this->entityManager->getRepository(Attendee::class)
        ->findOneBy(['courriel' => $attendee['MAILTO']]);
        if (!$calAttendee && array_key_exists('CN', $attendee) && !empty($attendee['CN'])) 
            $calAttendee = $this->entityManager->getRepository(Attendee::class)
            ->findOneBy(["fullname" => $attendee['CN']]);

        // si la personne désignée n'existe pas dans CliInfo/Clientele : création en prospect
        if (!$calAttendee) {
            $clientele = new Attendee();
            $clientele->setEmail($attendee['MAILTO']);
            $this->entityManager->persist($clientele);

            if (!array_key_exists('CN', $attendee) || empty($attendee['CN'])) {
                $attendeeDb->setFullname(uniqid("Prospect"));
            }
            $this->entityManager->persist($attendeeDb);
        }
        $this->entityManager->flush();
        return $attendeeDb;
    }

}