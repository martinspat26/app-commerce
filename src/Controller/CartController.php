<?php

namespace App\Controller;

use Pimcore\Model\DataObject\Product;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;

class CartController extends FrontendController
{
    /**
     * @Route("/cartadd", name="cartadd")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCartAction(Request $request) {
        $product = Product::getById($request->get("id"));

        $cartManager = Factory::getInstance()->getCartManager();
        $cart = $cartManager->getOrCreateCartByName('my-cart');

        $cart->addItem($product, 1);
        $cart->save();

        return $this->redirectToRoute("cartlist");
    }

    /**
     * @Route("/cartlist", name="cartlist")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function cartListAction() {
        $cartItems = [];

        $cartManager = Factory::getInstance()->getCartManager();
        $cart = $cartManager->getOrCreateCartByName('my-cart');

        foreach ($cart->getItems() as $item) {
            $cartItems[] = [
                'id' => $item->getItemKey(),
                'name' => $item->getProduct()->getName(),
                'count' => $item->getCount()
            ];
        }

        return $this->json($cartItems);
    }
}
