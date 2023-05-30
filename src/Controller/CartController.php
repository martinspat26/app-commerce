<?php

namespace App\Controller;

use Pimcore\Model\DataObject\Product;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\ProductInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;

class CartController extends FrontendController
{
    const DEFAULT_CART_NAME = 'cart';

    /**
     * @var Factory
     */
    protected $factory;
    
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }
    
    /**
     * @return CartInterface
     */
    protected function getCart($cartName = self::DEFAULT_CART_NAME)
    {
        $cartManager = $this->factory->getCartManager();
    
        return $cartManager->getOrCreateCartByName($cartName);
    }
    
    /**
     * @Route("/cartadd", name="cartadd")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCartAction(Request $request) {
        $productId = $request->get("id");
        $quantity = 1;
    
        $product = Product::getById($productId);
    
        $cart = $this->getCart('cart');
        $cart->addItem($product, $quantity);
        $cart->save();
    
        return $this->redirectToRoute("cartlist");
    }
    
    /**
     * @Route("/cartremove", name="cartremove")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromCartAction(Request $request) {
        $productId = $request->get('id');
    
        $cart = $this->getCart('cart');
        $cart->removeItem((string) $productId);
        $cart->save();
    
        return $this->redirectToRoute("cartlist");
    }
    


    /**
     * @Route("/cartlist", name="cartlist")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function cartListAction(Request $request) {
        $cartItems = [];

        $cart = $this->getCart('cart');

        
        foreach ($cart->getItems() as $item) {
            $cartItems[] = [
                'id' => $item->getItemKey(),
                'name' => $item->getProduct()->getOSName(),
                'count' => $item->getCount(),
                'price' => $item->getProduct()->getOSPrice()
            ];
        }
        // return $this->render('cart/cart_listing.html.twig', [
        //     'products' => $cartItems
        // ]);

        // $cart = $this->getCart();

        // if ($request->getMethod() == Request::METHOD_POST) {
        //     $items = $request->get('items');

        //     foreach ($items as $itemKey => $quantity) {
        //         if ($cart->getItemCount() > 99) {
        //             break;
        //         }
        //         $product = Product::getById($itemKey);
        //         if ($product instanceof CheckoutableInterface) {
        //             $cart->updateItem($itemKey, $product, $quantity, true);
        //         }
        //     }
        //     $cart->save();
        // }

        $params = array_merge($request->request->all(), $request->query->all());
        if ($cart->isEmpty()) {
            return $this->render('cart/cart_empty.html.twig', array_merge($params, ['cart' => $cart]));
        } else {
            return $this->render('cart/cart_listing.html.twig', array_merge($params, ['cart' => $cart, 'products' => $cartItems]));
        }
    }

    public function cartCheckout(){
        $environment = Factory::getInstance()->getEnvironment();
        $environment->setCurrentCheckoutTenant('default');
        $environment->save();

        $environment->setCurrentCheckoutTenant('noShipping');
        $environment->save();
    }
}
