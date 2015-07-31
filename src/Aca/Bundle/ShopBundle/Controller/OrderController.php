<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Exception;

/**
 * Class OrderController handles order placing functionality
 * @package Aca\Bundle\ShopBundle\Controller
 */
class OrderController extends Controller
{
    /**
     * Place the order
     *  Create an order record
     *  Get back an orderId
     *  Collect the billing and shipping address from the user.
     *  Write the billing and shipping address to the order_address table
     *  Write the products to the aca_order_product table
     *  Be sure to enter in the quantity and the total price in the price field in the order_product table
     */
    public function placeAction()
    {
        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        /** @var Session $session */
        $session = $this->get('session');

        $userId = $session->get('user_id');

        // Create an order record
        $query = 'insert into aca_order(user_id) values(' . $userId . ')';
        $db->setQuery($query);
        $db->query();
        $orderId = $db->getLastInsertId();

        $this->createOrderAddresses($orderId);

        // Write the products to the aca_order_product table
        $this->createOrderProducts($orderId);

        // Save the completed orderId in session, so we can have it on the
        // receipt page
        $session->set('completed_order_id', $orderId);

        // Redirect the user to the receipt route
        return new RedirectResponse('/receipt');
    }

    /**
     * Save the user entered addresses in the DB
     * @param int $orderId Newly created orderId
     * @throws Exception
     */
    protected function createOrderAddresses($orderId)
    {
        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        $billingStreet = $_POST['billing_street'];
        $billingCity = $_POST['billing_city'];
        $billingState = $_POST['billing_state'];
        $billingZip = $_POST['billing_zip'];

        $shippingStreet = $_POST['shipping_street'];
        $shippingCity = $_POST['shipping_city'];
        $shippingState = $_POST['shipping_state'];
        $shippingZip = $_POST['shipping_zip'];


        // Write the billing and shipping address to the order_address table
        // Save billing order address
        $query = '
        insert into aca_order_address
        (
            order_id,
            type,
            street,
            city,
            state,
            zip
        )
        values
        (
            "' . $orderId . '",
            "billing",
            "' . $billingStreet . '",
            "' . $billingCity . '",
            "' . $billingState . '",
            "' . $billingZip . '"
        )';

        $db->setQuery($query);
        $db->query();

        // Save shipping order address
        $query = '
        insert into aca_order_address
        (
            order_id,
            type,
            street,
            city,
            state,
            zip
        )
        values
        (
            "' . $orderId . '",
            "shipping",
            "' . $shippingStreet . '",
            "' . $shippingCity . '",
            "' . $shippingState . '",
            "' . $shippingZip . '"
        )';

        $db->setQuery($query);
        $db->query();
    }

    protected function createOrderProducts($orderId)
    {
        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        $session = $this->get('session');

        $cartItems = $session->get('cart');
        $cartProductIds = [];

        foreach ($cartItems as $cartItem) {
            $cartProductIds[] = $cartItem['product_id'];
        }

        $query = 'select * from aca_product where product_id
                  in(' . implode(',', $cartProductIds) . ')';

        $db->setQuery($query);
        $dbProducts = $db->loadObjectList();

        foreach ($cartItems as $cartItem) {

            foreach ($dbProducts as $dbProduct) {

                if ($dbProduct->product_id == $cartItem['product_id']) {

                    $productId = $dbProduct->product_id;
                    $quantity = $cartItem['quantity'];
                    $productPrice = $dbProduct->price * $cartItem['quantity'];

                    $query = '
                    insert into aca_order_product
                        (order_id, product_id, quantity, price)
                    values
                        ("' . $orderId . '", "' . $productId . '", "' . $quantity . '", "' . $productPrice . '")';

                    $db->setQuery($query);
                    $db->query();
                }
            }
        }
    }
}
