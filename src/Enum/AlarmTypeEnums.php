<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum AlarmTypeEnums: string
{
    use EnumToArray;

    case Audio      = "AUDIO";
    case Display    = "DISPLAY";
    case Email      = "EMAIL";

    public function _toString(): string
    {
        return (string) $this->value;
    }

    public static function isValid(string $value): bool
    {
        $courrielValuesTab = array_column(self::cases(), 'value');
        return in_array($value, $courrielValuesTab);
    }
}