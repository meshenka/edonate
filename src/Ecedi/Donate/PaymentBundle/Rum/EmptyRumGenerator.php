<?php
namespace Ecedi\Donate\PaymentBundle\Rum;

use Ecedi\Donate\CoreBundle\Entity\Intent;

class EmptyRumGenerator implements RumGeneratorInterface
{
    /**
     * @{inheritdoc}
     */
    public function generate(Intent $intent)
    {
        return str_repeat('', 35);
    }
}
