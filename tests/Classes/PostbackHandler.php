<?php

namespace Tests\Classes;

use Crazymeeks\Foundation\PaymentGateway\Handler\PostbackHandlerInterface;

class PostbackHandler implements PostbackHandlerInterface
{


    /**
     * @inheritDoc
     */
    public function handle(array $data)
    {
        // Developer can do everything here...
        // like save/update data to database, etc
        return $data;
    }
}