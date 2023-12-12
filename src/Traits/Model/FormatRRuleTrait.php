<?php

namespace Celtic34fr\CalendarCore\Traits\Model;

use Celtic34fr\CalendarCore\Model\EventRepetition;

trait FormatRRuleTrait
{
    public function formatRRule(array $rrule): EventRepetition
    {
        $rruleItem = new EventRepetition();
        $rruleItem->setPeriod($rrule["FREQ"]);
        if (array_key_exists("INTERVAL", $rrule)) $rruleItem->setInterval((int) $rrule["INTERVAL"]);
        if (array_key_exists("COUNT", $rrule)) $rruleItem->setCount($rrule["COUNT"]);
        if (array_key_exists("WKST", $rrule)) $rruleItem->setWeekStartDay($rrule["WKST"]);
        if (array_key_exists("UNTIL", $rrule)) $rruleItem->setUntilDate($rrule["UNTIL"]);

        /** integration of BY* componant */
        if (array_key_exists("BYSECOND", $rrule)) $rruleItem->setByFreqSecond($rrule["BYSECOND"]);
        if (array_key_exists("BYMINUTE", $rrule)) $rruleItem->setByFreqMinute($rrule["BYMINUTE"]);
        if (array_key_exists("BYHOUR", $rrule)) $rruleItem->setByFreqHour($rrule["BYHOUR"]);
        if (array_key_exists("BYDAY", $rrule)) $rruleItem->setByFreqDay($rrule["BYDAY"]);
        if (array_key_exists("BYMONTHDAY", $rrule)) $rruleItem->setByFreqMonthDay($rrule["BYMONTHDAY"]);
        if (array_key_exists("BYYEARDAY", $rrule)) $rruleItem->setByFreqYearDay($rrule["BYYEARDAY"]);
        if (array_key_exists("BYWEEKNO", $rrule)) $rruleItem->setByFreqWeekNo($rrule["BYWEEKNO"]);
        if (array_key_exists("BYMONTH", $rrule)) $rruleItem->setByFreqMonth($rrule["BYMONTH"]);
        if (array_key_exists("BYSETPOS", $rrule)) $rruleItem->setByFreqSetPos($rrule["BYSETPOS"]);

        return $rruleItem;
    }
}