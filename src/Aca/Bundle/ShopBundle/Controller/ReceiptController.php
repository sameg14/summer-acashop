<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ReceiptController show a receipt for the order they just placed
 * @package Aca\Bundle\ShopBundle\Controller
 */
class ReceiptController extends Controller
{
    /**
     * Display a receipt for a completed order
     */
    public function showAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        // Acquire the orderId ( from session )
        $orderId = $session->get('completed_order_id');

        $billingAddress = null;
        $shippingAddress = null;

        // write a query to get the addresses from the DB
        $query = '
        select
            *
        from
            aca_order_address
        where
            order_id = "' . $orderId . '"';

        // Get shipping/ billing address for this order (from the DB)
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        // Break out the shipping address and billing address into separate arrays
        foreach ($rows as $row) {

            if ($row->type == 'billing') {
                $billingAddress = $row;
            } else {
                $shippingAddress = $row;
            }
        }

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
        $db->setQuery($query);
        $products = $db->loadObjectList();

        return $this->render('AcaShopBundle:Receipt:receipt.html.twig', array(
                'orderId' => $orderId,
                'billing' => $billingAddress,
                'shipping' => $shippingAddress,
                'products' => $products
            )
        );
    }
}