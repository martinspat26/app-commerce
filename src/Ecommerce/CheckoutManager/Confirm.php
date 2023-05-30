<?php

namespace App\Ecommerce\CheckoutManager;

use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\AbstractStep;
use Pimcore\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutStepInterface;

class Confirm extends AbstractStep implements CheckoutStepInterface
{
    /**
     * namespace key
     */
    const PRIVATE_NAMESPACE = 'confirm';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'confirm';
    }

    /**
     * @inheritdoc
     */
    public function commit($data)
    {
        $this->cart->setCheckoutData(self::PRIVATE_NAMESPACE, json_encode($data));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = json_decode($this->cart->getCheckoutData(self::PRIVATE_NAMESPACE));

        return $data;
    }
}