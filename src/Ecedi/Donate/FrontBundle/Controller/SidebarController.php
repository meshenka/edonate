<?php

namespace Ecedi\Donate\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ecedi\Donate\CoreBundle\Entity\Layout;


class SidebarController extends Controller
{
    public function showAction(Request $request, Layout $layout, $cache = true)
    {

        //$repo = $this->getDoctrine()->getManager()->getRepository('DonateCoreBundle:Layout);
        //$layout = $this->get('donate_core.layout.manager')->getDefault($request->getLocale());

        $blocks = $layout->getBlocks();

        //cache validation
        $response = new Response();
        // $response->setEtag($this->computeEtag($blocks));
        // $response->setVary('Accept-Encoding');

        if($cache) {
            // Définit la réponse comme publique. Sinon elle sera privée par défaut.
            $response->setPublic();
            $response->setSharedMaxAge(600);
        }
        // Vérifie que l'objet Response n'est pas modifié
        // pour un objet Request donné
        // if ($response->isNotModified($request)) {
        //     // Retourne immédiatement un objet 304 Response
        //     return $response;
        // }

        return $this->render('DonateFrontBundle::sidebar.html.twig', array(
            'blocks' => $blocks,
            ), $response);

        //$response->setPublic()->setMaxAge(3600)->setSharedMaxAge(3600);
    }

    protected function computeEtag($blocks)
    {
        foreach ($blocks as $b) {
            $etagElements[] = $b->getId();
            $etagElements[] = $b->getChangedAt()->format('Y-m-d H:i:s');
        }

        return md5(implode('-', $etagElements));
    }
}
