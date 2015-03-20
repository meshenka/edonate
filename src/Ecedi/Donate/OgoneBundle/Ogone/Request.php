<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2014
 * @package Ecollecte
 */

namespace Ecedi\Donate\OgoneBundle\Ogone;

class Request implements \JsonSerializable
{
    private $method; //methode de soumission GET|POST
    private $action; //url de soumission

    private $orderId; //numéro de commande
    private $sha1in; //clef de validation IN
    private $pspid; //pspid du client
    private $pm; //payment method
    private $currency; //devise
    private $cn; //nom du customer
    private $email; //email du donateur
    private $ownerZip; //code postalset
    private $ownerAddress; //adresse
    private $ownerCty; //Pays
    private $ownerTown; //ville
    private $language; //langue de l'interface ogone
    private $complus; //libre
    private $acceptUrl; //url
    private $cancelUrl; //url
    private $declineUrl; //url
    private $backUrl; //url
    private $logo; //optionnel url https d'un logo
    private $tp; //optionnel url https d'une template custom
    private $amount; //montant en centimes
    private $operation; //type d'operation SAL (vente) RES (autorisation)
    private $aliasusage; //optionnel en sRES, texte usage de l'alias
    private $aliasoperation; //optionnel comment est généré l'alias (BYOGONE)

    /**
     * Sérialisation en tableau pour faciliter le json_encode
     * @see http://php.net/manual/en/class.jsonserializable.php
     *
     * Penser à ajouter touts nouvel attribut de la classe dans cette méthode car elle
     * sert pour calculé la clef sha1 in de la transactions
     *
     * @return array an array version of the instance
     */
    public function jsonSerialize()
    {
        return  [
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->getCurrency(),
            'OPERATION' => $this->getOperation(),
            'ORDERID' => $this->getOrderId(),
            'PSPID' => $this->getPspId(),
            'PM' => $this->getPm(),
            'CN' => $this->getCn(),
            'EMAIL' => $this->getEmail(),
            'OWNERADDRESS' => $this->getOwnerAddress(),
            'OWNERZIP' => $this->getOwnerZip(),
            'OWNERTOWN' => $this->getOwnerTown(),
            'OWNERCTY' => $this->getOwnerCty(),
            'ACCEPTURL' => $this->getAcceptUrl(),
            'BACKURL' => $this->getBackUrl(),
            'CANCELURL' => $this->getCancelUrl(),
            'DECLINEURL' => $this->getDeclineUrl(),
            'LANGUAGE' => $this->getLanguage(),
        ];
    }

    /**
     * address
     *
     * @return string address
     */
    public function getOwnerAddress()
    {
        return $this->ownerAddress;
    }

    /**
     * address
     *
     * @param String $newownerAddress Address
     */
    public function setOwnerAddress($ownerAddress)
    {
        $this->ownerAddress = $ownerAddress;

        return $this;
    }
    /**
     * method
     *
     * @return string form method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * method
     *
     * @param String $newmethod Form method
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * action
     *
     * @return string form action url
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * action
     *
     * @param String $newaction Form action url
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }
    /**
     * operation
     *
     * @return string operation
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * operation
     *
     * @param String $newoperation Operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * amount in cents
     *
     * @return string montant en centimes
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * amount in cents
     *
     * @param String $newamount Montant en centimes
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * template
     *
     * @return string url https d'une template
     */
    public function getTp()
    {
        return $this->tp;
    }

    /**
     * template
     *
     * @param String $newtp Url https d'une template
     */
    public function setTp($tp)
    {
        $this->tp = $tp;

        return $this;
    }

    /**
     * logo
     *
     * @return string url https du logo
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * logo
     *
     * @param String $newlogo Url https du logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * back URL
     *
     * @return string URL
     */
    public function getBackUrl()
    {
        return $this->backUrl;
    }

    /**
     * back URL
     *
     * @param string $newbackUrl [description]
     */
    public function setBackUrl($backUrl)
    {
        $this->backUrl = $backUrl;

        return $this;
    }

    /**
     * decline url
     *
     * @return string decline url
     */
    public function getDeclineUrl()
    {
        return $this->declineUrl;
    }

    /**
     * decline url
     *
     * @param String $newdeclineUrl Decline url
     */
    public function setDeclineUrl($declineUrl)
    {
        $this->declineUrl = $declineUrl;

        return $this;
    }

    /**
     * cancel url
     *
     * @return string cancel url
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * cancel url
     *
     * @param String $newcancelUrl Cancel url
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    /**
     * acceptUrl
     *
     * @return string url
     */
    public function getAcceptUrl()
    {
        return $this->acceptUrl;
    }

    /**
     * accept URL
     *
     * @param String $newacceptUrl Url
     */
    public function setAcceptUrl($acceptUrl)
    {
        $this->acceptUrl = $acceptUrl;

        return $this;
    }

    /**
     * complus
     *
     * @return string libre
     */
    public function getComplus()
    {
        return $this->complus;
    }

    /**
     * complus
     *
     * @param String $newcomplus Libre
     */
    public function setComplus($complus)
    {
        $this->complus = $complus;

        return $this;
    }

    /**
     * language
     *
     * @return string langue de l'écran ogone
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * language
     *
     * @param String $newlanguage Langue de l'écran ogone
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * ville
     *
     * @return string ville
     */
    public function getOwnerTown()
    {
        return $this->ownerTown;
    }

    /**
     * ville
     *
     * @param String $newownerTown Ville
     */
    public function setOwnerTown($ownerTown)
    {
        $this->ownerTown = $ownerTown;

        return $this;
    }

    /**
     * country
     *
     * @return string country
     */
    public function getOwnerCty()
    {
        return $this->ownerCty;
    }

    /**
     * country
     *
     * @param String $newownerCty Country
     */
    public function setOwnerCty($ownerCty)
    {
        $this->ownerCty = $ownerCty;

        return $this;
    }

    /**
     * orderId
     *
     * @return string orderid
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * orderid
     *
     * @param String $neworderId Orderid
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * sha1in
     *
     * @return string clef sha1
     */
    public function getSha1in()
    {
        return $this->sha1in;
    }

    /**
     * sha1in
     *
     * @param String $newsha1in Clef sha1
     */
    public function setSha1in($sha1in)
    {
        $this->sha1in = $sha1in;

        return $this;
    }

    /**
     * pspid
     *
     * @return string pspid du marchant
     */
    public function getPspid()
    {
        return $this->pspid;
    }

    /**
     * pspis
     *
     * @param String $newpspid Pspid du marchant
     */
    public function setPspid($pspid)
    {
        $this->pspid = $pspid;

        return $this;
    }

    /**
     * pm
     *
     * @return string payment method
     */
    public function getPm()
    {
        return $this->pm;
    }

    /**
     * [pm
     *
     * @param String $newpm Paymen
     */
    public function setPm($pm)
    {
        $this->pm = $pm;

        return $this;
    }

    /**
     * currency
     *
     * @return string currenct
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * currency
     *
     * @param String $newcurrency Currenct
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * card name
     *
     * @return string CN
     */
    public function getCn()
    {
        return $this->cn;
    }

    /**
     * [Description]
     *
     * @param String $newcn CN
     */
    public function setCn($cn)
    {
        $this->cn = $cn;

        return $this;
    }

    /**
     * email
     *
     * @return string email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * email
     *
     * @param String $newemail Email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * ownerZip
     *
     * @return string zipcode
     */
    public function getOwnerZip()
    {
        return $this->ownerZip;
    }

    /**
     * [Description]
     *
     * @param String $newownerZip Zipcode
     */
    public function setOwnerZip($ownerZip)
    {
        $this->ownerZip = $ownerZip;

        return $this;
    }
}
