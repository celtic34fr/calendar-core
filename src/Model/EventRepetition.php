<?php

namespace Celtic34fr\CalendarCore\Model;

use DateTime;

class EventRepetition
{
    private int $period = 4;        // by default by day : DAYLY
    private int $interval = 1;      // by default 1
    private ?Datetime $untilDate;
    private int $count = 1;         // by defaut 1 (if absent of the RRule)

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
     * @param int $period
     * @return EventRepetition|bool
     */
    public function setPeriod(int $period): mixed
    {
        if ($period > 0 && $period < 8) {
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
}