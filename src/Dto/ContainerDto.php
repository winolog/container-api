<?php

namespace winolog\ContainerApiClient\Dto;

class ContainerDto
{
    public ?string $container = null;
    public ?int $container_year = null;
    public ?int $cooler = null;
    public ?int $cooler_model = null;
    public ?int $cooler_year = null;
    public ?int $temp_admission = null;
    public ?int $type = null;
    public ?int $size = null;
    public ?int $special = null;
    public ?int $capacity = null;
    public ?int $tare = null;
    public ?int $price = null;
    public ?int $container_quality_id = null;
    public ?int $terminal_id = null;

    public function toArray(): array
    {
        return [
            'container' => $this->container,
            'container_year' => $this->container_year,
            'cooler' => $this->cooler,
            'cooler_model' => $this->cooler_model,
            'cooler_year' => $this->cooler_year,
            'temp_admission' => $this->temp_admission,
            'type' => $this->type,
            'size' => $this->size,
            'special' => $this->special,
            'capacity' => $this->capacity,
            'tare' => $this->tare,
            'price' => $this->price,
            'container_quality_id' => $this->container_quality_id,
            'terminal_id' => $this->terminal_id,
        ];
    }
}