<?php
namespace App\Service;

class CallApiService
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getImdbInfos() : array
    {
        return ['test1','test2'];
    }

}
