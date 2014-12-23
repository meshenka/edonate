<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package ECollecte
 * @subpackage PaymentMethod
 */

namespace Ecedi\Donate\PaymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Ecedi\Donate\CoreBundle\Entity\Intent;
use ZendPdf\PdfDocument;

/**
 * This event is dispatched when a Pdf version of a Sepa Mandate is Requested
 * An Event Listener should Decide if it can generate a Document for this intent and provide the document
 * (and stop propagation)
 */
class IntentDocumentGeneratedEvent extends Event
{
    /**
     * The Intent
     * @var Ecedi\Donate\CoreBundle\Entity\Intent
     */
    private $intent;
    /**
     * [
     * @var ZendPdf\PdfDocument
     */
    private $document;

    /**
     * Document
     *
     * @return ZendPdf\PdfDocument a document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Document
     *
     * @param ZendPdf\PdfDocument $newdocument document
     */
    public function setDocument(PdfDocument$document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * intent
     *
     * @return Intent the intent
     */
    public function getIntent()
    {
        return $this->intent;
    }

    public function __construct(Intent $intent)
    {
        $this->intent = $intent;
    }
}
