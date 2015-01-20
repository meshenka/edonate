<?php

namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ecedi\Donate\CoreBundle\Entity\Block;
use Ecedi\Donate\CoreBundle\Entity\Layout;
use Ecedi\Donate\CoreBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlockController extends Controller
{
    /**
     * @Route("/cms/layout/{id}/switch" , name="donate_admin_layout_switch", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Template()
     * ajax callback to switch default Status
     */
    public function switchLayoutAction(Request $request, Layout $layout)
    {
        $layoutManager = $this->get('donate_core.layout.manager');

        $layouts = $layoutManager->makeDefault($layout);

        if (count($layouts) == 2) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($layouts[0]);
            $em->persist($layouts[1]);
            $em->flush();

            $data = array(
                'result' => 'ok',
                'state' => [
                    [ 'id'  => $layouts[0]->getId(), 'value' => $layouts[0]->getIsDefault()],
                    [ 'id'  => $layouts[1]->getId(), 'value' => $layouts[1]->getIsDefault()],
                ],
            );
        } else {
            $data = array(
                'result' => 'no-changes',
                'state' => [$layout->getId() => $layout->getIsDefault()],
            );
        }

        $response = new JsonResponse($data);

        return $response;
    }

    /**
     * @Route("/cms/layout/{id}" , name="donate_admin_layout_show", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Template()
     */
    public function showLayoutAction(Request $request, Layout $layout)
    {
        $data = new Customer();

        $form = $this->createForm('donate', $data);

        return ['layout' => $layout];
    }

    /**
     * @Route("/cms/layout/{id}/preview" , name="donate_admin_layout_preview", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Template()
     */
    public function previewLayoutAction(Request $request, Layout $layout)
    {
        $request->setLocale($layout->getLanguage());

        $data = new Customer();
        $form = $this->createForm('donate', $data);

        return [
            'layout' => $layout,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/cms/layout/{id}/blocks" , name="donate_admin_block_list", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Template()
     */
    public function listBlocksAction(Request $request, Layout $layout)
    {
        return ['layout' => $layout];
    }

    /**
     * @Route("/cms/layouts" , name="donate_admin_layout_list")
     * @Template()
     */
    public function listLayoutsAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Layout');
        $query = $repo->getFindAllQuery();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $request->query->get('page', 1), 20);

        return ['layouts' => $pagination];
    }

    /**
     * @Route("/cms/layout/{id}/edit", name="donate_admin_layout_edit", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Template()
     */
    public function editLayoutAction(Request $request, Layout $layout)
    {
        $form = $this->createForm('layout', $layout);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //TODO verifier qu'on a bien toujours un seul layout par défaut par langue
            $em->persist($layout);
            $em->flush();

            return $this->redirect($this->generateUrl('donate_admin_layout_list'));
        }

        return [
            'form' =>  $form->createView(),
            'layout' => $layout
        ];
    }

    /**
     * @Route("/cms/layout/{id}/delete", name="donate_admin_layout_delete", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Template()
     */
    public function deleteLayoutAction(Request $request, Layout $layout)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($layout);
        $em->flush();

        return $this->redirect($this->generateUrl('donate_admin_layout_list'));
    }

    /**
     * @Route("/cms/layout/new", name="donate_admin_layout_new")
     * @Template()
     */
    public function newLayoutAction(Request $request)
    {
        $layout = new Layout();
        $form = $this->createForm('layout', $layout);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($layout);
            $em->flush();

            return $this->redirect($this->generateUrl('donate_admin_block_list', ['id' => $layout->getId()]));
        }

        return [
            'form' =>  $form->createView(),
            'layout' => $layout
        ];
    }

    /**
     * @Route("/cms/layout/{layout}/block/{block}/edit" , name="donate_admin_block_edit", requirements={"layout" = "\d+","block" = "\d+"}, defaults={"layout" = 0, "block" = 0})
     * @Template()
     */
    public function editBlockAction(Request $request, Layout $layout, Block $block)
    {
        $form = $this->createForm('block', $block);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($block);
            $em->flush();

            return $this->redirect($this->generateUrl('donate_admin_block_list', [ 'id' => $layout->getId()]));
        }

        return [
            'form' =>  $form->createView(),
            'block' => $block
        ];
    }
}
