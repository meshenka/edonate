<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * EquivalenceFactory generate Equivalence
 */
namespace Ecedi\Donate\CoreBundle\Equivalence;

use Ecedi\Donate\CoreBundle\Entity\Equivalence;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;

class EquivalenceFactory
{
    private $config;

    public function __construct(array $configuration)
    {
        $this->config = $configuration;
    }

    public function create($amount, $label, $currency = 'EUR', $default = false)
    {
        return new Equivalence($amount, $label, $currency, $default);
    }

    /**
     * get all equivalences for all tunnels.
     *
     * @return array key is the tunnel constant, value is an array of Equivalence Entities
     *               TODO refactore this to use Event and Listener (which will allow to have different way to gather equivalences)
     */
    public function getAll()
    {
        $equivalences = array();

        foreach ($this->config as $tunnel => $val) {
            $equivalences[$tunnel] = $this->get($tunnel);
        }

        return $equivalences;
    }

    public function get($tunnel = PaymentMethodInterface::TUNNEL_SPOT)
    {
        $equivalences = [];
        $tunnel = $this->config[$tunnel];
        foreach ($tunnel as $c) {
            $equivalences[] = $this->create($c['amount'], $c['label'], $c['currency'], $c['default']);
        }

        return $equivalences;
    }
}
