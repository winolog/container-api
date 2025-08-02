<?php

namespace winolog\ContainerApiClient\Dto;

class ContainerDto
{
    public string $container;
    public int $container_year;
    public int $cooler;
    public int $cooler_model;
    public int $cooler_year;
    public int $temp_admission;
    public int $type;
    public int $size;
    public int $special;
    public int $capacity;
    public int $tare;
    public int $price;
    public int $container_quality_id;
    public int $terminal_id;

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
