<?php

namespace spec\Ecedi\Donate\CoreBundle\Equivalence;

use PhpSpec\ObjectBehavior;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;

class EquivalenceFactorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([
            PaymentMethodInterface::TUNNEL_SPOT => [
                ['amount' => 10, 'label' => '10', 'currency' => 'USD', 'default' => true],
                ['amount' => 20, 'label' => '20', 'currency' => 'EUR', 'default' => false],
            ],
        ]);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\Equivalence\EquivalenceFactory');
    }

    public function it_should_create_equivalence()
    {
        $amount = 10;
        $label = 'php spec test';
        $currency = 'EUR';

        $eq = $this->create($amount, $label, $currency);
        $eq->shouldReturnAnInstanceOf('Ecedi\Donate\CoreBundle\Entity\Equivalence');
    }

    public function it_should_get_equivalence_from_configuration()
    {
        $get = $this->get();
        $get->shouldBeArray();
        $get->shouldHaveCount(2);
        $get[0]->getAmount()->shouldReturn(10);
    }
}
