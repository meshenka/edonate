<?php

namespace Ecedi\Donate\CoreBundle\PaymentMethod\Plugin;

use Ecedi\Donate\CoreBundle\Entity\Intent;

interface PaymentMethodInterface
{
	const TUNNEL_RECURING = 'recurring';
	const TUNNEL_SPOT = 'spot';
	const TUNNEL_SPONSORSHIP = 'sponsorship';

	const PAYMENT_STATUS_COMPLETED = 'completed';
	const PAYMENT_STATUS_CANCELED = 'canceled';
	const PAYMENT_STATUS_DENIED = 'denied';
	const PAYMENT_STATUS_FAILED = 'failed';

    public function getId();

    public function getName();

    public function getTunnel();

    public function autorize(Intent $intent);

    public function pay(Intent $intent);
}
