<?php

namespace Celtic34fr\CalendarCore\Doctrine\Type;

use Celtic34fr\CalendarCore\Model\EventRepetition;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EventRepetitionType extends Type
{
    const EVENT_REPETITION = 'event_repetition';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof EventRepetition) {
            throw new \Exception('Only '.EventRepetition::class.' object is supported.');
        }

        return json_encode($value->jsonSerialize());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        $data = json_decode($value, true);

        return EventRepetition::createFromArray($data);
    }

    public function getName()
    {
        return self::EVENT_REPETITION;
    }
}