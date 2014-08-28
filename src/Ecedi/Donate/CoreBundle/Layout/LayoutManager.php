<?php

namespace Ecedi\Donate\CoreBundle\Layout;
use  Ecedi\Donate\CoreBundle\Entity\Layout;
use  Ecedi\Donate\CoreBundle\Entity\Block;
use Doctrine\Common\Persistence\ObjectManager;

class LayoutManager {
	private $em;

	public function __construct(ObjectManager $em) {
	   $this->em = $em;
	}


    public function makeDefault(Layout $layout) {

        $repo = $this->em->getRepository('DonateCoreBundle:Layout');
        $defaultLayout = $repo->getDefaultLayout($layout->getLanguage());

        if($layout === $defaultLayout) {
            return [];
        }
        
        $defaultLayout->setIsDefault(false);        
        $layout->setIsDefault(true);
        
        return [$defaultLayout, $layout];
    }

    public function getDefault($lang = 'fr') {
        $repo = $this->em->getRepository('DonateCoreBundle:Layout');
        return $repo->getDefaultLayout($lang);
    }

}