<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright 2015 Ecedi
 * @package eDonate
 *
 */

namespace Ecedi\Donate\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Intent
 *
 * @ORM\Table("intent",
 *   indexes={
 *     @ORM\Index(name="search_idx", columns={
 *        "type",
 *        "status",
 *        "currency",
 *        "payment_method"
 *   })
 * })
 * @ORM\Entity(repositoryClass="Ecedi\Donate\CoreBundle\Repository\IntentRepository")
 */
class Intent
{
    const FISCAL_RECEIP_EMAIL = 0;
    const FISCAL_RECEIP_POST = 1;

    const TYPE_SPOT = 0;
    const TYPE_RECURING = 1;
    const TYPE_SPONSORSHIP = 2;

    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';
    const STATUS_CANCEL = 'cancel';
    const STATUS_ERROR = 'error';

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="intent", cascade={"persist", "remove"})
     * @Serializer\Groups({"REST"})
     */
    private $payments;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"REST"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="intents")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     *
     * @Serializer\Groups({"REST"})
     */
    private $customer;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     * @Assert\NotNull()
     * @Assert\Choice(callback = "getTypes")
     * @Serializer\Groups({"REST"})
     *
     */
    private $type;

    /**
     * @var string
     * @since  2.0.0
     *
     * @ORM\Column(name="affectation_code", type="string", length=12, nullable=true)
     * @Serializer\Groups({"REST"})
     *
     * Not an integrity-relation because affectations can be change after intents doned.
     */
    private $affectationCode;

    /**
     * accessor to affectationCode
     * @since  2.0.0
     *
     * @return string affectation code
     */
    public function getAffectationCode()
    {
        return $this->affectationCode;
    }

    /**
     * accessor to affectationCode
     * @since  2.0.0
     *
     * @param string $newaffectationCode affectation code
     */
    public function setAffectationCode($affectationCode)
    {
        $this->affectationCode = $affectationCode;

        return $this;
    }

    public static function getTypes()
    {
        return array(self::TYPE_SPOT, self::TYPE_RECURING, self::TYPE_SPONSORSHIP);
    }

    public static function getTypesLabel()
    {
        return array(
            self::TYPE_SPOT       => 'Spot',
            self::TYPE_RECURING   => 'Recuring',
            self::TYPE_SPONSORSHIP => 'Sponsorship',
        );
    }

    /**
     * @var integer
     * Montant du don entre 5 et 4000 €
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 500,
     *      max = 400000
     * )
     * @Serializer\Groups({"REST"})
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "3",
     *      max = "3")
     * @Serializer\Groups({"REST"})
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=36, nullable=false)
     * @Assert\Choice(callback = "getPossibleStatus")
     *
     * @Serializer\Groups({"REST"})
     */
    private $status;

    public static function getPossibleStatus()
    {
        return array(
            self::STATUS_NEW,
            self::STATUS_PENDING,
            self::STATUS_DONE,
            self::STATUS_CANCEL,
            self::STATUS_ERROR,
        );
    }

    public static function getStatusLabel()
    {
        return array(
            Intent::STATUS_NEW      => 'new',
            Intent::STATUS_PENDING  => 'pending',
            Intent::STATUS_DONE     => 'done',
            Intent::STATUS_CANCEL   => 'cancel',
            Intent::STATUS_ERROR    => 'error',
        );
    }

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=36, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Groups({"REST"})
     */
    private $paymentMethod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="changed_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $changedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign", type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="[a-zA-Z0-9\-_]{,8}")
     * @Serializer\Groups({"REST"})
     */
    private $campaign;

    /**
     * @var integer
     * @ORM\Column(name="fiscal_receipt", type="integer", nullable=false)
     * @Serializer\Groups({"REST"})
     */
    private $fiscal_receipt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param  integer $type
     * @return Intent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set amount
     *
     * @param  integer $amount
     * @return Intent
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param  string $currency
     * @return Intent
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set status
     *
     * @param  string $status
     * @return Intent
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set paymentMethod
     *
     * @param  string $paymentMethod
     * @return Intent
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Intent
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set changedAt
     *
     * @param  \DateTime $changedAt
     * @return Intent
     */
    public function setChangedAt($changedAt)
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    /**
     * Get changedAt
     *
     * @return \DateTime
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * Set campaign
     *
     * @param  string $campaign
     * @return Intent
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return string
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * spot pending euro par défaut
     */
    public function __construct($amount, $paymentMethod, $currency = 'EUR', $campaign = null)
    {
        $this->setAmount($amount);
        $this->setCurrency($currency);
        $this->setPaymentMethod($paymentMethod);
        $this->setType(self::TYPE_SPOT);
        $this->setStatus('new');
        $this->setCampaign($campaign);
        $this->setFiscalReceipt(self::FISCAL_RECEIP_EMAIL);

        $this->payments = new ArrayCollection();
    }

    /**
     * Set fiscal_receipt
     *
     * @param  integer $fiscalReceipt
     * @return Intent
     */
    public function setFiscalReceipt($fiscalReceipt)
    {
        $this->fiscal_receipt = $fiscalReceipt;

        return $this;
    }

    /**
     * Get fiscal_receipt
     *
     * @return integer
     */
    public function getFiscalReceipt()
    {
        return $this->fiscal_receipt;
    }

    /**
     * Add payments
     *
     * @param  \Ecedi\Donate\CoreBundle\Entity\Payment $payments
     * @return Intent
     */
    public function addPayment(Payment $payments)
    {
        $this->payments[] = $payments;
        $payments->setIntent($this);

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \Ecedi\Donate\CoreBundle\Entity\Payment $payments
     */
    public function removePayment(Payment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Formate la date de création en timestamp pour l'api REST
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("created_at")
     * @Serializer\Groups("REST")
     *
     * @return Timestamp
     */
    public function getTimestampCreatedAt()
    {
        return $this->getCreatedAt()->getTimestamp();
    }

    /**
     * Formate la date de maj en timestamp pour l'api REST
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("changed_at")
     * @Serializer\Groups("REST")
     *
     * @return Timestamp
     */
    public function getTimestampChangedAt()
    {
        return $this->getChangedAt()->getTimestamp();
    }
}
