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

        /** @var array $userSelectedProducts Contains the merge of products/cart items */
        $userSelectedProducts = [];

        // Challenge: Is to merge the items in the cart, with products!
        // Hint: two loops! loop through the DB products first, then cart items

        $grandTotal = 0.00;

        foreach ($cartItems as $cartItem) {

            foreach ($dbProducts as $dbProduct) {

                // magic happens! the struggle is real...

                if ($dbProduct->product_id == $cartItem['product_id']) {

                    $dbProduct->quantity = $cartItem['quantity'];

                    $dbProduct->total_price = $dbProduct->price * $cartItem['quantity'];
                    $grandTotal += $dbProduct->total_price;

                    $userSelectedProducts[] = $dbProduct;
                }
            }
        }

        return $this->render('AcaShopBundle:Cart:list.html.twig',
            array(
                'products' => $userSelectedProducts,
                'grandTotal' => $grandTotal
            )
        );
    }

    /**
     * Delete one item from your shopping cart
     * @return RedirectResponse
     */
    public function deleteAction()
    {
        $productId = $_POST['product_id'];

        $session = $this->get('session');
        $cartItems = $session->get('cart');

        foreach ($cartItems as $index => $cartItem) {
            if ($cartItem['product_id'] == $productId) {
                unset($cartItems[$index]);
            }
        }

        $session->set('cart', $cartItems);

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

        $session = $this->get('session');
        $cartItems = $session->get('cart');

        foreach($cartItems as $index => $cartItem){

            if($cartItem['product_id'] == $productId){
                $cartItems[$index]['quantity'] = $updatedQuantity;
            }
        }

        $session->set('cart', $cartItems);

        return new RedirectResponse('/cart');
    }
}
