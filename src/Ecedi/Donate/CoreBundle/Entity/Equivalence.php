<?php

namespace Ecedi\Donate\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table( "equivalence")
 * @ORM\Entity()
 */
class Equivalence
{

    /**
     * @ORM\ManyToOne(targetEntity="Layout", inversedBy="equivalences")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     *
     */
    private $layout;


    /**
     * layout
     *
     * @return Layout the layout
     */
    public function getLayout() {
        return $this->layout;
    }
    
    /**
     * layout
     *
     * @param Layout $newlayout The layout
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    
        return $this;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     */
    private $currency = 'EUR';

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;


    /**
     * id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * amount
     *
     * @return integer amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * amount
     *
     * @param Integer $newamount Amount
     */
    private function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Currency
     *
     * @return string currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * currency
     *
     * @param String $newCurrency Currency
     */
    private function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * label (en label)
     *
     * @return string label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * label
     *
     * @param String $newlabel Label
     */
    private function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }


    public function __construct($amount, $label, $currency = 'EUR')
    {
        $this->setAmount($amount);
        $this->setLabel($label);
        $this->setCurrency($currency);
    }
}