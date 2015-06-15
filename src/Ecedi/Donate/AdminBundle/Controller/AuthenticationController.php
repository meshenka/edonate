<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Security;

class AuthenticationController extends Controller
{
    /**
     * @Route("/_login" , name="donate_admin_login")
     *
     * @see http://symfony.com/doc/current/book/security.html
     * @since  2.4 SecurityContext is deprecated, use Symfony\Component\Security\Core\Security for constants
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return $this->render('DonateAdminBundle:Authentication:login.html.twig', [
                'last_username' => $session->get(Security::LAST_USERNAME),
                'error'         => $error,
            ]);
    }

    /**
     * @Route("/_login_check" , name="donate_admin_login_check")
     */
    public function loginCheckAction()
    {
        return array();
    }

    /**
     * @see http://symfony.com/fr/doc/current/book/security.html#se-deconnecter
     * @Route("/_logout" , name="donate_admin_logout")
     */
    public function logoutAction()
    {
        return array();
    }
}
