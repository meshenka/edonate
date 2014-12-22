<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package ECollecte
 * @subpackage SEPA
 */

namespace Ecedi\Donate\PaymentBundle\Rum;

use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * Provide an Empty RUM number, will be set by back-office members
 * Use it when client does not have a strategy
 *
 * @since  2.0.0
 */
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
