<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum PeriodEnums: string
{
    use EnumToArray;

    case Secondly   = "SECONDLY";
    case Minutely   = "MINUTELY";
    case Hourly     = "HOURLY";
    case Daily      = "DAILY";
    case Weekly     = "WEEKLY";
    case Monthly    = "MONTHLY";
    case Yearly     = "YEARLY";

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