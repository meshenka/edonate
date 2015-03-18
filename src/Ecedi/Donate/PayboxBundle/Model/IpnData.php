<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package Ecollecte
 */

namespace Ecedi\Donate\PayboxBundle\Model;

/**
 * IpnData is a simple Wrapper around Paybox Ipn response
 *
 * this class is statefull, it cannot be a service
 * It is a Value Object, immutable
 *
 * @since 2.2.0
 */
class IpnData
{
    /**
     * Paybox Response Variables in array.
     *
     * @var array $data
     */
    private $data;

    /**
     * Instanciate a reponse
     *
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * Retrieve all the response's datas.
     *
     * @return array $datas
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * Retrieve the total amount of the transaction.
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->data['M'];
    }
    /**
     * Retrieve the order identifier of the transaction.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->data['R'];
    }
    /**
     * Retrieve the transaction unique identifier.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['T'];
    }
    /**
     * Retrieve the authorisation identifier of the transaction.
     *
     * @return string
     */
    public function getAuthorisationId()
    {
        return $this->data['A'];
    }
    /**
     * Retrieve the subscription identifier.
     *
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->data['B'];
    }
    /**
     * Retrieve the specified payment method.
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->data['P'];
    }
    /**
     * Retrieve the credit card type used for the transaction.
     *
     * @return string
     */
    public function getCreditCardType()
    {
        return $this->data['C'];
    }
    /**
     * Retrieve the unique transaction id.
     *
     * @return string
     */
    public function getSoleTransactionId()
    {
        return $this->data['S'];
    }
    /**
     * Retrieve the normalized country code.
     *
     * @return string An ISO 3166-1 code
     */
    public function getCountryCode()
    {
        return $this->data['Y'];
    }
    /**
     * Retrieve error code.
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->data['E'];
    }
    /**
     * Retrieve the expiration card date.
     *
     * @return string
     */
    public function getExpirationCardDate()
    {
        return $this->data['D'];
    }
    /**
     * Subscription management with the PAYBOX DIRECT Plus process. Url encoded
     *
     * @return string
     */
    public function getSubscriptionManagement()
    {
        return $this->data['U'];
    }
    /**
     * Retrieve the geolocalized IP.
     *
     * @return string
     */
    public function getIpCountryCode()
    {
        return $this->data['I'];
    }
    /**
     * Retrieve the hashmac of the transaction.
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->data['K'];
    }
    /**
     * The first 6 digits (« bin6 ») of the cardholder
     *
     * @return string
     */
    public function getBin6()
    {
        return $this->data['N'];
    }
    /**
     * This is Digest (patch this comment if you get more informations from the
     * doc)
     *
     * @return string
     */
    public function getDigest()
    {
        return $this->data['H'];
    }
    /**
     * Retrieve the guarantee applicable to the payment.
     *
     * @return string
     */
    public function getPaymentGuaranteed()
    {
        return $this->data['G'];
    }
    /**
     * State of the enrolment of the cardholder. Y:Authentification available,
     * N:Cardholder not participating, U:Unable to authenticate
     *
     * @return string
     */
    public function getEnrolment()
    {
        return $this->data['O'];
    }
    /**
     * Retrieve the auth status.
     *
     * @return string
     */
    public function getAuthenticationStatus()
    {
        return $this->data['F'];
    }
    /**
     * Retrieve the 4 last numbers of the card.
     *
     * @return string
     */
    public function getLastPanDigits()
    {
        return $this->data['J'];
    }
    /**
     * Retrieve the date of the transaction.
     *
     * @return string
     */
    public function getTransactionDate()
    {
        return $this->data['W'];
    }
    /**
     * Retrieve the gift index.
     *
     * @return string
     */
    public function getGiftIndex()
    {
        return $this->data['Z'];
    }
    /**
     * Retrieve the transaction time.
     *
     * @return string
     */
    public function getTransactionTime()
    {
        return $this->data['Q'];
    }
    /**
     * Retrieve the Ecollecte Intent ID
     *
     * @return string
     */
    public function getIntentId()
    {
        $intentId = str_replace('DON-', '', $this->data['R']);

        return is_numeric($intentId) ? $intentId : false;
    }
}
