<?php

namespace Ecedi\Donate\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Layout
 *
 * @ORM\Table("layout")
 * @ORM\Entity(repositoryClass="Ecedi\Donate\CoreBundle\Repository\LayoutRepository")
 * @Vich\Uploadable
 *
 */
class Layout
{
    const SKIN_DEFAULT='sky';
    const SKIN_LIGHT='light';
    const SKIN_DARK='dark';
    const SKIN_CUSTOM='custom';

    /**
     * @ORM\OneToMany(targetEntity="Block", mappedBy="layout", cascade={"persist", "remove"})
     */
    private $blocks;

    /**
     * @ORM\OneToMany(targetEntity="Equivalence", mappedBy="layout", cascade={"persist", "remove"})
     */
    private $equivalences;

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
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $isDefault;


    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=5)
     */
    private $language;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="skin", type="string", length=128)
     */
    private $skin;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logoName;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_url", type="string", length=255, nullable=true)
     */
    private $logoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_alt", type="string", length=255, nullable=true)
     */
    private $logoAlt;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_title", type="string", length=255, nullable=true)
     */
    private $logoTitle;


    /**
     * @Assert\File(
     *     maxSize="100k",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="layout_logo", fileNameProperty="logoName")
     *
     * @var UploadedFile $logo
     */
    protected $logo;

    /**
     * @Assert\File(
     *     maxSize="512k",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="layout_background", fileNameProperty="backgroundName")
     *
     * @var UploadedFile $background
     */
    protected $background;
  
    /**
     * @var string
     *
     * @ORM\Column(name="baseline", type="string", length=255, nullable=true)
     */
    private $baseline;

    /**
     * @var string
     *
     * @ORM\Column(name="background", type="string", length=255, nullable=true)
     */
    private $backgroundName;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="string", length=255, nullable=true)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_title", type="string", length=255, nullable=true)
     */
    private $metaTitle;

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
     * @return Layout
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
     * Set skin
     *
     * @param  string $skin
     * @return Layout
     */
    public function setSkin($skin)
    {
        $this->skin = $skin;

        return $this;
    }

    /**
     * Get skin
     *
     * @return string
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * Set logoName
     *
     * @param  string $logoName
     * @return Layout
     */
    public function setLogoName($logoName)
    {
        $this->logoName = $logoName;

        return $this;
    }

    /**
     * Get logoName
     *
     * @return string
     */
    public function getLogoName()
    {
        return $this->logoName;
    }

    /**
     * Set baseline
     *
     * @param  string $baseline
     * @return Layout
     */
    public function setBaseline($baseline)
    {
        $this->baseline = $baseline;

        return $this;
    }

    /**
     * Get baseline
     *
     * @return string
     */
    public function getBaseline()
    {
        return $this->baseline;
    }

    /**
     * Set backgroundName
     *
     * @param  string $backgroundName
     * @return Layout
     */
    public function setBackgroundName($backgroundName)
    {
        $this->backgroundName = $backgroundName;

        return $this;
    }

    /**
     * Get backgroundName
     *
     * @return string
     */
    public function getBackgroundName()
    {
        return $this->backgroundName;
    }

    /**
     * Set metaDescription
     *
     * @param  string $metaDescription
     * @return Layout
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set metaKeywords
     *
     * @param  string $metaKeywords
     * @return Layout
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }
    /**
     * Constructor
     */
    public function __construct($language = 'fr', $name='default', $skin = self::SKIN_DEFAULT)
    {
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->equivalences = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setName($name);
        $this->setSkin($skin);
        $this->setIsDefault(false);
        $this->setLanguage($language);

        foreach ($this->generateBlock($name) as $b) {             
            $this->addBlock($b);
        }
        
    }

     // utilisation d'un generator php5.5
    private function generateBlock($name = 'default')
    {
        for ($i = 1; $i <= 4; $i++) {
            $b = new Block("{$name}-" . $i, $i*10);
            yield $b;
        }
    }


    /**
     * Add blocks
     *
     * @param  \Ecedi\Donate\CoreBundle\Entity\Block $blocks
     * @return Layout
     */
    public function addBlock(Block $blocks)
    {
        $this->blocks[] = $blocks;
        $blocks->setLayout($this);
        return $this;
    }

    /**
     * Remove blocks
     *
     * @param \Ecedi\Donate\CoreBundle\Entity\Block $blocks
     */
    public function removeBlock(\Ecedi\Donate\CoreBundle\Entity\Block $blocks)
    {
        $this->blocks->removeElement($blocks);
    }

    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return Layout
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    
        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string 
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set default
     *
     * @param boolean $defaul
     * @return Layout
     */
    public function setDefault($default)
    {
        $this->isDefault = $default;
    
        return $this;
    }

    /**
     * Get default
     *
     * @return boolean 
     */
    public function getDefault()
    {
        return $this->isDefault;
    }

    /**
     * Get default
     *
     * @return boolean 
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     * @return Layout
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    
        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean 
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }



    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setBackground(File $file = null)
    {
        $this->background = $file;
        $this->setChangedAt(new \DateTime());
        return $this;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getBackground()
    {
        return $this->background;
    }



    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setLogo(File $file = null)
    {
        $this->logo = $file;
        $this->setChangedAt(new \DateTime());
        return $this;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Layout
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
     * @param \DateTime $changedAt
     * @return Layout
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
     * Set language
     *
     * @param string $language
     * @return Layout
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set logoUrl
     *
     * @param string $logoUrl
     * @return Layout
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;
    
        return $this;
    }

    /**
     * Get logoUrl
     *
     * @return string 
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * Set logoAlt
     *
     * @param string $logoAlt
     * @return Layout
     */
    public function setLogoAlt($logoAlt)
    {
        $this->logoAlt = $logoAlt;
    
        return $this;
    }

    /**
     * Get logoAlt
     *
     * @return string 
     */
    public function getLogoAlt()
    {
        return $this->logoAlt;
    }

    /**
     * Set logoTitle
     *
     * @param string $logoTitle
     * @return Layout
     */
    public function setLogoTitle($logoTitle)
    {
        $this->logoTitle = $logoTitle;
    
        return $this;
    }

    /**
     * Get logoTitle
     *
     * @return string 
     */
    public function getLogoTitle()
    {
        return $this->logoTitle;
    }

    /**
     * Add equivalences
     *
     * @param \Ecedi\Donate\CoreBundle\Entity\Equivalence $equivalences
     * @return Layout
     */
    public function addEquivalence(\Ecedi\Donate\CoreBundle\Entity\Equivalence $equivalences)
    {
        $this->equivalences[] = $equivalences;
    
        return $this;
    }

    /**
     * Remove equivalences
     *
     * @param \Ecedi\Donate\CoreBundle\Entity\Equivalence $equivalences
     */
    public function removeEquivalence(\Ecedi\Donate\CoreBundle\Entity\Equivalence $equivalences)
    {
        $this->equivalences->removeElement($equivalences);
    }

    /**
     * Get equivalences
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEquivalences()
    {
        return $this->equivalences;
    }
}