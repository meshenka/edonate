<?php

namespace spec\Ecedi\Donate\PaymentBundle\Rum;

use PhpSpec\ObjectBehavior;
use Ecedi\Donate\CoreBundle\Entity\Intent;

class EmptyRumGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\PaymentBundle\Rum\RumGeneratorInterface');
    }

    public function it_should_generate_a_35_char_number(Intent $intent)
    {
        $rum = $this->generate($intent);
        $rum->shouldBeString();
        $rum->shouldBeLike(str_repeat(' ', 35));
    }
}
