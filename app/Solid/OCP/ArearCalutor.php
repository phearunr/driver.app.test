<?php

namespace App\Solid\OCP;

class ArearCalutor
{
    public function totalArea($rectangles)
    {
        $area = 0;
        foreach($rectangles as $rectangle){
           $area += 10* 20;
        }
        return $area;
    }

}
