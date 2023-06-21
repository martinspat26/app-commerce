<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\OnlineShopOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use stdClass;

class CheckoutController extends FrontendController
{
    /**
     * @Route("/checkout-address", name="shop-checkout-address")
     *
     * @param Factory $factory
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function checkoutAddressAction(Factory $factory, Request $request): RedirectResponse|Response
    {
        $data = $request->request->all();

        $cartManager = $factory->getCartManager();
        $cart = $cartManager->getOrCreateCartByName('cart');

        $checkoutManager = $factory->getCheckoutManager($cart);
        $deliveryAddress = $checkoutManager->getCheckoutStep('deliveryaddress');


        // Create the form
        $form = $this->createFormBuilder()
        ->add('email', EmailType::class, [
            'label' => 'E-mail',
            'required' => true,
            'constraints' => [new NotBlank()],
        ])
        ->add('firstName', TextType::class, [
            'label' => 'Nome',
            'required' => true,
            'constraints' => [new NotBlank()],
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Apelido',
            'required' => true,
            'constraints' => [new NotBlank()],
        ])
        ->add('_submit', SubmitType::class, [
            'label' => 'AVANÇAR',
            'attr' => ['class' => 'btn btn-primary btn-lg btn-block mt-4'],
        ])
        ->getForm();

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve form data
            $data = $form->getData();

            // Create the address object
            $address = new stdClass();
            $address->email = $data['email'];
            $address->firstName = $data['firstName'];
            $address->lastName = $data['lastName'];

            $checkoutManager->commitStep($deliveryAddress, $address);
            $cart->save();

            $confirm = $checkoutManager->getCheckoutStep('confirm');
            $checkoutManager->commitStep($confirm, true);

            return $this->redirectToRoute('shop-checkout-payment');
        }

        return $this->render('checkout/checkout_address.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/checkout-completed", name="shop-checkout-completed")
     *
     * @param SessionInterface $session
     *
     * @return Response
     */
    public function checkoutCompletedAction(SessionInterface $session): Response
    {
        $orderId = $session->get('last_order_id');

        $order = OnlineShopOrder::getById($orderId);

        return $this->render('checkout/checkout_completed.html.twig', [
            'order' => $order
        ]);
    }


    /**
     * @param Request $request
     *
     * @return Response
     */
    public function confirmationMailAction(Request $request): Response
    {
        $order = $request->get('order');

        if ($request->get('order-id')) {
            $order = OnlineShopOrder::getById($request->get('order-id'));
        }

        return $this->render('checkout/confirmation_mail.html.twig', [
            'order' => $order,
            'ordernumber' => $request->get('ordernumber')
        ]);
    }
}
