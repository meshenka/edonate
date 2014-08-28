<?php

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class AuthenticationController extends Controller
{
    /**
     * @Route("/_login" , name="donate_admin_login")
     * @Template()
     *
     * @see http://symfony.com/doc/current/book/security.html
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return [
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            ];
    }

    /**
     * @Route("/_login_check" , name="donate_admin_login_check")
     * @Template()
     */
    public function loginCheckAction()
    {
        return array();
    }

    /**
     * @see http://symfony.com/fr/doc/current/book/security.html#se-deconnecter
     * @Route("/_logout" , name="donate_admin_logout")
     * @Template()
     */
    public function logoutAction()
    {
        return array();
    }

}
