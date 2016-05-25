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
use Symfony\Component\HttpFoundation\Request;
use Ecedi\Donate\AdminBundle\Form\BlockType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Routing for layout, block and affectations
 * @since  2.5.0 BlockController only serve block related routes
 */
class BlockController extends Controller
{
    /**
     * @Route("/cms/layout/{id}/blocks" , name="donate_admin_block_list", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Security("is_granted('ROLE_CMS')")
     */
    public function listBlocksAction(Layout $layout)
    {
        return $this->render(':admin/block:list.html.twig', ['layout' => $layout]);
    }

    /**
     * @Route("/cms/layout/{layout}/block/{block}/edit" , name="donate_admin_block_edit", requirements={"layout" = "\d+","block" = "\d+"}, defaults={"layout" = 0, "block" = 0})
     * @Security("is_granted('ROLE_CMS')")
     */
    public function editBlockAction(Request $request, Layout $layout, Block $block)
    {
        $form = $this->createForm(BlockType::class, $block);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityMgr = $this->getDoctrine()->getManager();
            $entityMgr->persist($block);
            $entityMgr->flush();

            return $this->redirect($this->generateUrl('donate_admin_block_list', [ 'id' => $layout->getId()]));
        }

        return $this->render(':admin/block:edit.html.twig', [
            'form' =>  $form->createView(),
            'block' => $block
        ]);
    }
}
