<?php

namespace Celtic34fr\CalendarCore\Traits\Model;

use DateTime;
use DateTimeZone;

trait ExtractDateTrait
{
    
    /**
     * extract date from line in ICS file
     * @param string|array $eventDate
     * @param string $fuseau
     * @return DateTime
     */
    private function extractDate(mixed $eventDate, string $fuseau): DateTime
    {
        /**
         * format possible des dates :
         * 2014-06-12T20:00:00
         * 20140619T072445Z
         * array => VALUE / value => valeur à traiter : 20150211 / 20131220T130000
         * 
         * 1403086496 (timerstamp ?) ds DAYLIGHT bloc
         * 19700329T020000 das DAYLIGHT / STANDARD bloc
         */
        if (is_array($eventDate)) {
            /** recherche/extraction fuseau horaire local si trouvé sinon fuseau horaire global */
            $fuseau = array_key_exists('TZID', $eventDate) ? $eventDate['TZID'] : $fuseau;
            $value = $eventDate['VALUE'];
        } else {
            $value = $eventDate;
        }
        /** tranformation de la date pour obtenir un format : (YmdHis) */
        $value = trim($value);
        $value = str_replace(" ", "", $value);
        $value = str_replace("-", "", $value);
        $value = str_replace(":", "", $value);
        $value = str_replace("T", "", $value);
        $value = str_replace("Z", "", $value);
        if ($fuseau) { // si $fuseau non vide => gestion du fuseau horaine local / global
            $timezone = new DateTimeZone($fuseau);
            $dtStart = DateTime::createFromFormat('YmdHis', $value, $timezone);
        } else { // heure du système
            $dtStart = DateTime::createFromFormat('YmdHis', $value);
        }
        return $dtStart;
    }

    /**
     * @param string|array $eventDate
     * @param string $fuseau
     * @return DateTime
     */
    private function extractDateMutable(mixed $eventDate, string $fuseau): DateTime
    {
        if (empty($fuseau)) {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($this->extractDate($eventDate, $fuseau)->getTimestamp());
        } else {
            $dateTime = new DateTime('', $this->extractDate($eventDate, $fuseau)->getTimezone());
            $dateTime->setTimestamp($this->extractDate($eventDate, $fuseau)->getTimestamp());
        }
        return $dateTime;
    }

}