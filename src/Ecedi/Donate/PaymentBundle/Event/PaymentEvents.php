<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @package ECollecte
 * @subpackage PaymentMethod
 */
namespace Ecedi\Donate\PaymentBundle\Event;

/**
 * Define some events
 *
 * @since  2.0.0
 */
final class PaymentEvents
{
    /**
     * When a PDF SEPA Mandate is requested for generation
     */
    const INTENT_DOCUMENT_GENERATED = 'donate.payment.document.generated';
}
