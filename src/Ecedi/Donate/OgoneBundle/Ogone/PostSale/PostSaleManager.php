<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2014
 * @package Ecollecte
 */

namespace Ecedi\Donate\OgoneBundle\Ogone\PostSale;

use Ecedi\Donate\OgoneBundle\Ogone\Response;
use Ecedi\Donate\OgoneBundle\Exception\UnauthorizedPostSaleException;
use Ecedi\Donate\OgoneBundle\Exception\CannotDetermineOrderIdException;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Psr\Log\LoggerInterface;
use Ecedi\Donate\OgoneBundle\Ogone\StatusNormalizer;
use Ecedi\Donate\CoreBundle\IntentManager\IntentManagerInterface;
/**
 * This mange post-sale calls and make Entity model consistent
 *
 */
class PostSaleManager
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $sha1Out;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var StatusNormalizer
     */
    private $normalizer;

    /**
     * @var IntentManagerInterface
     */
    private $intentManager;

    /**
     * constructor
     * @since  2.2.0 PostSaleHandler is no more ContainerAware and receive all dependencies via contructor
     */
    public function __construct(IntentManagerInterface $intentManager, StatusNormalizer $normalizer, $sha1Out, $prefix, LoggerInterface $logger)
    {
        $this->intentManager = $intentManager;
        $this->normalizer = $normalizer;
        $this->sha1Out =  $sha1Out;
        $this->prefix =  $prefix;
        $this->logger = $logger;
    }

    /**
     * [handle description]
     * @param  Ecedi\Donate\OgoneBundle\Ogone\Response $response the OgoneResponse
     * @return [type]                                  [description]
     * @since  2.2.0 use a Ecedi\Donate\OgoneBundle\Ogone\Response object are argument
     */
    public function handle(Response $response)
    {
        return $this->doHandle($response);
    }

    /**
     * traitement réel du paiement
     *
     * @param  Ecedi\Donate\OgoneBundle\Ogone\Response $response
     * @return PostSaleManager                         this object (for chaining)
     */
    protected function doHandle(Response $response)
    {
        //initialize payment
        $payment = new Payment();
        $payment->setAutorisation($response->getAcceptance()) //n° autorisation
            ->setTransaction($response->getPayId()) //no transaction
            ->setResponseCode($response->getStatus()) //status ogone
            ->setResponse($response);

        $normalizer = $this->container->get('donate_ogone.status_normalizer');

        $payment->setStatus($normalizer->normalize($response->getStatus()));

        try {
            //validate response
            $this->validate($response);
            $this->logger->debug('Payment Status : '.$payment->getStatus());
        } catch (UnauthorizedPostSaleException $e) {
            $this->logger->warning('Incorrectly signed post-sale received');
            $payment->setStatus(Payment::STATUS_INVALID);
        }

        //add payment to intent
        try {
            $intentId  = $this->getIntentId($response);
            $this->logger->debug('found intent id '.$intentId);
        } catch (CannotDetermineOrderIdException $e) {
            $this->logger->warning('CannotDetermineOrderIdException');
            $intentId = false;
                //TODO le payment p-e ok, mais il est orphelin
        }

        $intentMgr = $this->container->get('donate_core.intent_manager');
        $intentMgr->attachPayment($intentId, $payment);

        return $payment;
    }

    /**
     * validation de la signature de la post-sale reçu
     * @param  Response                      $response
     * @return boolean                       true when signatire is valid
     * @throws UnauthorizedPostSaleException If signature is not valid
     */
    protected function validate(Response $response)
    {
        $sha1outkey =  $this->sha1Out;

        $keys = $response->jsonSerialize();

        ksort($keys);

        $hashKey = '';
        foreach ($keys as $key => $val) {
            if ($val != '' && $key != 'SHASIGN') {
                $hashKey .= $key.'='.$val.$sha1outkey;
            }
        }

        $this->logger->debug('signature calculé :'.hash('sha1', $hashKey));
        $this->logger->debug('signature reçu :'.$response->getShasign());

        if (strtoupper(hash('sha1', $hashKey)) === strtoupper($response->getShasign())) {
            return true;
        }

        throw new UnauthorizedPostSaleException();
    }

    /**
     * Extract Intent Id from post-sale orderId
     *
     * @param  Response                        $response
     * @return integer                         the intent Id
     * @throws CannotDetermineOrderIdException If post-sale orderId does not match expected format
     */
    protected function getIntentId(Response $response)
    {
        $orderId = $response->getOrderId();
        $prefix =  $this->prefix;

        if (strpos($orderId, $prefix.'-') === 0) {
            return (int) str_replace($prefix.'-', '', $orderId);
        }

        throw new CannotDetermineOrderIdException();
    }
}
