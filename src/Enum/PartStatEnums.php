<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum PartStatEnums: string
{
    use EnumToArray;

    case NeedsAction = 'NEEDS-ACTION';      // ALL CASE, DEFAULT
    case Accepted = 'ACCEPTED';             // ALL CASE
    case Declined = 'DECLINED';             // ALL CASE
    case Tentative = 'TENTATIVE';           // VEVENT & VTODO
    case Delegated = 'DELEGATED';           // VEVENT & VTODO
    case Completed = 'COMPLETED';           // VTODO
    case InProcess = 'IN-PROCESS';          // VTODO

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