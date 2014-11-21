<?php

namespace Ecedi\Donate\CoreBundle\IntentManager;

use Ecedi\Donate\CoreBundle\Entity\Intent;

interface IntentManagerInterface
{
    // public function handleAutorize(Intent $intent);
    // public function handlePay(Intent $intent);
    public function handle(Intent $intent);
}
