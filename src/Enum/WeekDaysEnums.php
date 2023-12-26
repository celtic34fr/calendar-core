<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum WeekDaysEnums: string
{
    use EnumToArray;

    // MO, TU, WE, TH, FR, SA et SU
    case Monday     = "MO"; // Lundi
    case Tuesday    = "TU"; // Mardi
    case Wednesday  = "WE"; // Mercredi
    case Thursday   = "TH"; // Jeudi
    case Friday     = "FR"; // Vendredi
    case Saturday   = "SA"; // Samedi
    case Sunday     = "SU"; // Dimanche

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