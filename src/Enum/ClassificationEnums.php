<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum ClassificationEnums: string
{
    use EnumToArray;

    case Prive = 'PR';          // événement privé
    case Public = 'PU';         // événement public
    case Confidentiel = 'CO';   // événement confidentiel

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
