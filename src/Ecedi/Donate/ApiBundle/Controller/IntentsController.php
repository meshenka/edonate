<?php
/**
 * @author Alexandre Fayolle <alf@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * @NamePrefix("donate_api_v1_")
 *
 * L' annotation ...View(serializerGroups={"REST"}) utilisée ci-dessous permet de retourner
 * seulement les éléments de l'entité qui appartiennent au groupe "REST" (défini dans l'entité)
 * cf: intent Entity et l'annotation ...Groups({"REST"})
 */
class IntentsController extends Controller
{
    /**
     * @return array
     * @View(serializerGroups={"REST"})
     */
    public function getIntentsAction()
    {
        $request = Request::createFromGlobals();
        $restParams = $request->query->All(); // On récupère tous les paramètres passés en GET

        $em = $this->getDoctrine()->getManager();
        $intentRepo = $em->getRepository('DonateCoreBundle:Intent');

        $intents = $intentRepo->findByRestParams($restParams);
        $nbResults = $intentRepo->countAll();

        return [
            'nbResults' => $nbResults,
            'intents' => $intents,
        ];
    }

    /**
     * @param int $intentId
     *
     * @return Intent
     * @View(serializerGroups={"REST"})
     */
    public function getIntentAction($intentId)
    {
        $em = $this->getDoctrine()->getManager();
        $intent = $em->getRepository('DonateCoreBundle:Intent')->find($intentId);

        return [
            'intent' => $intent,
        ];
    }
}
