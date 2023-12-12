<?php

namespace Celtic34fr\CalendarCore\Traits\Model;

use Celtic34fr\CalendarCore\Entity\Attendee;

trait FormatAttendeeTrait
{
    
    private function formatAttendee(array $attendee): Attendee
    {
        $attendeeDb = new Attendee();

        $calAttendee = null;
        /** recherche de la personne dans CliInfos / Clientele si existe */
        if (array_key_exists('MAILTO', $attendee) && !empty($attendee['MAILTO'])) 
            $calAttendee = $this->entityManager->getRepository(Attendee::class)
            ->findOneBy(['email' => $attendee['MAILTO']]);
        if (!$calAttendee && array_key_exists('CN', $attendee) && !empty($attendee['CN'])) 
            $calAttendee = $this->entityManager->getRepository(Attendee::class)
            ->findOneBy(["fullname" => $attendee['CN']]);

        // si la personne désignée n'existe pas dans CliInfo/Clientele : création en prospect
        if (!$calAttendee) {
            $attendeeDb->setEmail($attendee['MAILTO']);
            if (!array_key_exists('CN', $attendee) || empty($attendee['CN'])) {
                $attendeeDb->setFullname(uniqid("Prospect"));
            } else {
                $attendeeDb->setFullname($attendee['CN']);
            }
            $this->entityManager->persist($attendeeDb);
        }
        $this->entityManager->flush();
        return $attendeeDb;
    }

}
