<?php
/**
 * @author Alexandre Fayolle <afayolle@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package Ecollecte
 */

namespace Ecedi\Donate\PayboxBundle\Paybox;

use Ecedi\Donate\CoreBundle\Entity\Payment;

/**
 * Paybox Response Manager
 *
 * Pour plus d'informations sur les valeures retournées par Paybox
 * @see : http://www1.paybox.com/espace-integrateur-documentation/dictionnaire-des-donnees/paybox-system/
 *
 * this class is statefull, it cannot be a service
 * It is almost a ValueObject wrapper
 *
 * @since 2.2.0
 */
class StatusNormalizer
{
    /**
     * Cette méthode convertie un code de status Paybox en un status de Payment
     *
     * @see Ecedi\Donate\CoreBundle\Entity\Payment::getAllowedStatus()
     *
     * @param  integer $status a Paybox Status
     * @return string  one legal value from Ecedi\Donate\CoreBundle\Entity\Payment::getAllowedStatus()
     */
    public function normalize($status)
    {
        switch ($status) {
            case '00000':
                return Payment::STATUS_PAYED;
                break;
            case '00004':
            case '00010':
            case '00011':
            case '00021':
            case '00033':
            case '00040':
                return Payment::STATUS_DENIED;
                break;
            case '00008':
            case '00029':
                return Payment::STATUS_INVALID;
                break;
            case '00001':
            case '00003':
            case '00006':
            case '00009':
            case '00015':
            case '00016':
            case '00030':
                return Payment::STATUS_FAILED;
                break;
            case '00031':
            case '00032':
                return Payment::STATUS_UNKNOWN;
                break;
            case '99999':
                return Payment::STATUS_NEW;
                break;
            default:
                if (substr($status, 0, 3) == '001') { // Paiement refusé par le centre d'autorisation
                    return Payment::STATUS_DENIED;
                    break;
                }

                return Payment::STATUS_UNKNOWN;
                break;
        }
    }
}
