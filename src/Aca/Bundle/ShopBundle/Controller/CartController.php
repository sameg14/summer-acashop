<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller
{
    /**
     * Add an item to our shopping cart
     * @return RedirectResponse
     */
    public function addAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        // Get the cart from session, it may be empty the first time around.
        $cart = $session->get('cart');

        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // First time someone tries to add something to your cart
        if (empty($cart)) {

            $cart[] = array(
                'product_id' => $productId,
                'quantity' => $quantity
            );

        } else { // Something is in your cart already

            $existingItem = false;

            foreach ($cart as &$cartItem) {

                // If a product is existing in the shopping cart
                if ($cartItem['product_id'] == $productId) {

                    $existingItem = true;

                    // Add to the existing quantity
                    $cartItem['quantity'] += $quantity;
                }
            }

            // Brand new item
            if ($existingItem == false) {

                $cart[] = array(
                    'product_id' => $productId,
                    'quantity' => $quantity
                );
            }
        }

        $session->set('cart', $cart);

        return new RedirectResponse('/cart');
    }

    /**
     * Show the contents of the user's shopping cart
     */
    public function showAction()
    {
        try {

            $cart = $this->get('aca.cart');
            $grandTotal = $cart->getGrandTotal();
            $userSelectedProducts = $cart->getProducts();

            return $this->render('AcaShopBundle:Cart:list.html.twig',
                array(
                    'products' => $userSelectedProducts,
                    'grandTotal' => $grandTotal
                )
            );

        } catch (\Exception $exception) {

            $errorMessage = $exception->getMessage();

            return $this->render('AcaShopBundle:Cart:list.html.twig',
                array(
                    'errorMessage' => $errorMessage
                )
            );
        }
    }

    /**
     * Delete one item from your shopping cart
     * @throws \Exception
     * @return RedirectResponse
     */
    public function deleteAction()
    {
        $productId = $_POST['product_id'];

        $cart = $this->get('aca.cart');
        $cart->delete($productId);

        return new RedirectResponse('/cart');
    }

    /**
     * Update the quantity for one particular product in the cart
     * @return RedirectResponse
     */
    public function updateAction()
    {
        $productId = $_POST['product_id'];
        $updatedQuantity = $_POST['quantity'];

        $cart = $this->get('aca.cart');
        $cart->updateQuantity($productId, $updatedQuantity);

        return new RedirectResponse('/cart');
    }

    /**
     * Show shipping address form
     */
    public function shippingAddressAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        /** @var int $userId Logged in user identifier */
        $userId = $session->get('user_id');

        if (empty($userId)) {

            // Add some messaging telling them: why am i here and what do i do?
            $session->set('error_message', 'Why am I here and what am I doing?');

            return new RedirectResponse('/');
        }

        // Get the shipping_address_id and billing_address_id from the user table
        $query = '
        select
            shipping_address_id,
            billing_address_id
        from
            aca_user
        where
            user_id = ' . $userId;

        $db->setQuery($query);
        $shippingIds = $db->loadObject();

        $shippingAddressId = $shippingIds->shipping_address_id;
        $billingAddressId = $shippingIds->billing_address_id;

        // Get shipping and billing address

        $shippingQuery = '
        select
            *
        from
            aca_address
        where
            address_id =' . $shippingAddressId;

        $db->setQuery($shippingQuery);
        $shippingAddress = $db->loadObject();

        $billingQuery = '
        select
            *
        from
            aca_address
        where
            address_id =' . $billingAddressId;
        $db->setQuery($billingQuery);
        $billingAddress = $db->loadObject();

        return $this->render('AcaShopBundle:Shipping:address.html.twig',
            array(
                'shipping' => $shippingAddress,
                'billing' => $billingAddress
            )
        );
    }
}
