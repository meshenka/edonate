<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package ECollecte
 * @subpackage SEPA
 */

namespace Ecedi\Donate\PaymentBundle\Rum;

use Ecedi\Donate\CoreBundle\Entity\Intent;

/**
 * This interface is the base API to provide unique RUM for SEPA Mandate
 *
 * @since  2.0.0
 * @api
 */
interface RumGeneratorInterface
{
    /**
     * generate a RUM (Reference Unique de Mandat)
     * @return string a unique RUM Number 35 char long sans caractères spéciaux
     */
    public function generate(Intent $intent);
}
