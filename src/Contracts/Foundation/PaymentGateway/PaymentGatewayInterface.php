<?php

namespace Crazymeeks\Contracts\Foundation\PaymentGateway;

interface PaymentGatewayInterface
{
	public function setRequestParameters(array $params);
}