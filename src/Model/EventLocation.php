<?php

namespace Celtic34fr\CalendarCore\Model;

class EventLocation
{
    private ?string $location;
    private ?float $latitude;
    private ?float $longitude;

    public function __construct(array $tabLocation = null)
    {
        if ($tabLocation) $this->setByArray($tabLocation);
    }

    public function setByArray(array $location)
    {
        $this->location = array_key_exists('LOCATION', $location) ? $location['LOCATION'] : null;
        $this->latitude = array_key_exists('LATITUDE', $location) ? $location['LATITUDE'] : null;
        $this->longitude = array_key_exists('LONGITUDE', $location) ? $location['LONGITUDE'] : null;
    }

    /**
     * Get the value of location
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Set the value of location
     * @param string|null $location
     * @return EventLocation
     */
    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of latitude
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * Set the value of latitude
     * @param float|null $latitude
     * @return EventLocation
     */
    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get the value of longitude
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * Set the value of longitude
     * @param float|null $longitude
     * @return EventLocation
     */
    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}