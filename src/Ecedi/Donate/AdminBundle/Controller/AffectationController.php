<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ecedi\Donate\CoreBundle\Entity\Layout;
use Ecedi\Donate\CoreBundle\Entity\Affectation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ecedi\Donate\AdminBundle\Form\AffectationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @since  2.0.0
 */
class AffectationController extends Controller
{
    /**
     * @Route("/cms/layout/{layout}/affectations" , name="donate_admin_affectation_show", requirements={"layout" = "\d+"}, defaults={"layout" = 0})
     * @Security("is_granted('ROLE_AFFECTATION')")
     */
    public function showAction(Layout $layout)
    {
        return $this->render(':admin/affectation:show.html.twig', [
            'layout' => $layout,
        ]);
    }

    /**
     * @Route("/cms/layout/{layout}/affectations/add" , name="donate_admin_affectation_add", requirements={"layout" = "\d+"}, defaults={"layout" = 0})
     * @Security("is_granted('ROLE_AFFECTATION')")
     */
    public function addAction(Request $request, Layout $layout)
    {
        $affectation = new Affectation();

        $form = $this->createForm(AffectationType::class, $affectation);

        $form->handleRequest($request);

        if ($form->isValid()) {
            //TODO verifier que le code n'est pas déjà pris pour ce layout
            //
            $em = $this->getDoctrine()->getManager();

            $layout->addAffectations($affectation);
            $em->persist($layout);
            $em->flush();

            return $this->redirect($this->generateUrl('donate_admin_affectation_show', array('layout' => $layout->getId())));
        }

        return $this->render(':admin/affectation:add.html.twig', [
            'form' => $form->createView(),
            'layout' => $layout,
        ]);
    }

    /**
     * @Route("/cms/layout/{layout}/affectations/{affectation}/edit" , name="donate_admin_affectation_edit", requirements={"layout" = "\d+", "affectation" = "\d+"}, defaults={"layout" = 0, "affectation" = 0})
     * @Security("is_granted('ROLE_AFFECTATION')")
     */
    public function editAction(Request $request, Layout $layout, Affectation $affectation)
    {
        $form = $this->createForm(AffectationType::class, $affectation);

        $form->handleRequest($request);

        if ($form->isValid()) {
            //TODO verifier que le code n'est pas déjà pris pour ce layout
            $em = $this->getDoctrine()->getManager();

            $em->persist($affectation);
            $em->flush();

            return $this->redirect($this->generateUrl('donate_admin_affectation_show', array('layout' => $layout->getId())));
        }

        return $this->render(':admin/affectation:edit.html.twig', [
            'form' => $form->createView(),
            'layout' => $layout,
            'affectation' => $affectation,
        ]);
    }

    /**
     * @Route("/cms/layout/{layout}/affectations/{id}/delete" , name="donate_admin_affectation_delete", requirements={"layout" = "\d+", "id" = "\d+"}, defaults={"layout" = 0})
     * @Security("is_granted('ROLE_AFFECTATION')")
     */
    public function deleteAction(Layout $layout, Affectation $affectation)
    {
        $em = $this->getDoctrine()->getManager();

        $layout->removeAffectations($affectation);
        $em->remove($affectation);
        $em->persist($layout);
        $em->flush();

        return $this->redirect($this->generateUrl('donate_admin_affectation_show', array('layout' => $layout->getId())));
    }

    /**
     * @Route("/cms/layout/{layout}/affectations/sort" , name="donate_admin_affectation_sort", requirements={"layout" = "\d+","methods" = "POST"}, defaults={"layout" = 0})
     * @Security("is_granted('ROLE_AFFECTATION')")
     */
    public function sortAction(Request $request, Layout $layout)
    {
        $post = $request->request->all();
        $weight = 0;
        $affectations = $layout->getAffectations();

        $idWeight = array();

        foreach ($post['affectation'] as $id) {
            $idWeight[$id] = $weight;
            $weight += 10;
        }

        $em = $this->getDoctrine()->getManager();

        foreach ($affectations as $a) {
            $a->setWeight($idWeight[$a->getId()]);
            $em->persist($a);
        }

        $em->flush();

        return new JsonResponse(array('status' => 'ok'));
    }
}
