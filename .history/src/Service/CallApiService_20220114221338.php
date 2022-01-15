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

    public function getApi()
    {
        $response = $this->client->request(
            'GET',
            'http://www.omdbapi.com/?i=.$value.&apikey=7011bbd5' 
        );

        return $response->toArray();
    }


}
