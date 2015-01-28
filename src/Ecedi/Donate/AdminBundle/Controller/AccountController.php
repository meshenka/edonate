<?php

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\AdminBundle\Form\AccountType;
use Ecedi\Donate\CoreBundle\Entity\User as User;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Util\UserManipulator;

class AccountController extends Controller
{
    /**
     * @Route("/users" , name="donate_admin_users")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('DonateCoreBundle:User')->getAllUsers();
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
        $roles = $this->getAvailabledRoles();
        //$user = $userManager->findUserBy(['id' => $id]);
        // Un super-admin ne peut être édité que par un super-admin
        // TODO revoir ce code, faire un Voter?
        $currentUser = $this->getUser();
        if ($user->hasRole('ROLE_SUPER_ADMIN') && !$currentUser->hasRole('ROLE_SUPER_ADMIN')) {
            $this->get('session')->getFlashBag()->add('notice', "Vous n'êtes pas autorisé à modifier cet utilisateur.");

            return $this->redirect($this->generateUrl('donate_admin_users'));
        }

        $form = $this->createForm(new AccountType($roles, $request->get('_route')), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            if ($form->get('submit_delete')->isClicked()) {
                $userManager->deleteUser($user);
                $this->get('session')->getFlashBag()->add('notice', "L'utilisateur ".$user->getUsername()." a été supprimé.");
            } else {
                $data = $form->getData();
                if (!$this->userAlreadyExist($userManager, $data->getUsername(), $data->getEmail(), $id)) {
                    $userManager->updateUser($user);
                    $this->get('session')->getFlashBag()->add('notice', "L'utilisateur ".$user->getUsername()." a été mis à jour.");
                }
            }

            return $this->redirect($this->generateUrl('donate_admin_users'));
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
        $roles = $this->getAvailabledRoles();
        $form = $this->createForm(new AccountType($roles, $request->get('_route')), new User());
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
    *
    * @return $roles -- tableau contenant les rôles povant être assigné par un administrateur
    */
    private function getAvailabledRoles()
    {
        $roles = [];
        $rolesHierarchy = $this->container->getParameter('security.role_hierarchy.roles');
        // Un utilisateur ne peut pas assigner ces rôles à d'autres utilisateur
        // ! Le ROLE_SUPER_ADMIN ne doit etre créé qu'en ligne de commandes !
        $unAvailabledRoles = ['ROLE_SUPER_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'];

        // Parcours de l'arbre de la hiérachie des rôles
        foreach ($rolesHierarchy as $parentRole => $row) {
            if (!in_array($parentRole, $unAvailabledRoles)) {
                $roles[$parentRole] = $parentRole;
            }
            foreach ($row as $childRole) {
                if (!in_array($childRole, $unAvailabledRoles)) {
                    $roles[$childRole] = $childRole;
                }
            }
        }

        $roles = array_unique($roles); // Suppression de la redondance des roles (si plusieurs parents ont des enfants communs)

        return $roles;
    }

    /**
    * Fonction permettant de savoir si un utilisateur existe déja selon son username et email
    *
    * @param UserManager $userManager
    * @param string $username
    * @param string $userMail
    * @param int $id -- Facultatif -- id de l'utilisateur en cours d'édition (pour l'exclure du résultat)
    */
    private function userAlreadyExist(UserManager $userManager, $username, $userMail, $id = false)
    {
        $alreadyExist = false;
        $userByUsername = $userManager->findUserByUsername($username);
        $userByUserMail = $userManager->findUserByEmail($userMail);

        if (isset($userByUsername) && $userByUsername->getId() != $id) {
            $this->get('session')->getFlashBag()->add('notice', "Le nom d'utilisateur est déjà utilisé.");
            $alreadyExist = true;
        }
        if (isset($userByUserMail) && $userByUserMail->getId() != $id) {
            $this->get('session')->getFlashBag()->add('notice', "L'adresse e-mail est déjà utilisée.");
            $alreadyExist = true;
        }

        return $alreadyExist;
    }
}
