<?php

namespace Ecedi\Donate\FrontBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ecedi\Donate\CoreBundle\Event\PaymentCompletedEvent;
use Ecedi\Donate\CoreBundle\Event\PaymentAuthorizedEvent;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Symfony\Component\Templating\EngineInterface;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Symfony\Component\Translation\TranslatorInterface;

// TODO send mail only if donate_core.email.donator = true
class ThanksEmailEventListener implements EventSubscriberInterface {

	private $templating;
	private $mailer;
	private $translator;
	private $noreply;

	public function __construct(EngineInterface $templating, \Swift_Mailer $mailer, TranslatorInterface $translator, $noreply) {
		$this->templating = $templating;
		$this->mailer = $mailer;
		$this->translator = $translator;
		$this->noreply = $noreply;

	}

	public function onCompleted(PaymentCompletedEvent $event)
	{
		$payment = $event->getPayment();
		$this->send($payment);

	}

	public function onAuthorized(PaymentAuthorizedEvent $event)
	{
		$payment = $event->getPayment();
		$this->send($payment);
	}

	protected function send(Payment $payment) {
		//si on a pas associé l'intent alors pas d'email
		
		if($payment->getIntent()) {
			$body = $this->templating->render(
				'DonateFrontBundle:Mail:thanks.txt.twig',
				array(
					'intent' => $payment->getIntent(),
					'payment' => $payment
					)
				);
			$message = \Swift_Message::newInstance()
			->setSubject($this->translator->trans('Thank you for your generosity'))
			->setFrom($this->noreply)
			->setTo($payment->getIntent()->getCustomer()->getEmail())
			->setBody($body, 'text/html');

			$this->mailer->send($message);
		}
	}

	public static function getSubscribedEvents()
	{
		return array(
			DonateEvents::PAYMENT_COMPLETED => array(
				array('onCompleted', 10)
				),
			DonateEvents::PAYMENT_AUTHORIZED => array(
				array('onAuthorized', 10)
				)
			);
	}	
}