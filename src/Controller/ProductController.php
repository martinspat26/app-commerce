<?php

namespace App\Controller;

use Pimcore\Model\User;
use App\Model\DataObject\Product;
use Pimcore\Controller\FrontendController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Model\DataObject\FilterDefinition;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Bundle\EcommerceFrameworkBundle\Factory;
use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\ListHelper;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;

class ProductController extends FrontendController
{
    /**
     * @Route("/listing", name="listing")
     *
     * @param Request $request
     * @param Factory $ecommerceFactory
     * @param ListHelper $listHelper
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function listingAction(Request $request, Factory $ecommerceFactory, ListHelper $listHelper, PaginatorInterface $paginator)
    {
        $params = array_merge($request->query->all(), $request->attributes->all());

        $indexService = $ecommerceFactory->getIndexService();
        $productListing = $indexService->getProductListForCurrentTenant();
        $productListing->setVariantMode(ProductListInterface::PRODUCT_TYPE_OBJECT);
        $params['productListing'] = $productListing;

        $filterList = new FilterDefinition\Listing();
        $filterDefinition = $filterList->load()[0];

        $filterService = $ecommerceFactory->getFilterService();
        $listHelper->setupProductList($filterDefinition, $productListing, $params, $filterService, true);
        $params['filterService'] = $filterService;
        $params['filterDefinition'] = $filterDefinition;

        /** @var SlidingPagination $paginator */
        // init pagination
        $paginator = $paginator->paginate(
            $productListing,
            $request->get('page', 1),
            $filterDefinition->getPageLimit()
        );

        $params['results'] = $paginator;
        $params['paginationVariables'] = $paginator->getPaginationData();

        // return $this->render('gac/gac_listing.html.twig', $params);
        return $this->render('product/product_listing.html.twig', $params);

    }

    /**
     * @Route ("product-detail", name="product-detail")
     *
     * @param Request $request
     *
     * @return array
     */
    public function detailAction(Request $request)
    {
        $product = Product::getById($request->get('id'));
        return $this->render('product/product_detail.html.twig', [
            'product' => $product
        ]);
    }
}