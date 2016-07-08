<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi 2014
 */
namespace Ecedi\Donate\PaymentBundle\EventListener;

use Ecedi\Donate\PaymentBundle\Event\IntentDocumentGeneratedEvent;
use Ecedi\Donate\PaymentBundle\PaymentMethod\Plugin\SepaOfflinePaymentMethod;
use Ecedi\Donate\PaymentBundle\Rum\RumGeneratorInterface;
use ZendPdf as Pdf;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * This listener listen to PaymentEvents::INTENT_DOCUMENT_GENERATED to generate
 * a PDF Mandate.
 *
 * @since  2.0.0
 */
class GenerateSepaPdfListener
{
    /**
     * @var RumGeneratorInterface
     */
    private $rumGenerator;

    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * constructor.
     *
     * @param RumGeneratorInterface $rumGenerator A RUM Generator
     * @param HttpKernelInterface   $kernel       the kernel, needed to locate files
     */
    public function __construct(RumGeneratorInterface $rumGenerator, HttpKernelInterface $kernel)
    {
        $this->rumGenerator = $rumGenerator;
        $this->kernel = $kernel;
    }
    /**
     * Generate the Pdf.
     *
     * @param IntentDocumentGeneratedEvent $event the event
     *
     * @see http://stackoverflow.com/questions/7585474/accessing-files-relative-to-bundle-in-symfony2
     *
     * @todo finaliser l'inscrition de toutes les info dynamiques
     * @todo  avoir une sepa-template neutre
     * @todo  insérer le logo du layout courant dans le pdf
     * @todo  insérer des textes simple en y ajoutant le nom du client dynamiquement
     * @todo  insérer l'ICS
     */
    public function generate(IntentDocumentGeneratedEvent $event)
    {
        /*
         * @var Ecedi\Donate\CoreBundle\Entity\Intent
         */
        $intent = $event->getIntent();

        if ($intent->getPaymentMethod() === SepaOfflinePaymentMethod::ID && $event->getDocument() === null) {
            $pdf = new Pdf\PdfDocument();

            $pdf->pages[] = ($page1 = $pdf->newPage('A4'));
            $page1->setFont(Pdf\Font::fontWithName(Pdf\Font::FONT_HELVETICA), 12);

            $path = $this->kernel->locateResource('@DonatePaymentBundle/Resources/public/img/sepa-template.jpg');

            $stampImageJPG = Pdf\Image::imageWithPath($path);
            $page1->drawImage($stampImageJPG, 0, 0, 595, 842);
            $page1->drawText($this->rumGenerator->generate($intent), 170, 389, 'UTF-8');
            //TODO faire la suite

            $event->setDocument($pdf);
            $event->stopPropagation();
        }
    }
}
