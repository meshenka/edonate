<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2014
 */
namespace Ecedi\Donate\OgoneBundle\Ogone;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * @author syg
 */
class Response implements \JsonSerializable
{
    private $orderId;
    private $amount;
    private $currency;
    private $pm;
    private $acceptance;
    private $status;
    private $cardNo;
    private $payId;
    private $ncError;
    private $brand;
    private $ed;
    private $trxDate;
    private $cn;
    private $shasign;
    private $eci;
    private $alias;
    private $ip;
    private $complus;

    /**
     * Sérialisation en tableau pour faciliter le json_encode.
     *
     * @see http://php.net/manual/en/class.jsonserializable.php
     *
     * @return array an array version of the instance
     */
    public function jsonSerialize()
    {
        return [
            'ORDERID' => $this->getOrderId(),
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => $this->getCurrency(),
            'PM' => $this->getPm(),
            'ACCEPTANCE' => $this->getAcceptance(),
            'STATUS' => $this->getStatus(),
            'CARDNO' => $this->getCardNo(),
            'PAYID' => $this->getPayId(),
            'NCERROR' => $this->getNcError(),
            'BRAND' => $this->getBrand(),
            'ED' => $this->getEd(),
            'TRXDATE' => $this->getTrxDate(),
            'CN' => $this->getCn(),
            'SHASIGN' => $this->getShasign(),
            'ECI' => $this->getEci(),
            'ALIAS' => $this->getAlias(),
            'IP' => $this->getIp(),
            'COMPLUS' => $this->getComplus(),
        ];
    }

    /**
     * ALIAS.
     *
     * @return string alias
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * alias.
     *
     * @param string $newalias Alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * IP address.
     *
     * @return string ip address
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * IP.
     *
     * @param string $newip Ip address
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * complus.
     *
     * @return string complus
     */
    public function getComplus()
    {
        return $this->complus;
    }

    /**
     * [Description].
     *
     * @param string $newcomplus Complus
     */
    public function setComplus($complus)
    {
        $this->complus = $complus;

        return $this;
    }

    /**
     * ECI.
     *
     * @return string ECI
     */
    public function getEci()
    {
        return $this->eci;
    }

    /**
     * ECI.
     *
     * @param string $neweci ECI
     */
    public function setEci($eci)
    {
        $this->eci = $eci;

        return $this;
    }
    /**
     * orderId.
     *
     * @return string orderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * orderId.
     *
     * @param string $neworderId OrderId
     */
    protected function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * amount.
     *
     * @return int amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * amounnt.
     *
     * @param int $newamount [description]
     */
    protected function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Currency.
     *
     * @return string currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * currency.
     *
     * @param string $newcurrency Currency
     */
    protected function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * PaymentMethod.
     *
     * @return string payment method
     */
    public function getPm()
    {
        return $this->pm;
    }

    /**
     * pm.
     *
     * @param string $newpm Payment method
     */
    protected function setPm($pm)
    {
        $this->pm = $pm;

        return $this;
    }

    /**
     * acceptance.
     *
     * @return string numéro d'autorisation
     */
    public function getAcceptance()
    {
        return $this->acceptance;
    }

    /**
     * acceptance.
     *
     * @param string $newacceptance Numéro d'autorisation
     */
    protected function setAcceptance($acceptance)
    {
        $this->acceptance = $acceptance;

        return $this;
    }

    /**
     * status.
     *
     * @return string status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * status.
     *
     * @param string $newstatus Status
     */
    protected function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * CardNo.
     *
     * @return string Card Number
     */
    public function getCardNo()
    {
        return $this->cardNo;
    }

    /**
     * CardNo.
     *
     * @param string $newcardNo Card Number
     */
    protected function setCardNo($cardNo)
    {
        $this->cardNo = $cardNo;

        return $this;
    }

    /**
     * PayId.
     *
     * @return string payid
     */
    public function getPayId()
    {
        return $this->payId;
    }

    /**
     * payid.
     *
     * @param string $newpayId Payid
     */
    protected function setPayId($payId)
    {
        $this->payId = $payId;

        return $this;
    }

    /**
     * NCERROR.
     *
     * @return string NCERROR
     */
    public function getNcError()
    {
        return $this->ncError;
    }

    /**
     * NCERROR.
     *
     * @param string $newncError NCERROR
     */
    protected function setNcError($ncError)
    {
        $this->ncError = $ncError;

        return $this;
    }

    /**
     * card brand.
     *
     * @return string brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * card brand.
     *
     * @param string $newbrand Brand
     */
    protected function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * expiration date.
     *
     * @return string ed
     */
    public function getEd()
    {
        return $this->ed;
    }

    /**
     * expiration date.
     *
     * @param string $newed Ed
     */
    protected function setEd($ed)
    {
        $this->ed = $ed;

        return $this;
    }

    /**
     * Transaction Date.
     *
     * @return string trxdate
     */
    public function getTrxDate()
    {
        return $this->trxDate;
    }

    /**
     * transaction date.
     *
     * @param string $newtrxDate Trxdate
     */
    protected function setTrxDate($trxDate)
    {
        $this->trxDate = $trxDate;

        return $this;
    }

    /**
     * Cardholder name.
     *
     * @return string cn
     */
    public function getCn()
    {
        return $this->cn;
    }

    /**
     * cardholder name.
     *
     * @param string $newcn Cn
     */
    protected function setCn($cn)
    {
        $this->cn = $cn;

        return $this;
    }

    /**
     * SHA sign.
     *
     * @return string shasign
     */
    public function getShasign()
    {
        return $this->shasign;
    }

    /**
     * SHA sign.
     *
     * @param string $newshasign Shasign
     */
    protected function setShasign($shasign)
    {
        $this->shasign = $shasign;

        return $this;
    }

    // ORDERID Your order reference
    // AMOUNT Order amount (not multiplied by 100)
    // CURRENCY Order currency
    // PM Payment method
    // ACCEPTANCE Acceptance code returned by acquirer (Payment::autorisation)
    // STATUS Transaction status (see Appendix: Status overview)
    // CARDNO Masked card number
    // PAYID Payment reference in our system (Payment::transaction)
    // NC ERROR Error code
    // BRAND Card brand (our system derives this from the card number)
    // ED Expiry date
    // TRXDATE Transaction date
    // CN Cardholder/customer name
    // SHASIGN SHA signature calculated by our system (if SHA-1-OUT configured)
    //ECI
    //ALIAS
    //IP
    //COMPLUS

    /**
     * Création d'une Réponse à partir d'une Http Request.
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     */
    public static function createFromRequest(HttpRequest $request)
    {
        $response = new self();
        $response
            ->setOrderId($request->get('orderID'))
            ->setAmount($request->get('amount'))
            ->setCurrency($request->get('currency'))
            ->setPm($request->get('PM'))
            ->setAcceptance($request->get('ACCEPTANCE'))
            ->setStatus($request->get('STATUS'))
            ->setCardNo($request->get('CARDNO'))
            ->setPayId($request->get('PAYID'))
            ->setNcError($request->get('NCERROR'))
            ->setBrand($request->get('BRAND'))
            ->setEd($request->get('ED'))
            ->setTrxDate($request->get('TRXDATE'))
            ->setCn($request->get('CN'))
            ->setShasign($request->get('SHASIGN'))
            ->setEci($request->get('ECI'))
            ->setComplus($request->get('COMPLUS'))
            ->setIp($request->get('IP'))
            ->setAlias($request->get('ALIAS'))
        ;

        return $response;
    }

    /**
     * Création d'une Réponse à partir d'un tableau.
     *
     * @param array $data
     *
     * @return Response
     */
    public static function createFromArray(array $data)
    {
        $response = new self();

        $response
            ->setOrderId($data['ORDERID'])
            ->setAmount($data['AMOUNT'])
            ->setCurrency($data['CURRENCY'])
            ->setPm($data['PM'])
            ->setAcceptance($data['ACCEPTANCE'])
            ->setStatus($data['STATUS'])
            ->setCardNo($data['CARDNO'])
            ->setPayId($data['PAYID'])
            ->setNcError($data['NCERROR'])
            ->setBrand($data['BRAND'])
            ->setEd($data['ED'])
            ->setTrxDate($data['TRXDATE'])
            ->setCn($data['CN'])
            ->setShasign($data['SHASIGN'])
            ->setEci($data['ECI'])
            ->setComplus($data['COMPLUS'])
            ->setIp($data['IP'])
            ->setAlias($data['ALIAS'])
        ;

        return $response;
    }
}
