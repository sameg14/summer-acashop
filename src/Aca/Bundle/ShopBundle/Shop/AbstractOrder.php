<?php

namespace Aca\Bundle\ShopBundle\Shop;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AbstractOrder shares common functionality with Cart and OrderComplete
 * @package Aca\Bundle\ShopBundle\Shop
 */
abstract class AbstractOrder
{
    /**
     * @var DBCommon
     */
    protected $db;

    /**
     * @var Session
     */
    protected $session;

    public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }
}
