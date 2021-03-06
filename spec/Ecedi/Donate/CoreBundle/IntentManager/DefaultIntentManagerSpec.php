<?php

namespace spec\Ecedi\Donate\CoreBundle\IntentManager;

use Ecedi\Donate\CoreBundle\IntentManager\IntentManagerInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\CoreBundle\PaymentMethod\Discovery;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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

    /**
     * @var Request
     */
    private $request;

    /**
     *
     * @var SessionInterface
     */
    private $session;

    public function let(ContainerInterface $container,
        RegistryInterface $doctrine,
        Discovery $discovery,
        EventDispatcherInterface $dispatcher,
        ObjectManager $manager,
        LoggerInterface $logger,
        ObjectRepository $intentRepository,
        Request $request,
        SessionInterface $session)
    {
        $this->container = $container;
        $this->doctrine = $doctrine;
        $this->discovery = $discovery;
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
        $this->logger = $logger;
        $this->intentRepository = $intentRepository;
        $this->request = $request;
        $this->session = $session;
        $this->trainContainer();

        $this->beConstructedWith($container);
    }

    protected function trainContainer()
    {
        $this->request->getSession()->willReturn($this->session);
        $this->request->getLocale()->willReturn('fr_FR');
        $this->doctrine->getManager()->willReturn($this->manager);
        $this->doctrine->getManager()->willReturn($this->manager);

        $this->doctrine->getRepository('DonateCoreBundle:Intent')->willReturn($this->intentRepository);
        $this->container->has('doctrine')->willReturn(true);
        $this->container->get('doctrine')->willReturn($this->doctrine);
        $this->container->get('request')->willReturn($this->request);

        $this->container->get('donate_core.payment_method_discovery')->willReturn($this->discovery);
        $this->container->get('event_dispatcher')->willReturn($this->dispatcher);
        $this->container->get('logger')->willReturn($this->logger);
    }

    /**
     * le container peut créer une instance
     */
    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\IntentManager\DefaultIntentManager');
        $this->shouldHaveType('Ecedi\Donate\CoreBundle\IntentManager\IntentManagerInterface');
    }

    /**
     * L'IntentManager intialise des Intent et dispatch un event DonateEvents::POST_NEW_INTENT
     */
    public function it_should_dispatch_event_on_intent_creation()
    {
        $this->dispatcher
            ->dispatch(DonateEvents::DONATION_REQUESTED, Argument::type('Ecedi\Donate\CoreBundle\Event\DonationRequestedEvent'))
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
    public function it_should_delegate_payment_to_payment_method(Intent $intent, PaymentMethodInterface $pm, Response $response)
    {
        $this->discovery->getMethod('specmethod')->willReturn($pm);
        $intent->getId()->willReturn(144);
        $pm->getTunnel()->willReturn(PaymentMethodInterface::TUNNEL_SPOT);
        $pm->pay($intent)->willReturn($response);

        $intent->getPaymentMethod()->willReturn('specmethod');

        $this->handle($intent)->shouldHaveType('Symfony\Component\HttpFoundation\Response');
        $pm->pay($intent)->shouldHaveBeenCalled();
    }

    public function it_should_add_payment_to_intent(Intent $intent, Payment $payment)
    {
        $this->intentRepository->find(10)->willReturn($intent);
        $payment->getStatus()->willReturn(Payment::STATUS_PAYED);
        $intent->getType()->willReturn(Intent::TYPE_SPOT);
        $intent->getStatus()->willReturn(Intent::STATUS_PENDING);

        //on ajoute bien le payment a l'intent
        $intent->addPayment($payment)->shouldBeCalled();

        //on change le status de l'intent
        $intent->setStatus(Intent::STATUS_DONE)->shouldBeCalled();

        $this->attachPayment(10, $payment);

        //on persist les deux entity
        $this->manager->persist($intent)->shouldHaveBeenCalled();
        $this->manager->persist($payment)->shouldHaveBeenCalled();
    }

    public function it_should_persist_payment_if_no_intent(Intent $intent, Payment $payment)
    {
        //on ajoute bien le payment a l'intent
        $intent->addPayment($payment)->shouldNotBeCalled();
        $payment->setIntent($intent)->shouldNotBeCalled();
        $payment->getStatus()->willReturn(Payment::STATUS_PAYED);

        //on change le status de l'intent
        $intent->setStatus(Intent::STATUS_DONE)->shouldNotBeCalled();

        $this->attachPayment(false, $payment);

        //on persist les deux entity
        $this->manager->persist($payment)->shouldHaveBeenCalled();
    }
}
