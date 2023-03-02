<?php

namespace App\Services\Geolcation;

use App\Services\Map\Map;
use App\Services\Salellite\Salellite;

class Geolocation
{
    private $map;
    private $satellite;

    public function __construct(Map $map, Salellite $salellite)
    {
       $this->map = $map;
       $this->satellite = $salellite;
    }

    public function search(string $name){

        return $this->satellite->pinpoint($this->map->findAddress($name));
    }
}

