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

        $session->remove('cart');

        // Acquire the orderId ( from session )
        $orderId = $session->get('completed_order_id');

        $order = $this->get('aca.order');
        $products = $order->getProducts();

        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        return $this->render('AcaShopBundle:Receipt:receipt.html.twig', array(
                'orderId' => $orderId,
                'billing' => $billingAddress,
                'shipping' => $shippingAddress,
                'products' => $products
            )
        );
    }
}