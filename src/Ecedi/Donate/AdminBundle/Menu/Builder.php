<?php
namespace Ecedi\Donate\AdminBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;

class Builder extends ContainerAware
{
    /**
     * adminMenu builder
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\MenuItem
    */
    public function adminMenu(FactoryInterface $factory, array $options)
    {
        $trans = $this->container->get('translator');

        $menu = $factory->createItem('adminMenu');
        $menu->setChildrenAttributes(array('class' => 'nav navbar-nav')); // classe posée sur le ul du menu

        // BO Accueil
        $menu->addChild($trans->trans('Homepage'), array('route' => 'donate_admin_dashboard'))
             ->setAttribute('data-icon', 'glyphicon glyphicon-home'); // Permet l'ajout d'une icone (via un span) dans le lien
        // DONS
        $this->addIntentMenuItems($menu);
        // Donateurs
        $this->addCustomerMenuItems($menu);
        // Utilisateurs
        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->addUserMenuItems($menu);
        }
        // Mon compte
        $this->addMyAccountMenuItems($menu);

        // CMS
        $this->addCmsMenuItems($menu);

        // Logout
        $menu->addChild($trans->trans('Disconnect'), array('route' => 'fos_user_security_logout'))
            ->setAttribute('data-icon', 'glyphicon glyphicon-log-out');

        return $menu;
    }

   /**
    * Ajout des éléments du menu relatif au CMS
    *
    * @param $menu \Knp\Menu\MenuItem
    */
    private function addCmsMenuItems($menu)
    {
        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $request = $this->container->get('request');
            $id = !is_null($request->get('id')) ? $request->get('id') : 0;
            $layout = !is_null($request->get('layout')) ? $request->get('layout') : false;

            $block = !is_null($request->get('block')) ? $request->get('block') : false;

            $menu->addChild('CMS', array('route' => 'donate_admin_layout_list'))
                ->setAttribute('data-icon', 'glyphicon glyphicon-pencil') // Permet l'ajout d'une icone (via un span) dans le lien
                ->setDisplayChildren(false);

            $menu['CMS']->addChild('Nouveau Gabarit', array('route' => 'donate_admin_layout_new'));
            $menu['CMS']->addChild('Editer Gabarit', array(
                'route' => 'donate_admin_layout_edit',
                'routeParameters' => array(
                    'id' => ($layout) ? $layout->getId() : $id,
                ), ))
                ->setAttribute('data-icon', 'glyphicon glyphicon-edit');

            $menu['CMS']['Editer Gabarit']->addChild('Editer Block', array(
                'route' => 'donate_admin_block_edit',
                'routeParameters' => array(
                    'layout' => ($layout) ? $layout->getId() : $id,
                    'block' => ($block) ? $block->getId() : $id,
                ), ))
                ->setAttribute('data-icon', 'glyphicon glyphicon-edit');

            $menu['CMS']['Editer Gabarit']->addChild('Voir Block', array(
                'route' => 'donate_admin_block_list',
                'routeParameters' => array(
                    'id' => ($layout) ? $layout->getId() : $id,
                ), ))
                ->setAttribute('data-icon', 'glyphicon glyphicon-info-sign');
        }
    }

    /**
    * Ajout des éléments du menu relatif au don
    *
    * @param $menu \Knp\Menu\MenuItem
    */
    private function addIntentMenuItems($menu)
    {
        $menu->addChild('Dons', array('route' => 'donate_admin_reporting_intents'))
             ->setAttribute('data-icon', 'glyphicon glyphicon-euro') // Permet l'ajout d'une icone (via un span) dans le lien
             ->setDisplayChildren(false);
        // Enfants N2
        $menu['Dons']->addChild('Détail don', array('route' => 'donate_admin_reporting_intent_show'));
    }

    /**
    * Ajout des éléments du menu relatif au donateur
    *
    * @param $menu \Knp\Menu\MenuItem
    */
    private function addCustomerMenuItems($menu)
    {
        $request = $this->container->get('request');
        $id = !is_null($request->get('id')) ? $request->get('id') : 0; // paramètres par défault pour les routes de détail

        $menu->addChild('Donateurs', array('route' => 'donate_admin_reporting_customers'))
             ->setAttribute('data-icon', 'glyphicon glyphicon-info-sign') // Permet l'ajout d'une icone (via un span) dans le lien
             ->setDisplayChildren(false);
        // Enfants N2 -> N3
        $menu['Donateurs']
            ->addChild('Détail du donateur', array(
                'route'           => 'donate_admin_reporting_customer_show',
                'routeParameters' => array('id' => $id),
            ))
                ->addChild("Editer", array('route' => 'donate_admin_reporting_customer_edit')); // N3
    }

    /**
    * Ajout des éléments de gestions des utilisateurs
    *
    * @param $menu \Knp\Menu\MenuItem
    */
    private function addUserMenuItems($menu)
    {
        $menu->addChild('Utilisateurs', array('route' => 'donate_admin_users'))
             ->setAttribute('data-icon', 'glyphicon glyphicon-user') // Permet l'ajout d'une icone (via un span) dans le lien
             ->setDisplayChildren(false);
        // Enfants N2
        $menu['Utilisateurs']->addChild("Détails de l'utilisateur", array('route' => 'donate_admin_user_edit'));
        $menu['Utilisateurs']->addChild('Ajouter un utilisateur', array('route' => 'donate_admin_user_new'));
    }

    /**
    * Ajout des éléments du menu relatif  au compte du current user
    *
    * @param $menu \Knp\Menu\MenuItem
    */
    private function addMyAccountMenuItems($menu)
    {
        $menu->addChild('Mon compte', array('route' => 'fos_user_profile_edit'))
             ->setAttribute('data-icon', 'glyphicon glyphicon-user') // Permet l'ajout d'une icone (via un span) dans le lien
             ->setDisplayChildren(false);
        // Enfants N2
        $menu['Mon compte']->addChild('Modification mot de passe', array('route' => 'fos_user_change_password'));
    }
}
