<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum WeekDaysEnums: string
{
    use EnumToArray;

    // MO, TU, WE, TH, FR, SA et SU
    case Monday     = "MO";
    case Tuesday    = "TU";
    case Wednesday  = "WE";
    case Thursday   = "TH";
    case Friday     = "FR";
    case Saturday   = "SA";
    case Sunday     = "SU";

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