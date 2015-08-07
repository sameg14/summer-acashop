<?php

namespace Aca\Bundle\ShopBundle\Shop;

use \stdClass;

/**
 * Class OrderComplete represents a placed order
 * @package Aca\Bundle\ShopBundle\Shop
 */
class OrderComplete extends AbstractOrder
{
    /**
     * Get products for this particular order
     * @return stdClass[]
     */
    public function getProducts()
    {
        $orderId = $this->session->get('completed_order_id');

        // Get the products on this order from the DB
        $query = '
        select
            op.price,
            op.quantity,
            p.name,
            p.description,
            p.image
        from
            aca_order_product op
            join aca_product p on (p.product_id = op.product_id)
        where
            order_id = "' . $orderId . '"';

        $this->db->setQuery($query);

        return $this->db->loadObjectList();
    }

    /**
     * Get the billing address for this order
     * @return array
     */
    public function getBillingAddress()
    {
        return $this->getAddress('billing');
    }

    /**
     * Get the shipping address for this order
     * @return array
     */
    public function getShippingAddress()
    {
        return $this->getAddress('shipping');
    }

    /**
     * Get one address row from the DB based on its type
     * Type can be [billing, shipping]
     * @param string $type The kind of address desired
     * @return array
     */
    protected function getAddress($type)
    {
        $orderId = $this->session->get('completed_order_id');

        $query = '
        select
            *
        from
            aca_order_address
        where
            order_id = "'.$orderId.'"
            and type = "'.$type.'"';

        $this->db->setQuery($query);

        return $this->db->loadObject();
    }

}