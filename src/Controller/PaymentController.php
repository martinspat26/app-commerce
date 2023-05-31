<?php


namespace App\Controller;


use Psr\Log\LoggerInterface;
use Pimcore\Translation\Translator;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\Payment\PayPalSmartPaymentButton;
use Pimcore\Bundle\EcommerceFrameworkBundle\PaymentManager\V7\Payment\StartPaymentRequest\AbstractRequest;

class PaymentController extends FrontendController
{
    /**
     * @Route("/checkout-payment", name="shop-checkout-payment")
     *
     * @param Factory $factory
     *
     * @return Response
     */
    public function checkoutPaymentAction(Factory $factory)
    {
        $cartManager = $factory->getCartManager();

        $cart = $cartManager->getOrCreateCartByName('cart');

        if ($cart->isEmpty()) {
            return $this->redirectToRoute('shop-cart-detail');
        }

        $checkoutManager = $factory->getCheckoutManager($cart);
        $paymentProvider = $checkoutManager->getPayment();

        $SDKUrl = '';
        if ($paymentProvider instanceof PayPalSmartPaymentButton) {
            $SDKUrl = $paymentProvider->buildPaymentSDKLink();
        }

        return $this->render('payment/checkout_payment.html.twig', [
            'cart' => $cart,
            'SDKUrl' => $SDKUrl
        ]);
    }

    /**
     * @Route("/checkout-start-payment", name="shop-checkout-start-payment")
     *
     * @param Request $request
     * @param Factory $factory
     *
     * @return JsonResponse
     */
    public function startPaymentAction(Request $request, Factory $factory, LoggerInterface $logger)
    {
        $cartManager = $factory->getCartManager();
        $cart = $cartManager->getOrCreateCartByName('cart');

        $checkoutManager = Factory::getInstance()->getCheckoutManager($cart);
        $paymentInformation = $checkoutManager->initOrderPayment();
        $order = $paymentInformation->getObject();

        $config = [
            'return_url' => '',
            'cancel_url' => 'https://app-commerce.test/payment-error',
            'OrderDescription' => 'My Order ' . $order->getOrdernumber() . ' tripandfun.test',
            'InternalPaymentId' => $paymentInformation->getInternalPaymentId()
        ];

        $paymentConfig = new AbstractRequest($config);

        $response = $checkoutManager->startOrderPaymentWithPaymentProvider($paymentConfig);

        return new JsonResponse($response->getJsonString(), 200, [], true);
    }

    /**
     * @Route("/payment-error", name = "shop-checkout-payment-error" )
     *
     * @return RedirectResponse
     */
    public function paymentErrorAction(Request $request, LoggerInterface $logger)
    {
        $this->addFlash('danger', 'Payment error');

        return $this->redirectToRoute('shop-checkout-payment');
    }

    /**
     * @Route("/payment-commit-order", name="shop-commit-order")
     *
     * @param Request $request
     * @param Factory $factory
     * @param LoggerInterface $logger
     * @param Translator $translator
     * @param SessionInterface $session
     *
     * @return RedirectResponse
     *
     */
    public function commitOrderAction(Request $request, Factory $factory, LoggerInterface $logger, Translator $translator, SessionInterface $session)
    {

        $cartManager = $factory->getCartManager();
        $cart = $cartManager->getOrCreateCartByName('cart');

        $checkoutManager = $factory->getCheckoutManager($cart);
        $params = array_merge($request->query->all(), $request->request->all());

        $order = $checkoutManager->handlePaymentResponseAndCommitOrderPayment($params);

        if (!$session->isStarted()) {
            $session->start();
        }

        $session->set('last_order_id', $order->getId());

        return $this->redirectToRoute('shop-checkout-completed');
    }
}
