<?php

namespace Celtic34fr\CalendarCore\Traits\Model;

use Celtic34fr\CalendarCore\Model\EventLocation;

trait FormatLocationTrait
{
    private function formatLocation(string $location = null, array $geo = null): EventLocation
    {
        $eventLocation = new EventLocation();
        if (!$location && !$geo) return false;
        if ($location) $eventLocation->setLocation($location);
        if ($geo) {
            $eventLocation->setLatitude((float) $geo['LATITUDE']);
            $eventLocation->setLongitude((float) $geo['LONGITUDE']);
        }
        return $eventLocation;
    }

}