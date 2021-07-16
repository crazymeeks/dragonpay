<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\PaymentGateway;

use Crazymeeks\Foundation\PaymentGateway\Parameters;
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;

class BillingInfoVerifier
{
 
    protected $parameters;


    /**
     * Hook to Dragonpay billing info
     *
     * @return boolean
     */
    public function send(SoapClientAdapter $soap, $url)
    {
       
        return $soap->setParameters($this->parameters->billing_info())
                    ->execute(
                        $url . '?wsdl', array(
                        'location' => $url,
                        'trace' => 1,
                    )
        );

    }
    
    /**
     * Set our parameter instance
     *
     * @param Crazymeeks\Foundation\PaymentGateway\Parameters $parameter
     * 
     * @return $this
     */
    public function setParameterObject(Parameters $parameter)
    {
        $this->parameters = $parameter;
        return $this;
    }
}