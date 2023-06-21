<?php

use Pimcore\Bundle\EcommerceFrameworkBundle\PimcoreEcommerceFrameworkBundle;
use CustomerManagementFrameworkBundle\PimcoreCustomerManagementFrameworkBundle;
use Pimcore\Bundle\ObjectMergerBundle\ObjectMergerBundle;
use Pimcore\Bundle\EcommerceFrameworkBundle\PimcorePaymentProviderPayPalSmartPaymentButtonBundle;

return [
    //Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    PimcoreEcommerceFrameworkBundle::class => ['all' => true],
    PimcoreCustomerManagementFrameworkBundle::class => ['all' => true],
    ObjectMergerBundle::class => ['all' => true],
    PimcorePaymentProviderPayPalSmartPaymentButtonBundle::class => ['all' => true],
];
