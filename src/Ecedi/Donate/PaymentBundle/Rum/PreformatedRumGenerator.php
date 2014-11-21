<?php
namespace Ecedi\Donate\PaymentBundle\Rum;

use Ecedi\Donate\CoreBundle\Entity\Intent;

class PreformatedRumGenerator implements RumGeneratorInterface
{
    private $prefix;

    public function __construct($prefix)
    {
        //prefix must be 3 to 6 char
        $this->prefix = $prefix;
    }

    /**
     * @{inheritdoc}
     * Generate RUM from a KEY + a MD5sum
     */
    public function generate(Intent $intent)
    {
        //CODECLIENT-WEB-YYYY-MM-DD-ORDERID
        $rum = $this->prefix.'-WEB-'.$intent->getCreatedAt()->format('Y-m-d').'-'.$intent->getId();

        return strtoupper(str_pad($rum, 35, ' ', STR_PAD_LEFT));
    }
}
