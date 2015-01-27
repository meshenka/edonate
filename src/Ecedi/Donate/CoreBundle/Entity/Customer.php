<?php

namespace Ecedi\Donate\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table(
 *  "customer",
 *   indexes={
 *     @ORM\Index(name="search_idx", columns={
 *        "remote_id",
 *        "lastname",
 *        "email",
 *        "address_city",
 *        "address_country",
 *        "address_zipcode",
 *        "optin",
 *   })
 * })
 * @ORM\Entity(repositoryClass="Ecedi\Donate\CoreBundle\Repository\CustomerRepository")
 *
 */
class Customer
{
    public function __construct()
    {
        $this->intents = new ArrayCollection();
    }

    /**
     * @ORM\OneToMany(targetEntity="Intent", mappedBy="customer", cascade={"persist", "remove"})
     */
    private $intents;

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
     * @var integer
     *
     * @ORM\Column(name="remote_id", type="integer", nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $remoteId;

    /**
     * @var string
     *
     * @ORM\Column(name="civility", type="string", length=6, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $civility;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Groups({"REST"})
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Groups({"REST"})
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="middlename", type="string", length=255, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @Assert\Email(
     *     checkMX = false
     * )
     * @Assert\NotBlank()
     * @Serializer\Groups({"REST"})
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     * @Assert\Date()
     * @Serializer\Groups("REST")
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @Assert\Regex("/^[0-9\.\-\s\+]*$/")
     * @Serializer\Groups({"REST"})
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     * @Assert\Url()
     * @Serializer\Groups({"REST"})
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="address_nber", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $addressNber;

    /**
     * @var string
     *
     * @ORM\Column(name="address_street", type="string", length=255, nullable=false)
     * @Assert\NotBlank
     * @Serializer\Groups({"REST"})
     */
    private $addressStreet;

    /**
     * @var string
     *
     * @ORM\Column(name="address_extra", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $addressExtra;

    /**
     * @var string
     *
     * @ORM\Column(name="address_pb", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $addressPb;

    /**
     * @var string
     *
     * @ORM\Column(name="address_living", type="string", length=255, nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $addressLiving;

    /**
     * @var string
     *
     * @ORM\Column(name="address_zipcode", type="string", length=6, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min = 0,max = 99999)
     * @Serializer\Groups({"REST"})
     */
    private $addressZipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="address_city", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Groups({"REST"})
     */
    private $addressCity;

    /**
     * @var string
     *
     * @ORM\Column(name="address_country", type="string", length=3, nullable=false)
     * @Assert\Country
     * @Serializer\Groups({"REST"})
     */
    private $addressCountry;

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
     * @var boolean
     *
     * @ORM\Column(name="optin", type="boolean", nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $optin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="optinSynchronized", type="boolean", nullable=true)
     * @Serializer\Groups({"REST"})
     */
    private $optinSynchronized;

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
     * Set remoteId
     *
     * @param  integer  $remoteId
     * @return Customer
     */
    public function setRemoteId($remoteId)
    {
        $this->remoteId = $remoteId;

        return $this;
    }

    /**
     * Get remoteId
     *
     * @return integer
     */
    public function getRemoteId()
    {
        return $this->remoteId;
    }

    /**
     * Set civility
     *
     * @param  string   $civility
     * @return Customer
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility
     *
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * Set firstName
     *
     * @param  string   $firstName
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param  string   $lastName
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set middleName
     *
     * @param  string   $middleName
     * @return Customer
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set email
     *
     * @param  string   $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set birthday
     *
     * @param  \DateTime $birthday
     * @return Customer
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set phone
     *
     * @param  string   $phone
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set company
     *
     * @param  string   $company
     * @return Customer
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set website
     *
     * @param  string   $website
     * @return Customer
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set addressNber
     *
     * @param  string   $addressNber
     * @return Customer
     */
    public function setAddressNber($addressNber)
    {
        $this->addressNber = $addressNber;

        return $this;
    }

    /**
     * Get addressNber
     *
     * @return string
     */
    public function getAddressNber()
    {
        return $this->addressNber;
    }

    /**
     * Set addressStreet
     *
     * @param  string   $addressStreet
     * @return Customer
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set addressExtra
     *
     * @param  string   $addressExtra
     * @return Customer
     */
    public function setAddressExtra($addressExtra)
    {
        $this->addressExtra = $addressExtra;

        return $this;
    }

    /**
     * Get addressExtra
     *
     * @return string
     */
    public function getAddressExtra()
    {
        return $this->addressExtra;
    }

    /**
     * Set addressPb
     *
     * @param  string   $addressPb
     * @return Customer
     */
    public function setAddressPb($addressPb)
    {
        $this->addressPb = $addressPb;

        return $this;
    }

    /**
     * Get addressPp
     *
     * @return string
     */
    public function getAddressPb()
    {
        return $this->addressPb;
    }

    /**
     * Set addressZipcode
     *
     * @param  string   $addressZipcode
     * @return Customer
     */
    public function setAddressZipcode($addressZipcode)
    {
        $this->addressZipcode = $addressZipcode;

        return $this;
    }

    /**
     * Get addressZipcode
     *
     * @return string
     */
    public function getAddressZipcode()
    {
        return $this->addressZipcode;
    }

    /**
     * Set addressCity
     *
     * @param  string   $addressCity
     * @return Customer
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set addressLiving
     *
     * @param  string   $addressLiving
     * @return Customer
     */
    public function setAddressLiving($addressLiving)
    {
        $this->addressLiving = $addressLiving;

        return $this;
    }

    /**
     * Get addressLiving
     *
     * @return string
     */
    public function getAddressLiving()
    {
        return $this->addressLiving;
    }

    /**
     * Set addressCountry
     *
     * @param  string   $addressCountry
     * @return Customer
     */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;

        return $this;
    }

    /**
     * Get addressCountry
     *
     * @return string
     */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }

    /**
     * Set optin
     *
     * @param  string   $optin
     * @return Customer
     */
    public function setOptin($optin)
    {
        $this->optin = $optin;

        return $this;
    }

    /**
     * Get optin
     *
     * @return string
     */
    public function getOptin()
    {
        return $this->optin;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Customer
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
     * @return Customer
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
     * intents
     *
     * @return Intent intent
     */
    public function getIntents()
    {
        return $this->intents;
    }

    public function addIntent(Intent $intent)
    {
        $this->intents[] = $intent;
        $intent->setCustomer($this);

        return $this;
    }

    /**
     * Remove intents
     *
     * @param \Ecedi\Donate\CoreBundle\Entity\Intent $intents
     */
    public function removeIntent(\Ecedi\Donate\CoreBundle\Entity\Intent $intents)
    {
        $this->intents->removeElement($intents);
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

    /**
     * Set optinSynchronized
     *
     * @param  boolean  $optinSynchronized
     * @return Customer
     */
    public function setOptinSynchronized($optinSynchronized)
    {
        $this->optinSynchronized = $optinSynchronized;

        return $this;
    }

    /**
     * Get optinSynchronized
     *
     * @return boolean
     */
    public function getOptinSynchronized()
    {
        return $this->optinSynchronized;
    }
}
