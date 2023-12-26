<?php

namespace Celtic34fr\CalendarCore\Doctrine\Type;

use Celtic34fr\CalendarCore\Model\EventLocation;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EventLocationType extends Type
{
    const EVENT_LOCATION = 'event_location';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof EventLocation) {
            throw new \Exception('Only '.EventLocation::class.' object is supported.');
        }

        return json_encode($value->jsonSerialize());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        $data = json_decode($value, true);

        return EventLocation::createFromArray($data);
    }

    public function getName()
    {
        return self::EVENT_LOCATION;
    }
}