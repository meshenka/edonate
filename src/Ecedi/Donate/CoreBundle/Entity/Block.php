<?php

namespace Ecedi\Donate\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Block
 *
 * @ORM\Table(
 *  "block",
 *   indexes={
 *     @ORM\Index(name="search_idx", columns={
 *        "name",
 *        "type",
 *        "enabled"
 *   })
 * })
 * @ORM\Entity(repositoryClass="Ecedi\Donate\CoreBundle\Repository\BlockRepository")
 */
class Block
{

    const FORMAT_HTML = 'html';
    const FORMAT_RAW = 'raw';
    const FORMAT_MARKDOWN = 'md';

    /**
     * @ORM\ManyToOne(targetEntity="Layout", inversedBy="blocks")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     *
     */
    private $layout;

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string
     * @ORM\Column(name="title_url", type="string", length=255, nullable=true)
     */
    private $titleUrl;

    /**
     * @ORM\Column(name="format", type="string", length=6, nullable=false)
     * @Assert\Choice(callback="getFormats")
     * @var string
     */
    private $format;

    public static function getFormats()
    {
        return array(
            self::FORMAT_HTML,
            self::FORMAT_MARKDOWN,
            self::FORMAT_RAW);
    }

    /**
     * @param  string $titleUrl
     * @return Block  $this
     */
    public function setTitleUrl($titleUrl)
    {
        $this->titleUrl = $titleUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleUrl()
    {
        return $this->titleUrl;
    }

    /**
     * @param  string $titleUrlTitle
     * @return Block  $this
     */
    public function setTitleUrlTitle($titleUrlTitle)
    {
        $this->titleUrlTitle = $titleUrlTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleUrlTitle()
    {
        return $this->titleUrlTitle;
    }

    /**
     * @var string
     * @ORM\Column(name="title_url_title", type="string", length=255, nullable=true)
     */
    private $titleUrlTitle;

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
     * Set name
     *
     * @param  string $name
     * @return Block
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set position
     *
     * @param  integer $position
     * @return Block
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set type
     *
     * @param  string $type
     * @return Block
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set enabled
     *
     * @param  boolean $enabled
     * @return Block
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Block
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
     * @return Block
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
     * Set title
     *
     * @param  string $title
     * @return Block
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param  string $body
     * @return Block
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    public function __construct($name, $position, $type='simple')
    {
        $this->setName($name);
        $this->setPosition($position);
        $this->setType($type);
        $this->setEnabled(true);
        $this->setFormat(self::FORMAT_HTML);
    }

    /**
     * Set format
     *
     * @param  string $format
     * @return Block
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set layout
     *
     * @param  \Ecedi\Donate\CoreBundle\Entity\Layout $layout
     * @return Block
     */
    public function setLayout(\Ecedi\Donate\CoreBundle\Entity\Layout $layout = null)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get layout
     *
     * @return \Ecedi\Donate\CoreBundle\Entity\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }
}