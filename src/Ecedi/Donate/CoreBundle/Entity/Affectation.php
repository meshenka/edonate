<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright 2015 Ecedi
 * @package eCollecte
 *
 */

namespace Ecedi\Donate\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Define possible affectations for a donation
 *
 * @since  2.0.0
 *
 * @ORM\Table( "affectation")
 * @ORM\Entity()
 */
class Affectation
{
    /**
     * @ORM\ManyToOne(targetEntity="Layout", inversedBy="affectations")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     *
     */
    private $layout;

    /**
     * layout
     *
     * @return Layout the layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * layout
     *
     * @param Layout $newlayout The layout
     */
    public function setLayout($layout)
    {
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=12, nullable=false)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer", nullable=false)
     */
    private $weight;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * code
     *
     * @return string an affectation code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * code
     *
     * @param String $newcode An affectation code
     */
    public function setCode($code)
    {
        $this->code = $code;

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
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function __construct()
    {
        $this->weight = 0;
    }

    /**
     * weight
     *
     * @return integer weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * weight
     *
     * @param Integer $newweight Weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }
}
