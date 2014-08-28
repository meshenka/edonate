<?php

namespace Ecedi\Donate\OgoneBundle\Ogone;

use Ecedi\Donate\CoreBundle\Entity\Payment;

class OgoneStatusNormalizer {

	/**
     * Cette méthode convertie un code de status Ogone en un status de l'intent
     *
     * @see Ecedi\Donate\CoreBundle\Entity\Payment::getAllowedStatus()
     *
     * @param integer $status
     * @return string one legal value from
     */
    public function normalize($status)
    {

        switch ($status) {
            case 0: //Incomplete or invalid

                return Payment::STATUS_INVALID;
                break;
            case 1: //Cancelled by client
            case 6: //Authorized and canceled
            case 61: //Author. deletion waiting
            case 62: //Author. deletion uncertain
            case 64:  // ??

                return Payment::STATUS_CANCELED;
                break;
            // case 4: // Order stored
          //   case 41:

          //   case 51:
          //   case 52:
          //   case 55:
          //   case 59:
          //   case 91:
          //   case 92:
          //   case 99:
                // return Payment::STATUS_FAILED;
          //     	break;
            case 5: //Authorized

                  return Payment::STATUS_AUTHORIZED;
                  break;
            case 51: //Authorization waiting
            case 52: //Authorization not known
            case 59: //Author. to get manually

                  return Payment::STATUS_AUTHORIZED;
                  break;
            case 9: //Payment requested
            case 95: //Payment processed by merchant
            case 97: //Being processed (intermediate technical status)
            case 98: //Being processed (intermediate technical status)
            case 99: //Being processed (intermediate technical status)

                  return Payment::STATUS_PAYED;
                  break;
            case 2:  //Authorization refused
            case 83: //Refund refused
            case 93: //Payment refused
            case 63: //Author. deletion refused
            case 73: //Payment deletion refused
            case 94: //Refund declined by the acquirer

                  return Payment::STATUS_DENIED; //autorisation refusé ou paiement refusé
                  break;
            default:
                  return Payment::STATUS_FAILED;
        }
    }

	
}