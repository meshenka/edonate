<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2014
 * @package eDonate
 */
namespace Ecedi\Donate\OgoneBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ecedi\Donate\OgoneBundle\Ogone\Response;
use Ecedi\Donate\OgoneBundle\OgoneEvents;
use Ecedi\Donate\OgoneBundle\Event\PostSaleEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Templating\EngineInterface;
/**
 * Ce Subscriber envoi des emails lors de la réception de post-sale quand le code status de la réponse Ogone
 * Indique qu'une action humaine est nécessaire.
 *
 * @since  2.2.0 listen to OgoneEvents::POSTSALE
 * @since  2.3.0 no more extends Symfony\Component\DependencyInjection\ContainerAware
 *
 */
class NotifyPostSaleStatusListener implements EventSubscriberInterface
{
    /**
     *
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     *
     * @var string
     */
    private $webmasterEmail;

    /**
     * Template Engine
     * @var Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @since  2.3 use constructor arguments instead of ContainerAware
     * @param LoggerInterface $logger [description]
     */
    public function __construct(EngineInterface $templating, $webmasterEmail, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->templating = $templating;
        $this->webmasterEmail = $webmasterEmail;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return array(OgoneEvents::POSTSALE => array(
                array('onPostSale', 10),
            ),
        );
    }

    /**
     * Réaction à la post-sale, on vérifie que le code de retour est sur 2 digits et on test le dernier pour
     * définir le message à envoyer
     *
     * @param PostSaleEvent $event the post sale Event
     * @since  2.2.0 it listen to OgoneEvents::POSTSALE instead of DonateEvents::PAYMENT_RECEIVED as it is Ogone Specific
     */
    public function onPostSale(PostSaleEvent $event)
    {
        //send email to webmaster on certain response code
        $status = $event->getResponse()->getStatus();

        if (strlen($status) == 2) {
            if (substr($status, -1) == '2') {
                $this->sendErrorMessage($event->getResponse());
            }

            if (substr($status, -1) == '3') {
                $this->sendRefusedMessage($event->getResponse());
            }
        }
        $this->logger->debug('status test called');
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
        $message = \Swift_Message::newInstance()
            ->setSubject('[eDonate] une post-sale Ogone require votre attention')
            ->setFrom('edonate@ecedi.fr')
            ->setTo($this->webmasterEmail)
            ->setBody(
                $this->templating->render(
                    'DonateOgoneBundle:Mail:ogone.txt.twig',
                    array('response' => $response, 'message' => $body)
                )
            );

        $this->mailer->send($message);
    }
}
