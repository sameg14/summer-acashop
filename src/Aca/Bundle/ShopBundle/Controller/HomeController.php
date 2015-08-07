<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        $name = $session->get('name');
        $loggedIn = $session->get('logged_in');
        $errorMessage = $session->get('error_message');

        return $this->render(
            'AcaShopBundle:Home:index.html.twig',
            array(
                'loggedIn' => $loggedIn,
                'name' => $name,
                'errorMessage' => $errorMessage
            )
        );
    }

    /**
     * This logs the user in
     * @return RedirectResponse
     */
    public function loginAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        // Acquire user input
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check username and password
        $query = 'select * from aca_user where username="' . $username . '" and password="' . $password . '"';

        $db = $this->get('aca.db');

        $db->setQuery($query);
        $user = $db->loadObject(); // fetches one row from the database!

        // If the user is good, then set logged_in=1 in session
        if (empty($user)) {

            $session->set('logged_in', 0);
            $session->set('error_message', 'Login failed, please try again');

        } else {

            $session->set('logged_in', 1);
            $session->set('name', $user->name);
            $session->set('user_id', $user->user_id);
        }

        return new RedirectResponse('/');
    }
}
