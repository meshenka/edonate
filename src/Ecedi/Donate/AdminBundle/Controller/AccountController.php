<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package Ecollecte
 */
namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\AdminBundle\Form\AccountType;
use Ecedi\Donate\CoreBundle\Entity\User as User;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Util\UserManipulator;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccountController extends Controller
{
    /**
     * @Route("/users" , name="donate_admin_users")
     */
    public function indexAction(Request $request)
    {
        // @since 2.3 we user voters to check authorization instead of being ROLE based
        if (false === $this->get('security.authorization_checker')->isGranted('list users')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entityMgr = $this->getDoctrine()->getManager();
        $query = $entityMgr->getRepository('DonateCoreBundle:User')->getAllUsers();
        $pagination = $this->getPagination($request, $query, 10);

        return $this->render('DonateAdminBundle:Account:index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/user/{id}/edit" , name="donate_admin_user_edit", defaults={"id" = 0})
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(new AccountType(), $user, array(
            'roles' => $this->getAvailabledRoles(),
            'action' => 'edit',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');

            //DELETE
            if ($form->get('submit_delete')->isClicked()) {
                // @since 2.3 we user voters to check authorization instead of being ROLE based
                if (false === $this->get('security.authorization_checker')->isGranted('delete', $user)) {
                    throw new AccessDeniedException('Unauthorised access!');
                }

                $userManager->deleteUser($user);
                $this->get('session')->getFlashBag()->add('notice', "L'utilisateur ".$user->getUsername()." a été supprimé.");

                return $this->redirect($this->generateUrl('donate_admin_users'));
            }

            //EDIT
            if ($form->get('submit_save')->isClicked()) {
                // @since 2.3 we user voters to check authorization instead of being ROLE based
                if (false === $this->get('security.authorization_checker')->isGranted('edit', $user)) {
                    throw new AccessDeniedException('Unauthorised access!');
                }

                $userManager->updateUser($user);
                $this->get('session')->getFlashBag()->add('notice', "L'utilisateur ".$user->getUsername()." a été mis à jour.");

                return $this->redirect($this->generateUrl('donate_admin_users'));
            }
        }

        //view
        // @since 2.3 we user voters to check authorization instead of being ROLE based

        if (false === $this->get('security.authorization_checker')->isGranted('view', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        return $this->render('DonateAdminBundle:Account:edit.html.twig', [
            'form'      => $form->createView(),
            'user'      => $user,
        ]);
    }

    /**
     * Displays a form to create a new User.
     *
     * @Route("/user/new", name="donate_admin_user_new")
     */
    public function newAction(Request $request)
    {
        // @since 2.3 we user voters to check authorization instead of being ROLE based
        if (false === $this->get('security.authorization_checker')->isGranted('create users')) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form = $this->createForm(new AccountType(), new User(), array(
            'roles' => $this->getAvailabledRoles(),
            'action' => 'new',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $userManager = $this->get('fos_user.user_manager');

            if (!$this->userAlreadyExist($userManager, $data->getUsername(), $data->getEmail())) {
                $userManipulator = new UserManipulator($userManager);
                $user = $userManipulator->create($data->getUsername(), $data->getPassword(), $data->getEmail(), true, false);
                $this->get('session')->getFlashBag()->add('notice', "L'utilisateur ".$user->getUsername()." a été enregistré");

                return $this->redirect($this->generateUrl('donate_admin_users'));
            }
        }

        return $this->render('DonateAdminBundle:Account:new.html.twig', [
            'form'   => $form->createView()
        ]);
    }

    /**
    * Fonction pour récupérer notre objet de pagination
    *
    * @param Request $request
    * @param int $limit -- limit du pager
    * @param $entities -- les entités à rendre
    */
    public function getPagination(Request $request, $entities, $limit = 10)
    {
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            $limit
        );

        return $pagination;
    }

    /**
     * Fonction qui retourne les rôles pouvant être assignés par l'utilisateur
     * @since  2.3 the function roles are hardcoded instead of been deduced from role_hierarchy
     * @return $roles -- tableau contenant les rôles povant être assigné par un administrateur
     */
    private function getAvailabledRoles()
    {
        return array(
            'ROLE_USER' => 'Utilisateur',
            'ROLE_ADMIN' => 'Administrateur',
        );
    }

    /**
    * Fonction permettant de savoir si un utilisateur existe déja selon son username et email
    * @since 2.3 fourth argument id has ben removed
    * @param UserManager $userManager
    * @param string $username
    * @param string $userMail
    */
    private function userAlreadyExist(UserManager $userManager, $username, $userMail)
    {
        $alreadyExist = false;
        $userByUsername = $userManager->findUserByUsername($username);
        $userByUserMail = $userManager->findUserByEmail($userMail);

        if (isset($userByUsername)) {
            $this->get('session')->getFlashBag()->add('notice', "Le nom d'utilisateur est déjà utilisé.");
            $alreadyExist = true;
        }
        if (isset($userByUserMail)) {
            $this->get('session')->getFlashBag()->add('notice', "L'adresse e-mail est déjà utilisée.");
            $alreadyExist = true;
        }

        return $alreadyExist;
    }
}
