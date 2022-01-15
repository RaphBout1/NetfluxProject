<?php
namespace App\Service;
use Doctrine\ORM\EntityManager;
use App\Entity\Episode;
use App\Entity\User;
use App\Entity\Series;
use App\Entity\Season;


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


### permet d'initialiser les moyennes
        