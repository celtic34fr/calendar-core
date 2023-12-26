<?php

namespace Celtic34fr\CalendarCore\EntityRedefine;

use Celtic34fr\CalendarCore\Entity\Parameter;
use DateTimeImmutable;

/**
 * class ParameterCalEvent redefines class Parameter for ParemeterList 'SysCalNature'
 * 
 * nature           : code de la nature d'événement
 * description      : description fonctionnalité
 * backgroundColor  : couleur de fond d'affichage
 * borderColor      : coukeur de bordure d'affichage
 * textColor        : couleur d'affichage du texte
 */
class ParameterCalEvent
{
    const HEADER = [
        'name', 'description', 'backgroundColor', 'borderColor', 'textColor'
    ];
    const PARAM_CLE = "SysCalNature";

    private array $values = [];

    public function __construct(private ?Parameter $parameter = null)
    {
        if ($parameter) {
            $this->values = $this->array_combine(self::HEADER, $this->getValues());
        } else {
            $this->parameter = new Parameter();
            $this->parameter->setCle(self::PARAM_CLE);
        }
    }

    public function getParameter(): Parameter
    {
        return $this->parameter;
    }

    /**
     * Set the value of parameter
     * @return  self
     */
    public function setParameter(Parameter $parameter)
    {
        $this->parameter = $parameter;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->parameter->getId();
    }

    public function getOrd(): mixed
    {
        return $this->parameter->getOrd() ?? false;
    }

    public function setOrd(int $ord): self
    {
        $this->parameter->setOrd($ord);
        return $this;
    }

    public function getUpdatedAt(): mixed
    {
        return $this->parameter->getUpdatedAt() ?? false;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->parameter->setUpdatedAt($updatedAt);
        return $this;
    }

    public function getValues(): array
    {
        $datas = $this->parameter->getValeur() ?? [];
        if ($datas) {
            $datas = explode("|", $datas);
            return $this->array_combine(self::HEADER, $datas);
        }
        return [];
    }

    public function setValues(array $values): self
    {
        $chaine = implode('|', $values);
        $this->parameter = $this->parameter->setValeur($chaine);
        return $this;
    }

    public function getItem(string $key): mixed
    {
        if (!array_key_exists($key, self::HEADER)) return false;
        return $this->getValues()[$key];
    }

    public function setItem(string $key, string $val): mixed
    {
        if (!array_key_exists($key, self::HEADER)) return false;
        $datas = $this->getValues();
        $datas[$key] = $val;
        return $this->setValues($datas);
    }

    public function getName(): mixed
    {
        $valuesAR = $this->getValues();
        return array_key_exists('name', $valuesAR) ? $valuesAR['name'] : null;
    }

    public function setName(string $name): mixed
    {
        $datas = $this->getValues();
        $datas['name'] = $name;
        $this->setValues($datas);
        return $this;
    }

    public function getDescription(): mixed
    {
        $valuesAR = $this->getValues();
        return array_key_exists('description', $valuesAR) ? $valuesAR()['description'] : null;
    }

    public function setDescription(string $description): mixed
    {
        $datas = $this->getValues();
        $datas['description'] = $description;
        $this->setValues($datas);
        return $this;
    }

    public function getBackgroundColor(): string
    {
        $valuesAR = $this->getValues();
        return array_key_exists('backgroundColor', $valuesAR) ? $valuesAR['backgroundColor'] : null;
    }

    public function setBackgroundColor(string $backgroundColor)
    {
        $datas = $this->getValues();
        $datas['backgroundColor'] = $backgroundColor;
        $this->setValues($datas);
        return $this;
    }

    public function getBorderColor(): string
    {
        $valuesAR = $this->getValues();
        return array_key_exists('borderColor', $valuesAR) ? $valuesAR['borderColor'] : null;
    }

    public function setBorderColor(string $borderColor)
    {
        $datas = $this->getValues();
        $datas['borderColor'] = $borderColor;
        $this->setValues($datas);
        return $this;
    }

    public function getTextColor(): string
    {
        $valuesAR = $this->getValues();
        return array_key_exists('textColor', $valuesAR) ? $valuesAR['textColor'] : null;
    }

    public function setTextColor(string $textColor)
    {
        $datas = $this->getValues();
        $datas['textColor'] = $textColor;
        $this->setValues($datas);
        return $this;
    }

    private function array_combine(array $headers, array $values)
    {
        $maxOrd = max (sizeof($headers), sizeof($values));
        $newArray = [];
        for ($idx =0; $idx < $maxOrd; $idx++) {
            $header = array_key_exists($idx, $headers) ? $headers[$idx] : $idx;
            $value = array_key_exists($idx, $values) ? $values[$idx] : null;
            $newArray[$header] = $value;
        }
        return $newArray;
    }
}
