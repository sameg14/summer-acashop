<?php

namespace Aca\Bundle\ShopBundle\Shop;

use Aca\Bundle\ShopBundle\Db\DBCommon;

/**
 * Class Product represents product related functionality
 * @package Aca\Bundle\ShopBundle\Shop
 */
class Product
{
    /**
     * @var DBCommon
     */
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all product rows from the database
     * @return \stdClass[]
     */
    public function getAllProducts()
    {
        $query = 'select * from aca_product';
        $this->db->setQuery($query);
        $products = $this->db->loadObjectList();

        return $products;
    }

    /**
     * Get a number of products from the DB from the specified productIds
     * @param array $productIds Array of productIds
     * @return \stdClass[]
     */
    public function getProductsByProductIds($productIds)
    {
        $query = 'select * from aca_product where product_id
                  in(' . implode(',', $productIds) . ')';

        $this->db->setQuery($query);
        $dbProducts = $this->db->loadObjectList();

        return $dbProducts;
    }
}