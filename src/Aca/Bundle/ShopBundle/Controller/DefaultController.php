<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name, $myAge)
    {
        return $this->render('AcaShopBundle:Default:index.html.twig',
            array('foo' => $name, 'myAge' => $myAge)
        );
    }
}
