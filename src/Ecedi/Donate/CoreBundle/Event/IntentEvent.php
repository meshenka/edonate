<?php

namespace Ecedi\Donate\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Ecedi\Donate\CoreBundle\Entity\Intent;

class IntentEvent extends Event
{

    private $intent;

   /**
    * intent
    *
    * @return Intent intent
    */
   public function getIntent()
   {
       return $this->intent;
   }

   /**
    * intent
    *
    * @param Intent $newintent Intent
    */
   protected function setIntent(Intent $intent)
   {
       $this->intent = $intent;

       return $this;
   }

    public function __construct(Intent $intent)
    {
        $this->setIntent($intent);
    }
}
