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
use Ecedi\Donate\CoreBundle\Entity\Block;
use Ecedi\Donate\CoreBundle\Entity\Layout;
use Ecedi\Donate\CoreBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ecedi\Donate\AdminBundle\Form\LayoutType;
use Ecedi\Donate\FrontBundle\Form\Type\DonationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Routing for layout, block and affectations
 * @since  2.5.0 new Controller refactor from previous BlockController
 */
class LayoutController extends Controller
{
    /**
     * @Route("/cms/layout/{id}/switch" , name="donate_admin_layout_switch", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * ajax callback to switch default Status
     * @Security("is_granted('ROLE_CMS')")
     */
    public function switchLayoutAction(Layout $layout)
    {
        $layoutManager = $this->get('donate_core.layout.manager');

        $layouts = $layoutManager->makeDefault($layout);

        if (count($layouts) == 2) {
            $entityMgr = $this->getDoctrine()->getManager();

            $entityMgr->persist($layouts[0]);
            $entityMgr->persist($layouts[1]);
            $entityMgr->flush();

            $data = array(
                'result' => 'ok',
                'state' => [
                    [ 'id'  => $layouts[0]->getId(), 'value' => $layouts[0]->getIsDefault()],
                    [ 'id'  => $layouts[1]->getId(), 'value' => $layouts[1]->getIsDefault()],
                ],
            );

            return new JsonResponse($data);
        }

        $data = array(
            'result' => 'no-changes',
            'state' => [$layout->getId() => $layout->getIsDefault()],
        );

        return new JsonResponse($data);
    }

    /**
     * @Route("/cms/layout/{id}/preview" , name="donate_admin_layout_preview", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Security("is_granted('ROLE_CMS')")
     */
    public function previewLayoutAction(Request $request, Layout $layout)
    {
        $request->setLocale($layout->getLanguage());

        $form = $this->createForm(DonationType::class, new Customer(), array(
            'civilities' => $this->getParameter('donate_front.form.civility'),
            'equivalences' => $this->get('donate_core.equivalence.factory')->getAll(),
            'payment_methods' => $this->get('donate_core.payment_method_discovery')->getEnabledMethods(),
            'affectations' =>  $layout->getAffectations(),
        ));

        return $this->render('DonateAdminBundle:Block:previewLayout.html.twig', [
            'layout' => $layout,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cms/layouts" , name="donate_admin_layout_list")
     * @Security("is_granted('ROLE_CMS')")
     */
    public function listLayoutsAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Layout');
        $query = $repo->getFindAllQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $request->query->get('page', 1), 20);

        return $this->render('DonateAdminBundle:Block:listLayouts.html.twig', ['layouts' => $pagination]);
    }

    /**
     * @Route("/cms/layout/{id}/edit", name="donate_admin_layout_edit", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Security("is_granted('ROLE_CMS')")
     */
    public function editLayoutAction(Request $request, Layout $layout)
    {
        $form = $this->createForm(LayoutType::class, $layout, [
            'language' => $this->getParameter('donate_front.i18n'),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityMgr = $this->getDoctrine()->getManager();

            //TODO verifier qu'on a bien toujours un seul layout par défaut par langue
            $entityMgr->persist($layout);
            $entityMgr->flush();

            return $this->redirect($this->generateUrl('donate_admin_layout_list'));
        }

        return $this->render('DonateAdminBundle:Block:editLayout.html.twig', [
            'form' =>  $form->createView(),
            'layout' => $layout
        ]);
    }

    /**
     * @Route("/cms/layout/{id}/delete", name="donate_admin_layout_delete", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Security("is_granted('ROLE_CMS')")
     */
    public function deleteLayoutAction(Layout $layout)
    {
        $entityMgr = $this->getDoctrine()->getManager();
        $entityMgr->remove($layout);
        $entityMgr->flush();

        return $this->redirect($this->generateUrl('donate_admin_layout_list'));
    }

    /**
     * @Route("/cms/layout/new", name="donate_admin_layout_new")
     * @Security("is_granted('ROLE_CMS')")
     */
    public function newLayoutAction(Request $request)
    {
        $layout = new Layout();
        $form = $this->createForm(LayoutType::class, $layout, [
            'language' => $this->getParameter('donate_front.i18n'),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityMgr = $this->getDoctrine()->getManager();
            $entityMgr->persist($layout);
            $entityMgr->flush();

            return $this->redirect($this->generateUrl('donate_admin_block_list', ['id' => $layout->getId()]));
        }

        return $this->render('DonateAdminBundle:Block:newLayout.html.twig', [
            'form' =>  $form->createView(),
            'layout' => $layout
        ]);
    }
}
