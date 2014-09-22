<?php

namespace Ecedi\Donate\CoreBundle\Event;

/**
 * Contains all domain events thrown by CoreBundle and FrontBundle
 *
 *
 * @api
 */
final class DonateEvents
{

    //when someone submit a new donation via the form
    const DONATION_REQUESTED = "donate.intent.requested";

    //when someone submit a new donation via the form
    const AUTORIZATION_REQUESTED = "donate.autorization.requested";

    //when the TPE is submited
    const PAYMENT_REQUESTED = "donate.payment.requested";

    //when the TPE is send back transaction details
    const PAYMENT_RECEIVED = "donate.payment.received";
    
    //when transaction details says ok
    const PAYMENT_COMPLETED = "donate.payment.completed";

    //when transaction details says canceled
    const PAYMENT_CANCELED = "donate.payment.canceled";

    //when transaction details says denied
    const PAYMENT_DENIED = "donate.payment.denied";

    //when transaction details says failed
    const PAYMENT_FAILED = "donate.payment.failed";

    //when transaction details says authorization is granted
    const PAYMENT_AUTHORIZED = "donate.payment.authorized";

    private function __construct()
    {

    }

}
