<?php

namespace Ecedi\Donate\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Payment.
 *
 * @ORM\Table("payment",
 *   indexes={
 *     @ORM\Index(name="search_idx", columns={
 *        "status",
 *        "response_code",
 *        "alias",
 *   })
 * })
 * @ORM\Entity(repositoryClass="Ecedi\Donate\CoreBundle\Repository\PaymentRepository")
 */
class Payment
{
    public function __construct()
    {
        $this->setStatus(self::STATUS_NEW);
    }

//enum('invalid','canceled', 'authorized', 'denied', 'completed', 'failed')
    const STATUS_INVALID = 'invalid';
    const STATUS_CANCELED = 'canceled';
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_DENIED = 'denied';
    const STATUS_PAYED = 'payed';
    const STATUS_FAILED = 'failed';
    const STATUS_NEW = 'new';
    const STATUS_REFUND = 'refund';
    const STATUS_UNKNOW = 'unknow';

    /**
     * @ORM\ManyToOne(targetEntity="Intent", inversedBy="payments")
     * @ORM\JoinColumn(name="intent_id", referencedColumnName="id")
     */
    private $intent;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"REST"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     * @Assert\Choice(callback = "getAllowedStatus")
     * @Serializer\Groups({"REST"})
     */
    private $status;

    public static function getAllowedStatus()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_INVALID,
            self::STATUS_CANCELED,
            self::STATUS_AUTHORIZED,
            self::STATUS_DENIED,
            self::STATUS_PAYED,
            self::STATUS_FAILED,
            self::STATUS_REFUND,
            self::STATUS_UNKNOW,
        ];
    }

    /**
     * @var string
     *
     * @ORM\Column(name="response_code", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $responseCode;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $transaction;

    /**
     * @var string
     *
     * @ORM\Column(name="autorisation", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $autorisation;

    /**
     * @var array
     *
     * @ORM\Column(name="response", type="json_array", nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $response;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=128, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $alias;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Payment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set responseCode.
     *
     * @param string $responseCode
     *
     * @return Payment
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * Get responseCode.
     *
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Set transaction.
     *
     * @param string $transaction
     *
     * @return Payment
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction.
     *
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set autorisation.
     *
     * @param string $autorisation
     *
     * @return Payment
     */
    public function setAutorisation($autorisation)
    {
        $this->autorisation = $autorisation;

        return $this;
    }

    /**
     * Get autorisation.
     *
     * @return string
     */
    public function getAutorisation()
    {
        return $this->autorisation;
    }

    /**
     * Set response.
     *
     * @param array $response
     *
     * @return Payment
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return Payment
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Payment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set changedAt.
     *
     * @param \DateTime $changedAt
     *
     * @return Payment
     */
    public function setChangedAt($changedAt)
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    /**
     * Get changedAt.
     *
     * @return \DateTime
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * Set intent.
     *
     * @param \Ecedi\Donate\CoreBundle\Entity\Intent $intent
     *
     * @return Payment
     */
    public function setIntent(Intent $intent = null)
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * Get intent.
     *
     * @return \Ecedi\Donate\CoreBundle\Entity\Intent
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * Formate la date de création en timestamp pour l'api REST.
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
     * Formate la date de maj en timestamp pour l'api REST.
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
