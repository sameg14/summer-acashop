<?php

namespace Aca\Bundle\ShopBundle\Controller;

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
            if($existingItem == false){

                $cart[] = array(
                    'product_id' => $productId,
                    'quantity' => $quantity
                );
            }
        }

        $session->set('cart', $cart);

        echo '<h3>Cart Items</h3>';
        echo '<pre>';
        print_r($cart);
        die();
    }
}
