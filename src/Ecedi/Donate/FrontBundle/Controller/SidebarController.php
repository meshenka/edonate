<?php

namespace Ecedi\Donate\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ecedi\Donate\CoreBundle\Entity\Layout;

class SidebarController extends Controller
{
    public function showAction(Layout $layout, $cache = true)
    {
        $blocks = $layout->getBlocks();

        //cache validation
        $response = new Response();
        if ($cache) {
            // Définit la réponse comme publique. Sinon elle sera privée par défaut.
            $response->setPublic();
            $response->setSharedMaxAge(600);
        }

        return $this->render(':front:sidebar.html.twig', array(
            'blocks' => $blocks,
            ), $response);
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
