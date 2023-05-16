<?php

namespace App\Model\DataObject;


class Product extends \Pimcore\Model\DataObject\Product
{
    /**
     * defines if product is included into the product index. If false, product doesn't appear in product index.
     *
     * @return bool
     */
    public function getOSDoIndexProduct(): bool
    {
        return $this->isPublished();
    }

    /**
     * defines the name of the price system for this product.
     * there should either be a attribute in pro product object or
     * it should be overwritten in mapped sub classes of product classes
     *
     * @return string
     */
    public function getPriceSystemName(): ?string
    {
        return 'default';
    }

    /**
     * returns array of categories.
     * has to be overwritten either in pimcore object or mapped sub class.
     *
     * @return \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory[]|null
     */
    public function getCategories(): ?array
    {
        return [];
    }

    /**
     * returns if product is active.
     * there should either be a attribute in pro product object or
     * it should be overwritten in mapped sub classes of product classes 
     * in case of multiple criteria for product active state
     *
     * @param bool $inProductList
     *
     * @return bool
     */
    public function isActive($inProductList = false): bool
    {
        return $this->isPublished();
    }

    /**
     * returns product type for product index (either object or variant).
     * by default it returns type of object, but it may be overwritten if necessary.
     *
     * @return string
     */
    public function getOSIndexType(): ?string
    {
        return $this->getType();
    }

    /**
     * returns parent id for product index.
     * by default it returns id of parent object, but it may be overwritten if necessary.
     *
     * @return int
     */
    public function getOSParentId()
    {
        return $this->getParentId();

    }
}
