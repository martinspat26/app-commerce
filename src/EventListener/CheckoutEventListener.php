<?php


namespace App\EventListener;

use CustomerManagementFrameworkBundle\ActivityManager\ActivityManagerInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Event\Model\Ecommerce\CommitOrderProcessorEvent;
use Pimcore\Event\Model\Ecommerce\OrderManagerEvent;
use Pimcore\Event\Model\Ecommerce\SendConfirmationMailEvent;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Mail;
use Pimcore\Model\DataObject\OnlineShopOrder;
use Pimcore\Model\Document\Email;

class CheckoutEventListener
{
    /**
     * @var Factory
     */
    protected $ecommerceFactory;



    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * CheckoutEventListener constructor.
     *
     * @param Factory $ecommerceFactory
     * @param ActivityManagerInterface $activityManager
     */
    public function __construct(Factory $ecommerceFactory, LocaleServiceInterface $localeService)
    {
        $this->ecommerceFactory = $ecommerceFactory;
        $this->localeService = $localeService;
    }

    /**
     * @param OrderManagerEvent $event
     *
     * @throws \Exception
     */
    public function onUpdateOrder(OrderManagerEvent $event)
    {
        $cart = $event->getCart();

        /**
         * @var $order OnlineShopOrder
         */
        $order = $event->getOrder();

        $checkout = $this->ecommerceFactory->getCheckoutManager($cart);
        $deliveryAddress = $checkout->getCheckoutStep('deliveryaddress') ? $checkout->getCheckoutStep('deliveryaddress')->getData() : null;

        if ($deliveryAddress) {

            //inserting delivery and billing address from checkout step delivery

            $order->setCustomerFirstname($deliveryAddress->firstname);
            $order->setCustomerLastname($deliveryAddress->lastname);
            $order->setCustomerEmail($deliveryAddress->email);

            $order->setDeliveryFirstname($deliveryAddress->firstname);
            $order->setDeliveryLastname($deliveryAddress->lastname);

            //updating customer object based on delivery address data
            $customer = $order->getCustomer();
            if ($customer) {
                $params = ['email', 'firstname', 'lastname'];
                foreach ($params as $param) {
                    $customer->{'set' . ucfirst($param)}($deliveryAddress->{$param});
                }
                $customer->save();
            }
        }
    }

    /**
     * @param SendConfirmationMailEvent $event
     */
    public function sendConfirmationMail(SendConfirmationMailEvent $event)
    {
        $order = $event->getOrder();

        $mail = new Mail();
        $mail->setDocument(Email::getByPath('/' . $this->localeService->getLocale() . '/mails/confirmation-mail'));
        $mail->setParams([
            'ordernumber' => $order->getOrdernumber(),
            'order' => $order
        ]);

        $mail->addTo($order->getCustomerEMail());
        $mail->send();

        $event->setSkipDefaultBehaviour(true);
    }

    /**
     * @param CommitOrderProcessorEvent $event
     *
     * @throws \Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException
     */
    public function postCommitOrder(CommitOrderProcessorEvent $event)
    {
        $order = $event->getOrder();
        $order->getCustomer();
        // if ($this->activityManager && $order->getCustomer()) {
        //     $this->activityManager->trackActivity(new OrderActivity($order->getCustomer(), $order));
        // }
    }
}