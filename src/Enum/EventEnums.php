<?php

namespace Celtic34fr\CalendarCore\Enum;

use Celtic34fr\CalendarCore\Traits\EnumToArray;

enum EventEnums: string
{
    use EnumToArray;

    case ContactTel     = 'CTEL';   // demande de rendez-vous via demande de contact
    case CongesAbs      = 'ABCS';   // congé, absence
    case Intervention   = 'INTR';   // intervention (chez client, dans locaux ...)
    case Meeting        = "MEET";   // réunion de travail, meeting divers ...

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
