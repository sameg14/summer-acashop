<?php

namespace Aca\Bundle\ShopBundle\Shop;

/**
 * Class Cart will contain all cart functionality
 * @package Aca\Bundle\ShopBundle\Shop
 */
class Cart extends AbstractOrder
{
    /**
     * Delete one product from the shopping cart
     * @param int $productId Primary key from product table
     * @throws \Exception
     * @return bool
     */
    public function delete($productId)
    {
        $cartItems = $this->session->get('cart');

        foreach ($cartItems as $index => $cartItem) {
            if ($cartItem['product_id'] == $productId) {
                unset($cartItems[$index]);
            }
        }

        $this->session->set('cart', $cartItems);

        $didRemove = true;

        foreach ($cartItems as $index => $cartItem) {
            if ($cartItem['product_id'] == $productId) {
                $didRemove = false;
            }
        }

        if (!$didRemove) {
            throw new \Exception('Cannot delete item from cart!');
        }

        return $didRemove;
    }

    /**
     * Update the quantity of an item in the cart
     * @param int $productId PK from product
     * @param int $quantityToUpdate Qty to update to
     * @return void
     */
    public function updateQuantity($productId, $quantityToUpdate)
    {
        $cartItems = $this->session->get('cart');

        foreach ($cartItems as $index => $cartItem) {

            if ($cartItem['product_id'] == $productId) {
                $cartItems[$index]['quantity'] = $quantityToUpdate;
            }
        }

        $this->session->set('cart', $cartItems);
    }

    /**
     * Get an array of productIds from the shopping cart
     * @return array
     */
    public function getProductIds()
    {
        $cartItems = $this->session->get('cart');

        $cartProductIds = [];
        foreach ($cartItems as $cartItem) {
            $cartProductIds[] = $cartItem['product_id'];
        }

        return $cartProductIds;
    }


    public function getGrandTotal()
    {

    }
}