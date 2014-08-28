<?php

namespace spec\Ecedi\Donate\CoreBundle\IntentManager;

use Ecedi\Donate\CoreBundle\IntentManager\IntentManagerInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\CoreBundle\PaymentMethod\Discovery;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\IntentEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class DefaultIntentManagerSpec extends ObjectBehavior
{
	/**
	 * @var ContainerInterface
	 */
	public $container;

	/**
	 * @var RegistryInterface
	 */
	public $doctrine;

	/**
	 * @var Discovery
	 */
	public $discovery;

	/**
	 * @var EventDispatcherInterface
	 */
	public $dispatcher;

	/**
	 * ObjectManager
	 */
	public $manager;

	public $logger;

	public $intentRepository;

	function let(ContainerInterface $container, 
		RegistryInterface $doctrine, 
		Discovery $discovery, 
		EventDispatcherInterface $dispatcher, 
		ObjectManager $manager,
		LoggerInterface $logger,
		ObjectRepository $intentRepository) {

		$this->container = $container;
		$this->doctrine = $doctrine;
		$this->discovery = $discovery;
		$this->dispatcher = $dispatcher;
		$this->manager = $manager;
		$this->logger = $logger;
		$this->intentRepository = $intentRepository;

		$this->trainContainer();

		$this->beConstructedWith($container);


	}

	protected function trainContainer() {

		$this->doctrine->getManager()->willReturn($this->manager);

		$this->doctrine->getRepository('DonateCoreBundle:Intent')->willReturn($this->intentRepository);
		$this->container->has('doctrine')->willReturn(true);
		$this->container->get('doctrine')->willReturn($this->doctrine);
		$this->doctrine->getManager()->willReturn($this->manager);

		$this->container->get('donate_core.payment_method_discovery')->willReturn($this->discovery);
		$this->container->get('event_dispatcher')->willReturn($this->dispatcher);
		$this->container->get('logger')->willReturn($this->logger);

		
	}

	/**
	 * le container peut créer une instance
	 */
    function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\IntentManager\DefaultIntentManager');
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\IntentManager\IntentManagerInterface');
    }

    /**
     * l'IntentManager manipule et persiste l'état d'un Intent
     */
    function it_should_set_pending_status_on_intent(Intent $intent) {
    	$this->manager->persist($intent)->shouldBeCalled();
    	$this->manager->flush()->shouldBeCalled();
    	$intent->setStatus(Intent::STATUS_PENDING)->shouldBeCalled();
    	$this->pending($intent);
    }

    /**
     * L'IntentManager intialise des Intent et dispatch un event DonateEvents::POST_NEW_INTENT
     */
    function it_should_dispatch_event_on_intent_creation() {
    	$this->dispatcher
    		->dispatch(DonateEvents::POST_NEW_INTENT, Argument::type('Ecedi\Donate\CoreBundle\Event\IntentEvent'))
    		->shouldBeCalled();
    	$intent = $this->newIntent(10, 'specmethod');
    	$intent->shouldHaveType('Ecedi\Donate\CoreBundle\Entity\Intent');
    	$intent->getAmount()->shouldBe(10);
    	$intent->getPaymentMethod()->shouldBe('specmethod');

    }

    /**
     * L'IntentManager gère les présentations à l'API de paiement
     *
     */
    function it_should_delegate_payment_to_payment_method(Intent $intent, PaymentMethodInterface $pm, Response $response) {

    	$this->discovery->getMethod('specmethod')->willReturn($pm);
    	$pm->pay($intent)->willReturn($response);

    	$intent->getPaymentMethod()->willReturn('specmethod');

    	$this->handlePay($intent)->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    	$pm->pay($intent)->shouldHaveBeenCalled();
    }

    function it_should_add_payment_to_intent(Intent $intent, Payment $payment) {

    	$this->intentRepository->find(10)->willReturn($intent);
    	$intent->getType()->willReturn(Intent::TYPE_SPOT);
    	$intent->getStatus()->willReturn(Intent::STATUS_PENDING);

    	//on ajoute bien le payment a l'intent
    	$intent->addPayment($payment)->shouldBeCalled();
    	$payment->setIntent($intent)->shouldBeCalled();

    	//on change le status de l'intent 
    	$intent->setStatus(Intent::STATUS_DONE)->shouldBeCalled();

    	$this->attachPayment(10, $payment);

    	//on persist les deux entity
    	$this->manager->persist($intent)->shouldHaveBeenCalled();
    	$this->manager->persist($payment)->shouldHaveBeenCalled();    	

    	//on dispatch un event
    	$this->dispatcher->dispatch(DonateEvents::EVENT_PAYMENT, Argument::type('Ecedi\Donate\CoreBundle\Event\PaymentEvent'))->shouldBeCalled();
    }

    function it_should_persist_payment_if_no_intent(Intent $intent, Payment $payment) {

    	//on ajoute bien le payment a l'intent
    	$intent->addPayment($payment)->shouldNotBeCalled();
    	$payment->setIntent($intent)->shouldNotBeCalled();

    	//on change le status de l'intent 
    	$intent->setStatus(Intent::STATUS_DONE)->shouldNotBeCalled();

    	$this->attachPayment(false, $payment);

    	//on persist les deux entity
    	$this->manager->persist($payment)->shouldHaveBeenCalled();

    	//on dispatch un event
    	$this->dispatcher->dispatch(DonateEvents::EVENT_PAYMENT, Argument::type('Ecedi\Donate\CoreBundle\Event\PaymentEvent'))->shouldBeCalled();
    }
}
