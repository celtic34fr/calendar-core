<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum StatusEnums: string
{
    use EnumToArray;

    case WaitResponse = 'WRES';   // demande de rendez-vous en attente de réponse du contact
    case Accepted     = 'ACCP';    // demande accptée
    case Refused      = 'REFU';   // demande refusée
    case Reported     = "REPT";   // demande reportée sur demande contact

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
