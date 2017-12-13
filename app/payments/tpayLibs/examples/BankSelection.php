<?php

/*
 * Created by tpay.com
 */

namespace tpayLibs\examples;

use tpayLibs\src\_class_tpay\PaymentForms\PaymentBasicForms;

include_once 'loader.php';

class BankSelection extends PaymentBasicForms
{
    public function __construct($secret, $id)
    {
        $this->merchantSecret = $secret;
        $this->merchantId = (int)$id;
        parent::__construct();
    }

}

