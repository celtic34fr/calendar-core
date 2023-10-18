<?php

namespace Celtic34fr\CalendarCore\FormEntity;

class CalEventItem
{
    private ?int $id = null;
    private ?string $cle;
    private ?string $fonction;
    private ?string $background;
    private ?string $border;
    private ?string $text;

    /**
     * @param string $jsonStr
     * @return CalEventItem|bool
     */ 
    public function hydrateFromJson(string $jsonStr):mixed
    {
        $jsonArray = json_decode($jsonStr, true);
        if (!empty($jsonStr) && is_string($jsonStr) 
            && is_array($jsonArray) && !empty($jsonArray) 
            && json_last_error() == 0) {
            foreach ($jsonArray as $key => $val) {
                $method = "set" . ucfirst($key);
                $this->$method($val);
            }
            return $this;
        }
        return false;
    }

    public function hydratefromArray(array $datas): self
    {
        foreach ($datas as $key => $data) {
            $method = "set" . ucfirst($key);
            $this->$method($data);
        }
        return $this;
    }

    /**
     * @return bool|string
     */ 
    public function getValeur(): bool|string
    {
        $jsonArray = [
            'cle' => $this->getCle(),
            'fonction' => $this->getFonction(),
            'background' => $this->getBackground(),
            'border' => $this->getBorder(),
            'text' => $this->getText()
        ];
        return json_encode($jsonArray);
    }

    /**
     * @return null|int
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $is
     * @return CalEventItem
     */ 
    public function setId(?int $id) : self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return null|string
     */ 
    public function getCle(): ?string
    {
        return $this->cle;
    }

    /**
     * @param string[null] $cle
     * @return CalEventItem
     */ 
    public function setCle(?string $cle): self
    {
        $this->cle = $cle;
        return $this;
    }

    /**
     * @return null|string
     */ 
    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    /**
     * @param string|null $fonction
     * @return CalEventItem
     */ 
    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;
        return $this;
    }

    /**
     * @return null|string
     */ 
    public function getBackground(): ?string
    {
        return $this->background;
    }

    /**
     * @param string|null $background
     * @return CalEventItem|bool
     */ 
    public function setBackground(?string $background): mixed
    {
        if ($this->validColorHexa($background)) {
            $this->background = $background;
            return $this;
        }
        return false;
    }

    /**
     * @return null|string
     */ 
    public function getBorder(): ?string
    {
        return $this->border;
    }

    /**
     * @param string|null $border
     * @return CalEventItem|bool
     */ 
    public function setBorder(?string $border): mixed
    {
        if ($this->validColorHexa($border)) {
            $this->border = $border;
            return $this;
        }
        return false;
    }

    /**
     * @return null|string
     */ 
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string[null] $text
     * @return CalEventItem|bool
     */ 
    public function setText(?string $text): mixed
    {
        if ($this->validColorHexa($text)) {
            $this->text = $text;
            return $this;
        }
        return false;
    }

    private function validColorHexa(string $color_str): bool
    {
        /** validation de la chaîne de caractères
         *      -> commencer par '#'
         *      -> par groupe de 2 caractères : valeur hexadéciaml de 0 à 255 : 00 à FF
         */
        if (!$color_str) return false;
        if (substr($color_str, 0, 1) != "#") return false;
        if (!ctype_xdigit(substr($color_str, 1))) return false;
        return true;
    }
}
