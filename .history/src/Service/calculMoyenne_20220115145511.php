<?php
namespace App\Service;
use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class CallApiService
{

    

    public function __construct()
    {
       
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
        