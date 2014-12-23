<?php

namespace spec\Ecedi\Donate\PaymentBundle\Rum;

use PhpSpec\ObjectBehavior;
use Ecedi\Donate\CoreBundle\Entity\Intent;

class PreformatedRumGeneratorSpec extends ObjectBehavior
{
    const PREFIX = 'PHPSPEC';
    private $intent;

    public function let(Intent $intent)
    {
        $intent->getCreatedAt()->willReturn(new \DateTime('2014-12-22'));
        $intent->getId()->willReturn(144);
        $this->intent = $intent;
        $this->beConstructedWith(self::PREFIX);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\PaymentBundle\Rum\RumGeneratorInterface');
    }

    public function it_should_generate_a_35_char_long_rum()
    {
        $rum = $this->generate($this->intent);
        $rum->shouldBeString();
        $rum->shouldHaveLenght(35);
    }

    public function it_should_contain_prefix_date_and_id()
    {
        $rum = $this->generate($this->intent);
        $rum->shouldBe('         PHPSPEC-WEB-2014-12-22-144');
    }

    public function getMatchers()
    {
        return [
            'haveLenght' => function ($subject, $key) {
                if (strlen($subject) === $key) {
                    return true;
                }

                return false;
            }
        ];
    }
}
