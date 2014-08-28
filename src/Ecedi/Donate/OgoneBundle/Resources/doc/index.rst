# Configuration
donate_ogone:
	prod: false
	pspid: %ogone_pspid%
	prefix: "ECEDIDEV"
	options: { 
		'LOGO': "", 
		'TP': 33,
		}

# Sha1 In coté ogone

Nous utilisons la hashkey suivante :
  * amount : 15.00 -> 1500
  * currency : EUR
  * Operation : RES
  * orderID: 1234
  * PSPID : MyPSPID

 Ce n'est pas configurable

 # TEST POSTSALE

 une OK
 ?ORDERID=SYGLOC-1&AMOUNT=10000&CURRENCY=EUR&PM=CreditCard&ACCEPTANCE=777777777&STATUS=95&CARDNO=******&PAYID=88888888&NCERROR=0&BRAND=MasterCard&ED=2014/09&TRXDATE=2013-10-21&CN=Gogel Sylvain&SHASIGN=b4e58f08a4f87f26c9a7ed70ab238eceddd258f1

?ORDERID=SYGLOC-1&AMOUNT=10000&CURRENCY=EUR&PM=CreditCard&ACCEPTANCE=777777777&STATUS=92&CARDNO=******&PAYID=88888888&NCERROR=0&BRAND=MasterCard&ED=2014/09&TRXDATE=2013-10-21&CN=Gogel Sylvain&SHASIGN=f56646befd8afab9a9680450d13771d0cdb13233


``
curl -X GET "http://donate.loc/app_dev.php/ogone/api/postsale?orderID=SYGLOC-1&amount=10000&currency=EUR&PM=CreditCard&ACCEPTANCE=777777777&STATUS=95&CARDNO=******&PAYID=88888888&NCERROR=0&BRAND=MasterCard&ED=2014/09&TRXDATE=2013-10-21&CN=Gogel Sylvain&SHASIGN=b4e58f08a4f87f26c9a7ed70ab238eceddd258f1"
``

	// CURRENCY Order currency
	// PM Payment method
	// ACCEPTANCE Acceptance code returned by acquirer (Payment::autorisation)
	// STATUS Transaction status (see Appendix: Status overview)
	// CARDNO Masked card number
	// PAYID Payment reference in our system (Payment::transaction)
	// NC ERROR Error code
	// BRAND Card brand (our system derives this from the card number)
	// ED Expiry date
	// TRXDATE Transaction date
	// CN Cardholder/customer name
	// SHASIGN SHA signature calculated by our system (if SHA-1-OUT configured)

# Spécification des status Ogone
Status of the payment.

  * 0 Incomplete or invalid
  * 1 Cancelled by client
  * 2 Authorization refused
  * 4 Order stored
    * 41 Waiting client payment
  * 5 Authorized
    * 51 Authorization waiting
    * 52 Authorization not known
    * 59 Author. to get manually
  * 6 Authorized and canceled
    * 61 Author. deletion waiting
    * 62 Author. deletion uncertain
	* 63 Author. deletion refused
  * 7 Payment deleted
    * 71 Payment deletion pending
	* 72 Payment deletion uncertain
	* 73 Payment deletion refused
    * 74 Payment deleted (not accepted)
    * 75 Deletion processed by merchant
  * 8 Refund
	* 81 Refund pending
	* 82 Refund uncertain
	* 83 Refund refused
	* 84 Payment declined by the acquirer (will be debited)
	* 85 Refund processed by merchant
  * 9 Payment requested
    * 91 Payment processing
    * 92 Payment uncertain
  	* 93 Payment refused
  	* 94 Refund declined by the acquirer
  	* 95 Payment processed by merchant
    * 97-99 Being processed (intermediate technical status)


The table above summarises the possible statuses of the payments.

Statuses in 1 digit are 'normal' statuses:

    0 means the payment is invalid (e.g. data validation error) or the processing is not complete either because it is still underway, or because the transaction was interrupted. If the cause is a validation error, an additional error code (*) (NCERROR) identifies the error.
    1 means the customer cancelled the transaction.
    2 means the acquirer did not authorise the payment.
    5 means the acquirer autorised the payment.
    9 means the payment was captured. 

Statuses in 2 digits correspond either to 'intermediary' situations or to abnormal events. When the second digit is:

    1, this means the payment processing is on hold.
    2, this means an unrecoverable error occurred during the communication with the acquirer. The result is therefore not determined. You must therefore call the acquirer's helpdesk to find out the actual result of this transaction.
    3, this means the payment processing (capture or cancellation) was refused by the acquirer whilst the payment had been authorised beforehand. It can be due to a technical error or to the expiration of the authorisation. You must therefore call the acquirer's helpdesk to find out the actual result of this transaction.
    4, this means our system has been notified the transaction was rejected well after the transaction was sent to your acquirer.
    5, this means our system hasn’t sent the requested transaction to the acquirer since the merchant will send the transaction to the acquirer himself, like he specified in his configuration. 