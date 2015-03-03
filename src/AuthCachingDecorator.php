<?php

class Wolfnet_AuthCachingDecorator extends Wolfnet_ApiAuthDecorator
{


    private $auth;


    public function __construct(Wolfnet_ApiAuthDecorator $auth)
    {
        $this->auth = $auth;
    }





}
