<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class HomeController
 * @package Aca\Bundle\ShopBundle\Controller
 */
class ProductController extends Controller
{
    /**
     * Show all products on ACA Shop
     */
    public function showAction()
    {
        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        $query = 'select * from aca_product';
        $db->setQuery($query);
        $products = $db->loadObjectList();

        return $this->render('AcaShopBundle:Product:list.html.twig',
            array(
                'products' => $products
            )
        );
    }
}
