<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi 2014
 */
namespace Ecedi\Donate\PaymentBundle\Event;

/**
 * Define some events.
 *
 * @since  2.0.0
 */
final class PaymentEvents
{
    /**
     * When a PDF SEPA Mandate is requested for generation.
     */
    const INTENT_DOCUMENT_GENERATED = 'donate.payment.document.generated';
}
