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

class WishlistController extends FrontendController
{
    const DEFAULT_CART_NAME = 'wish';

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
    protected function getWishlist($wishlistName = self::DEFAULT_CART_NAME)
    {
        $wishlistManager = $this->factory->getCartManager();
    
        return $wishlistManager->getOrCreateCartByName($wishlistName);
    }
    
    /**
     * @Route("/wishlistadd", name="wishlistadd")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToWishlistAction(Request $request) {
        $productId = $request->get("id");
        $quantity = 1;
    
        $product = Product::getById($productId);
    
        $wishCart = $this->getWishlist('wish');
        $wishCart->addItem($product, $quantity);
        $wishCart->save();
    
        return $this->redirectToRoute("wishlist");
    }
    
    /**
     * @Route("/wishlistremove", name="wishlistremove")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromWishlistAction(Request $request) {
        $productId = $request->get('id');
    
        $wishCart = $this->getWishlist('wish');
        $wishCart->removeItem((string) $productId);
        $wishCart->save();
    
        return $this->redirectToRoute("wishlist");
    }
    


    /**
     * @Route("/wishlist", name="wishlist")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function wishListAction(Request $request) {
        $wishCartItems = [];

        $wishCart = $this->getWishlist('wish');

        
        foreach ($wishCart->getItems() as $item) {
            $wishCartItems[] = [
                'id' => $item->getItemKey(),
                'name' => $item->getProduct()->getOSName(),
                'count' => $item->getCount(),
                'price' => $item->getProduct()->getOSPrice()
            ];
        }
        $params = array_merge($request->request->all(), $request->query->all());
        if ($wishCart->isEmpty()) {
            return $this->render('wishlist/wishlist_empty.html.twig', array_merge($params, ['wish' => $wishCart]));
        } else {
            return $this->render('wishlist/wishlist_listing.html.twig', array_merge($params, ['wish' => $wishCart, 'products' => $wishCartItems]));
        }
    }
}
