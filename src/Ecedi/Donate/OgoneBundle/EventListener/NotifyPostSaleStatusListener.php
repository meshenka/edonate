<?php
/**
 * @author sgogel@ecedi.fr
 */
namespace Ecedi\Donate\OgoneBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Ecedi\Donate\OgoneBundle\Ogone\Response;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\PaymentReceivedEvent;

/**
 * Ce Subscriber envoi des emails lors de la réception de post-sale quand le code status de la réponse Ogone
 * Indique qu'une action humaine est nécessaire.
 * TODO attention avec les evenements il faut les traiter que quand cela est pertinant
 *
 */
class NotifyPostSaleStatusListener extends ContainerAware implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(DonateEvents::PAYMENT_RECEIVED => array(
                array('onPostSale', 10),
            ),
        );
    }

    /**
     * Réaction à la post-sale, on vérifie que le code de retour est sur 2 digits et on test le dernier pour
     * définir le message à envoyer
     *
     * @param
     */
    public function onPostSale(PaymentReceivedEvent $event)
    {
        //send email to webmaster on certain response code
        $status = $event->getPayment()->getResponse()->getStatus();

        if (strlen($status) == 2) {
            if (substr($status, -1) == '2') {
                $this->sendErrorMessage($event->getPayment()->getResponse());
            }

            if (substr($status, -1) == '3') {
                $this->sendRefusedMessage($event->getPayment()->getResponse());
            }
        }
        $this->container->get('logger')->debug('status test called');
    }

    protected function sendErrorMessage(Response $response)
    {
        $msg = 'Cela signifie qu\'une erreur irrécupérable s\'est produite lors de la communication avec l\'acquéreur. Le résultat n\'est donc pas déterminée. Vous devez donc appeler le service assistance de l\'acquéreur pour connaitre le résultat réel de cette transaction.';

        $this->sendMail($response, $msg);
    }

    protected function sendRefusedMessage(Response $response)
    {
        $msg = 'Cela signifie que le traitement du paiements (capture ou annulation) a été refusé par l\'acquéreur, tandis que le paiement avait été préalablement autorisée. Cela peut être due à une erreur technique ou à l\'expiration de l\'autorisation. Vous devez donc appeler le service assistance de l\'acquéreur pour découvrir le résultat réel de cette transaction.';
        $this->sendMail($response, $msg);
    }

    protected function sendMail(Response $response, $body)
    {
        if ($this->container->getParameter('donate_core.mail.webmaster')) {
            $message = \Swift_Message::newInstance()
            ->setSubject('[ECollecte] une post-sale Ogone require votre attention')
            ->setFrom('ecollecte@ecedi.fr')
            ->setTo($this->container->getParameter('donate_core.mail.webmaster'))
            ->setBody(
                $this->container->get('templating')->render(
                    'DonateOgoneBundle:Mail:ogone.txt.twig',
                    array('response' => $response, 'message' => $body)
                )
            );

            $this->container->get('mailer')->send($message);
        }
    }
}
