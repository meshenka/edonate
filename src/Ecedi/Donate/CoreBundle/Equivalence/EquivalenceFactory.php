<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * EquivalenceFactory generate Equivalence
 *
 */
namespace Ecedi\Donate\CoreBundle\Equivalence;
use  Ecedi\Donate\CoreBundle\Entity\Equivalence;

class EquivalenceFactory
{
    private $config;

    public function __construct(array $configuration)
    {
        $this->config = $configuration;
    }

    public function create($amount, $label, $currency = 'EUR')
    {
        return new Equivalence($amount, $label, $currency);
    }

    public function get()
    {
        $equivalences = [];

        foreach ($this->config as $c) {
            $equivalences[] = $this->create($c['amount'], $c['label'], $c['currency']);
        }

        return $equivalences;
    }

}
