<?php

namespace Aca\Bundle\ShopBundle\Shop;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Aca\Bundle\ShopBundle\Shop\Product;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Cart will contain all cart functionality
 * @package Aca\Bundle\ShopBundle\Shop
 */
class Cart extends AbstractOrder
{
    /**
     * Product class
     * @var Product
     */
    protected $product;

    /**
     * This is the grand total for everything in the cart
     * @var float
     */
    protected $grandTotal;

    /**
     * These are the products the user added to their shopping cart, with all details
     * @var array
     */
    protected $userSelectedProducts;

    /**
     * @param DBCommon $db
     * @param Session $session
     * @param Product $product
     */
    public function __construct($db, $session, $product)
    {
        parent::__construct($db, $session);

        $this->product = $product;
    }

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
     * @throws \Exception
     * @return array
     */
    public function getProductIds()
    {
        $cartItems = $this->session->get('cart');
        if(empty($cartItems)){
            throw new \Exception('The cart is empty!');
        }

        $cartProductIds = [];
        foreach ($cartItems as $cartItem) {
            $cartProductIds[] = $cartItem['product_id'];
        }

        return $cartProductIds;
    }


    /**
     * Get back an array of products with the user selected quantity
     * @throws \Exception
     * @return array
     */
    public function getProducts()
    {
        if (isset($this->userSelectedProducts)) {
            return $this->userSelectedProducts;
        }

        $cartItems = $this->session->get('cart');

        $cartProductIds = $this->getProductIds();

        $dbProducts = $this->product->getProductsByProductIds($cartProductIds);

        /** @var array $userSelectedProducts Contains the merge of products/cart items */
        $userSelectedProducts = [];

        $grandTotal = 0.00;

        foreach ($cartItems as $cartItem) {

            foreach ($dbProducts as $dbProduct) {

                if ($dbProduct->product_id == $cartItem['product_id']) {

                    $dbProduct->quantity = $cartItem['quantity'];

                    $dbProduct->total_price = $dbProduct->price * $cartItem['quantity'];
                    $grandTotal += $dbProduct->total_price;

                    $userSelectedProducts[] = $dbProduct;
                }
            }
        }

        $this->grandTotal = $grandTotal;
        $this->userSelectedProducts = $userSelectedProducts;

        if (empty($userSelectedProducts)) {
            throw new \Exception('Please add some things to your cart!');
        }

        return $userSelectedProducts;
    }

    /**
     * Get the grand total for this shopping cart
     * @return float
     */
    public function getGrandTotal()
    {
        if (!isset($this->grandTotal)) {
            $this->getProducts();
        }

        return $this->grandTotal;
    }
}