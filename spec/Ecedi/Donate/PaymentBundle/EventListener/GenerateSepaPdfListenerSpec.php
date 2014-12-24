<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package Ecollecte
 * @subpackage SEPA
 * @copyright Agence Ecedi 2014
 */
namespace spec\Ecedi\Donate\PaymentBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Ecedi\Donate\PaymentBundle\Event\IntentDocumentGeneratedEvent;
use Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin\SepaOfflinePaymentMethod;
use Ecedi\Donate\PaymentBundle\Rum\RumGeneratorInterface;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\HttpKernel\Kernel;
use Prophecy\Argument;
use ZendPdf\PdfDocument;
/**
 * Unit tests for GenerateSepaPdfListener
 */
class GenerateSepaPdfListenerSpec extends ObjectBehavior
{
    /**
     * @var IntentDocumentGeneratedEvent
     */
    private $event;

    /**
     * @var RumGeneratorInterface
     */
    private $rumGenerator;

    /**
     * @var Intent
     */
    private $intent;

    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    public function let(IntentDocumentGeneratedEvent $event, RumGeneratorInterface $rumGenerator, Intent $intent, Kernel $kernel)
    {
        $this->event = $event;
        $this->rumGenerator = $rumGenerator;
        $this->intent = $intent;
        $this->kernel = $kernel;

        $this->event->getIntent()->willReturn($this->intent);
        $kernel->locateResource('@DonatePaymentBundle/Resources/public/img/sepa-template.jpg')->willReturn(__DIR__.'/test.jpg');
        $this->beConstructedWith($rumGenerator, $kernel);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType('Ecedi\Donate\PaymentBundle\EventListener\GenerateSepaPdfListener');
    }

    public function it_should_create_pdf()
    {
        $this->intent->getPaymentMethod()->willReturn(SepaOfflinePaymentMethod::ID);
        $this->event->getDocument()->willReturn(null);
        $this->event->setDocument(Argument::Type('ZendPdf\PdfDocument'))->shouldBeCalled();
        $this->event->stopPropagation()->shouldBeCalled();
        $this->generate($this->event);
    }

    public function it_should_not_create_pdf_if_not_right_type()
    {
        $this->intent->getPaymentMethod()->willReturn('PHPSPEC');
        $this->event->getDocument()->willReturn(null);
        $this->event->setDocument(Argument::Type('ZendPdf\PdfDocument'))->shouldNotBeCalled();
        $this->event->stopPropagation()->shouldNotBeCalled();
        $this->generate($this->event);
    }
    public function it_should_not_create_pdf_document_already_created(PdfDocument $doc)
    {
        $this->intent->getPaymentMethod()->willReturn(SepaOfflinePaymentMethod::ID);
        $this->event->getDocument()->willReturn($doc);
        $this->event->setDocument(Argument::Type('ZendPdf\PdfDocument'))->shouldNotBeCalled();
        $this->event->stopPropagation()->shouldNotBeCalled();
        $this->generate($this->event);
    }
}
