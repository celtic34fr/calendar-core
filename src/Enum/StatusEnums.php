<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum StatusEnums: string
{
    use EnumToArray;

    case NeedsAction    = 'NEEDS-ACTION';   // événement, journal, tache en attente de réponse du contact
    case Accepted       = 'ACCEPTED';       // événement, journal, tache acceptée
    case Declined       = 'DECLINED';       // événement, journal, tache refusée
    case Tentative      = "TENTATIVE";      // événement, tache tempotairement acceptée
    case Delegated      = "DELEGATED";      // événement, tache délégué
    case Completed      = "COMPLETED";      // tache terminée (complté avec la datede fin)
    case InProcess      = "IN-PROCESS";     // tache en cours de finalisation

    const VeventStatus = [
        'NEEDS-ACTION', 'ACCEPTED', 'DECLINED', "TENTATIVE", "DELEGATED"
    ];
    const VtodoStatus = [
        'NEEDS-ACTION', 'ACCEPTED', 'DECLINED', "TENTATIVE", "DELEGATED", "COMPLETED", "IN-PROCESS"
    ];
    const VjournalStatus =  [
        'NEEDS-ACTION', 'ACCEPTED', 'DECLINED'
    ];

    public function _toString(): string
    {
        return (string) $this->value;
    }

    public static function isValid(string $value): bool
    {
        $courrielValuesTab = array_column(self::cases(), 'value');
        return in_array($value, $courrielValuesTab);
    }

    public static function isValidVevent(string $value): bool
    {
        return in_array($value, self::VeventStatus);        
    }

    public static function isValidVtodo(string $value): bool
    {
        return in_array($value, self::VtodoStatus);
    }

    public static function isValidVjournal(string $value): bool
    {
        return in_array($value, self::VjournalStatus);
    }
}
