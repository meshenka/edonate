<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @author Alexandre Fayolle <alf@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\AdminBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;

/**
 * Build menu for Admin
 *
 * @since  2.4 refactor the way parameters are found from the request by using request attributes instead of request GET parameters
 * @since  2.4 refactor code to reduce complexity
 */
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
    public function adminMenu(FactoryInterface $factory)
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
        // @since 2.3 we user voters to check authorization instead of being ROLE based
        // @since 2.4.7 we use ROLE_ADMIN as User Manager
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->addUserMenuItems($menu);
        }
        // Mon compte
        $this->addMyAccountMenuItems($menu);

        // CMS
        // @since 2.3 we user voters to check authorization instead of being ROLE based
        // @since 2.4.7 we use ROLE_CMS as Layout / Block / Affectation management
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_CMS')) {
            $this->addCmsMenuItems($menu);
        }

        // Logout
        $menu->addChild($trans->trans('Disconnect'), array('route' => 'fos_user_security_logout'))
            ->setAttribute('data-icon', 'glyphicon glyphicon-log-out');

        return $menu;
    }

    /**
     * extract a value from the current route in the request
     *
     * @param  string $param   name of the route param
     * @param  mixed  $default default value if param is not found
     * @return mixed  the value
     */
    protected function getRouteParam($param, $default = false)
    {
        $request = $this->container->get('request');

        return (isset($request->attributes->get('_route_params')[$param])) ? $request->attributes->get('_route_params')[$param] : $default;
    }

    private function addCmsLayoutBlockMenuItems($menu)
    {
        $id = $this->getRouteParam('id', 0);
        $layout = $this->getRouteParam('layout', $id);
        $block = $this->getRouteParam('block', $id);

        $menu['CMS']['Editer Gabarit']->addChild('Editer Block', array(
            'route' => 'donate_admin_block_edit',
            'routeParameters' => array(
                'layout' => $layout,
                'block' => $block,
            ), ))
            ->setAttribute('data-icon', 'glyphicon glyphicon-edit');

        $menu['CMS']['Editer Gabarit']->addChild('Voir Block', array(
            'route' => 'donate_admin_block_list',
            'routeParameters' => array(
                'id' => $layout,
            ), ))
            ->setAttribute('data-icon', 'glyphicon glyphicon-info-sign');
    }

    private function addCmsLayoutAffectationsMenuItems($menu)
    {
        $id = $this->getRouteParam('id', 0);
        $layout = $this->getRouteParam('layout', $id);
        $affectation = $this->getRouteParam('affectation', $id);

        $menu['CMS']['Editer Gabarit']->addChild('Voir Affectations', array(
            'route' => 'donate_admin_affectation_show',
            'routeParameters' => array(
                'layout' => $layout,
            ), ))
            ->setAttribute('data-icon', 'glyphicon glyphicon-map-marker');

        $menu['CMS']['Editer Gabarit']->addChild('Nouvelle affectation', array(
            'route' => 'donate_admin_affectation_add',
            'routeParameters' => array(
                'layout' => $layout,
            ), ))
            ->setAttribute('data-icon', 'glyphicon glyphicon-map-marker');

        $menu['CMS']['Editer Gabarit']->addChild('Editer Affectations', array(
            'route' => 'donate_admin_affectation_edit',
            'routeParameters' => array(
                'layout' => $layout,
                'affectation' => $affectation,
            ), ))
            ->setAttribute('data-icon', 'glyphicon glyphicon-map-marker');
    }

   /**
    * Ajout des éléments du menu relatif au CMS
    *
    * @param $menu \Knp\Menu\MenuItem
    */
    private function addCmsMenuItems($menu)
    {
        $id = $this->getRouteParam('id', 0);
        $layout = $this->getRouteParam('layout', $id);

        $menu->addChild('CMS', array('route' => 'donate_admin_layout_list'))
            ->setAttribute('data-icon', 'glyphicon glyphicon-pencil') // Permet l'ajout d'une icone (via un span) dans le lien
            ->setDisplayChildren(false);

        $menu['CMS']->addChild('Nouveau Gabarit', array('route' => 'donate_admin_layout_new'));
        $menu['CMS']->addChild('Editer Gabarit', array(
            'route' => 'donate_admin_layout_edit',
            'routeParameters' => array(
                'id' => $layout,
            ), ))
            ->setAttribute('data-icon', 'glyphicon glyphicon-edit');

        //blocks
        $this->addCmsLayoutBlockMenuItems($menu);

        //Affectations
        $this->addCmsLayoutAffectationsMenuItems($menu);
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
        $customerId =  $this->getRouteParam('id', 0);

        $menu->addChild('Donateurs', array('route' => 'donate_admin_reporting_customers'))
             ->setAttribute('data-icon', 'glyphicon glyphicon-info-sign') // Permet l'ajout d'une icone (via un span) dans le lien
             ->setDisplayChildren(false);
        // Enfants N2 -> N3
        $menu['Donateurs']
            ->addChild('Détail du donateur', array(
                'route'           => 'donate_admin_reporting_customer_show',
                'routeParameters' => array('id' => $customerId),
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
