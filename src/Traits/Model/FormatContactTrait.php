<?php

namespace Celtic34fr\CalendarCore\Traits\Model;

use Celtic34fr\CalendarCore\Entity\Contact;

trait FormatContactTrait
{
    public function formatContact(array $contact): Contact
    {
        $fbContact = $this->entityManager->getRepository(Contact::class)
        ->findOneBy(["email" => $contact["MAILTO"]]);
        if (!$fbContact && array_key_exists('CN', $contact) && !empty($contact['CN']))
            $fbContact = $this->entityManager->getRepository(Contact::class)
                ->findOneBy(['fullname' => $contact['CN']]);
        if (!$fbContact) {
            $fbContact = new Contact();
            $fbContact->setEmail($contact['MAILTO']);
            $fbContact->setFullname($contact['CN']);
            if (array_key_exists("ALTREP", $contact)) $fbContact->setAltrep($contact["ALTREP"]);
            if (array_key_exists("LANGUAGE", $contact))
                $fbContact->setLanguage($contact["LANGUAGE"]);
        }
        $this->entityManager->persist($fbContact);
        return $fbContact;
    }
}