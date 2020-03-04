<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;

class Recaptcha {

    protected $ip = NULL ;
    protected $privateKey = NULL ;

    public function __construct(
        string $ip = NULL ,
        string $privateKey
    ) {

        $this->ip = $ip ;
        $this->privateKey = $privateKey ;
    }

    public function execute( string $clientToken ) {

        $client = HttpClient::create() ;

        $response = $client->request(
            'POST',
            'https://www.google.com/recaptcha/api/siteverify' ,
            [
                'body' =>  [
                    'secret' => $this->privateKey ,
                    'response' => $clientToken ,
                    'remoteip' => $this->ip
                ]
                // 'headers' => [
                //     'Content-Type' => 'application/json'
                // ]
            ]
        ) ;

        return $response->toArray() ;
    }

}
