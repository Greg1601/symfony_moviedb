<?php

namespace AppBundle\Service;

class Slugger
{   
    private $isLower;

    public function __construct($sluggerToLower = true)
    {
        $this->isLower = $sluggerToLower;
    }
    
    public function slugify($stringToConvert){

        //si mon parametre de service passé a mon constructeur est true alors je met ma chaine en minuscule
        if($this->isLower){
            $stringToConvert = strtolower($stringToConvert);
        }

        $convertedString = preg_replace( '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '-', trim(strip_tags($stringToConvert)));
        
        return $convertedString;
    }
}