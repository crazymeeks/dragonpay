<?php

namespace Crazymeeks\Foundation\PaymentGateway\Handler;

interface PostbackHandlerInterface
{


    /**
     * Handle postback invoked by Dragonpay
     *
     * @param array $data
     * 
     * @return mixed
     */
    public function handle(array $data);

}