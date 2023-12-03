<?php

namespace Celtic34fr\CalendarCore\Model;

use Celtic34fr\CalendarCore\Enum\PeriodEnums;
use Celtic34fr\CalendarCore\Enum\WeekDaysEnums;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class EventRepetition
{
    private string $period;         // by default by day : DAYLY
    private int $interval = 1;      // by default 1
    private ?Datetime $untilDate;
    private int $count = 1;         // by defaut 1 (if absent of the RRule)
    private array $byFreq = [];     // array where find frequency information
    private string $weekStartDay;   // first day of the week

    public function __construct()
    {
        $this->period = PeriodEnums::Daily->_toString();
        $this->weekStartDay = WeekDaysEnums::Monday->_toString();
    }

    public function setByRRule(array $rrule, string $fuseauHoraire)
    {
        $this->setPeriod($rrule["FREQ"]);
        if (array_key_exists("INTERVAL", $rrule)) $this->setInterval((int) $rrule["INTERVAL"]);
        if (array_key_exists("COUNT", $rrule)) $this->setCount((int)$rrule["COUNT"]);
        if (array_key_exists("WKST", $rrule)) $this->setWeekStartDay($rrule["WKST"]);

        /** integration of BY* componant */
        if (array_key_exists("BYSECOND", $rrule)) $this->setByFreqSecond($rrule["BYSECOND"]);
        if (array_key_exists("BYMINUTE", $rrule)) $this->setByFreqMinute($rrule["BYMINUTE"]);
        if (array_key_exists("BYHOUR", $rrule)) $this->setByFreqHour($rrule["BYHOUR"]);
        if (array_key_exists("BYDAY", $rrule)) $this->setByFreqDay($rrule["BYDAY"]);
        if (array_key_exists("BYMONTHDAY", $rrule)) $this->setByFreqMonthDay($rrule["BYMONTHDAY"]);
        if (array_key_exists("BYYEARDAY", $rrule)) $this->setByFreqYearDay($rrule["BYYEARDAY"]);
        if (array_key_exists("BYWEEKNO", $rrule)) $this->setByFreqWeekNo($rrule["BYWEEKNO"]);
        if (array_key_exists("BYMONTH", $rrule)) $this->setByFreqMonth($rrule["BYMONTH"]);
        if (array_key_exists("BYSETPOS", $rrule)) $this->setByFreqSetPos($rrule["BYSETPOS"]);
    
        if (array_key_exists('UNTIL', $rrule)) {
            $untilDate = $this->extractDate($rrule['UNTIL'], $fuseauHoraire);
            $this->setUntilDate($untilDate);
        }
}


    /**
     * get the Period of the Repetition
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }

    /**
     * set the Period of the Repetition
     * @param string $period
     * @return EventRepetition|bool
     */
    public function setPeriod(string $period): mixed
    {
        if (PeriodEnums::isValid($period)) {
            $this->period = $period;
            return $this;
        }
        return false;
    }

    /**
     * get the Interval between 3 occurance of the Event
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * Set the value of interval
     * @param integer $interval
     * @return EvantRepetition|bool
     */
    public function setInterval(int $interval): mixed
    {
        if ($interval > 0) {
            $this->interval = $interval;
            return $this;
        }
        return false;
    }

    /**
     * get the End Date Of the Repetition
     * @return DateTime|null
     */
    public function getUntilDate(): ?Datetime
    {
        return $this->untilDate;
    }

    /**
     * set the value of untilDate
     * @param DateTime|null $untilDate
     * @return EventRepetition
     */
    public function setUntilDate(?Datetime $untilDate): EventRepetition
    {
        $this->untilDate = $untilDate;
        return $this;
    }

    /**
     * get the Number of Repetition
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * set the Number of Repetition
     * @param int $count
     * @return EventRepetition|bool
     */
    public function setCount(int $count): mixed
    {
        if ($count > 0) {
            $this->count = $count;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of byFreq
     */
    public function getByFreq(): array
    {
        return $this->byFreq;
    }

    /**
     * Set the value of byFreq
     */
    public function setByFreq(array $byFreq): self
    {
        $this->byFreq = $byFreq;
        return $this;
    }

    public function setByFreqSecond(array $bySecond)
    {
        $this->byFreq["BYSECOND"] = $bySecond;
        return $this;
    }

    public function setByFreqMinute(array $byMinute)
    {
        $this->byFreq["BYMINUTE"] = $byMinute;
        return $this;
    }

    public function setByFreqHour(array $byHour)
    {
        $this->byFreq["BYHOUR"] = $byHour;
        return $this;
    }

    public function setByFreqDay(array $byDay)
    {
        $this->byFreq["BYDAY"] = $byDay;
        return $this;
    }

    public function setByFreqMonthDay(array $byMonthDay)
    {
        $this->byFreq["BYMONTHDAY"] = $byMonthDay;
        return $this;
    }

    public function setByFreqYearDay(array $byYearDay)
    {
        $this->byFreq["BYYEARDAY"] = $byYearDay;
        return $this;
    }

    public function setByFreqWeekNo(array $byWeekNo)
    {
        $this->byFreq["BYWEEKNO"] = $byWeekNo;
        return $this;
    }

    public function setByFreqMonth(array $byMonth)
    {
        $this->byFreq["BYMONTH"] = $byMonth;
        return $this;
    }

    public function setByFreqSetPos(array $bySetPos)
    {
        $this->byFreq["BYSETPOS"] = $bySetPos;
        return $this;
    }

    /**
     * Get the value of weekStartDay
     */
    public function getWeekStartDay(): string
    {
        return $this->weekStartDay;
    }

    /**
     * Set the value of weekStartDay
     */
    public function setWeekStartDay(string $weekStartDay): self
    {
        if (WeekDaysEnums::isValid($weekStartDay)) {
            $this->weekStartDay = $weekStartDay;
            return $this;
        }
        return false;
    }

    /**
     * extract date from line in ICS file
     * @param string|array $eventDate
     * @param string $fuseau
     * @return DateTime
     */
    private function extractDate(mixed $eventDate, string $fuseau): DateTimeImmutable
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
            $dtStart = DateTimeImmutable::createFromFormat('YmdHis', $value, $timezone);
        } else { // heure du système
            $dtStart = DateTimeImmutable::createFromFormat('YmdHis', $value);
        }
        return $dtStart;
    }


    private function extractDateMutable(mixed $eventDate, string $fuseau)
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