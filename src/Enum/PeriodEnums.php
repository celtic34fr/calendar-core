<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum PeriodEnums: string
{
    use EnumToArray;

    case Secondly   = "SECONDLY";   // à la seconde
    case Minutely   = "MINUTELY";   // à la minute
    case Hourly     = "HOURLY";     // à l'heure
    case Daily      = "DAILY";      // journalier
    case Weekly     = "WEEKLY";     // hebdomadaire
    case Monthly    = "MONTHLY";    // mensuel
    case Yearly     = "YEARLY";     // annuel

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