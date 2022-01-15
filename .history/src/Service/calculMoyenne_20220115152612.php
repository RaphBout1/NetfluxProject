<?php
namespace App\Service;
use Doctrine\ORM\EntityManager;
use App\Entity\Rating;
use App\Entity\Series;


class calculMoyenne
{

    

    public function __construct()
    {
       
    }

    public function setMoyenne(EntityManager $entityManager)
    {
        $series = $entityManager
        ->getRepository(Series::class)
        ->findAll();
    

    foreach($series as $serie){
        $ratings = $entityManager
        ->getRepository(Rating::class)
        ->findBy(
            ['series' => $serie],
        );
        $moyenne = 0;
        $nbRating = 0;
        foreach($ratings as $rat){
             $moyenne = $moyenne + $rat->getValue();
             $nbRating ++;

        }
        if($nbRating!=0){
            $moyenne = $moyenne/$nbRating;
            $moyenne = number_format($moyenne,1);
            $serie->setnoteUser($moyenne);
        }
        

    }   
    }



}


        