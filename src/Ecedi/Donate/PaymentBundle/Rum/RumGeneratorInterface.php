<?php
namespace Ecedi\Donate\PaymentBundle\Rum;
use Ecedi\Donate\CoreBundle\Entity\Intent;

class RumGeneratorInterface {
    /**
     * generate a RUM (Reference Unique de Mandat)
     * @return string a unique RUM Number 35 char long sans caractères spéciaux
     */
    function generate(Intent $intent);
}