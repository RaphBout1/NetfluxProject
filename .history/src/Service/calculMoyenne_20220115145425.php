<?php
namespace App\Service;
use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class CallApiService
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getApi(string $value)
    {
        $response = $this->client->request(
            'GET',
            'http://www.omdbapi.com/?i='.$value.'&apikey=8d2b9f05' 
        );

        return $response->toArray();
    }


}


### permet d'initialiser les moyennes
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