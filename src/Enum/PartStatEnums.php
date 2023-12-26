<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum PartStatEnums: string
{
    use EnumToArray;

    case NeedsAction = 'NEEDS-ACTION';      // ALL CASE, DEFAULT : besoin d'une action
    case Accepted = 'ACCEPTED';             // ALL CASE          : status accepté
    case Declined = 'DECLINED';             // ALL CASE          : status refusé
    case Tentative = 'TENTATIVE';           // VEVENT & VTODO    : en attente action
    case Delegated = 'DELEGATED';           // VEVENT & VTODO    : status dékégué
    case Completed = 'COMPLETED';           // VTODO             : tache terminée
    case InProcess = 'IN-PROCESS';          // VTODO             : tache encours d'exécution

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