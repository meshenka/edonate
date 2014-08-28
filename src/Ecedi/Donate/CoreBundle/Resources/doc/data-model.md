# Modèle de donnée

Customer 
   1 -> * Order 
   		0 -> * -> Payment

DonateFormSetting 
	1 -> 1 MailTemplate
		* -> 1 Contact (sender)
		* -> * Contact (cc)
		* -> * Contact (bc)


Si pas d'espace donateur, alors chaque soumission génère un nouveau customer. Si on a moyen de ré-identifier un donateur alors on peut le réutiliser

* DonateFormSetting
  * id (serial) int(11)
  * name varchar(255)
  * mail_template int(11) MailTemplate::id  one-to-one MailTemplate Template du mail de confirmation
  * default boolean est-ce la configuration active (on peut avoir plusieurs configurations)
  
* MailTemplate
  * id (serial) int(11)
  * sender int(11) one-to-one Contact
  * cc int(11) many-to-many Contact
  * bc int(11) many-to-many Contact
  * reply_to int(11) many-to-many Contact
  * subject varchar(255)
  * body longtext

* Contact -- Référence à un email
  * id (serial) int(11)
  * name varchar(255) index
  * email varchar(255) index

* Customer
  * id (serial) int(11)
  * remote_id (index) varchar(255) ; ce champs sert pour l'interfacage avec un outil d'import/gestion des donateurs externe. vide par défaut
  * civility varchar(6)
  * firstName varchar(255)
  * lastName varchar(255)
  * email (index) varchar(255)
  * date_of_birth date
  * phone varchar(255)
  * company (index) varchar(255)
  * website varchar(255)
  * address
     * nber varchar(255)
     * street varchar(255)
     * extra varchar(255)
     * pb varchar(255)
     * zipcode (index) varchar(6)
     * city (index) varchar(255)
     * country (index) varchar(255)
  * optin_newsletter boolean //opt-in newsletter (index)
  * created_at datetime
  * changed_at datetime

comment gérer plusieurs opt-in ? On ne gère que deux optin pour l'instant
comment gérer/configurer les civilités ? Via config.yml 

@see 
  form :
  	civility :
  	  01: Mr //Monsieur
  	  02: Ms //Mademoiselle
  	  03: Mrs //Madame
  	  04: Miss //Société
  	  05: Prof //Professeur
  	  06: Dr //Docteur
  	  07: Rev //Père (prêtrise) ?
  	  08: Sir //Noblesse

En base on stock la clef, en front on se sert de la valeur pour traduire

* Intent (Intention de don)
  * id (serial)
  * type : smallint type : 1 ponctuel, 2 Récurrent, (index)
  * amount : montant en centimes
  * currency : (index)
  * status : \[pending|done\] (index)
  * payment_method : string (index)
  * created_at datetime
  * changed_at datetime
  * customer int(11) Customer::id  many-to-one Customer
  * campaign (index) varchar(255) : champs prévisionnel pour segmenter les dons en fonctions de campagnes de com
  * erf : int



comment gérer les devises: On ne gère que l'EURO

Usage des status ?  
  * Un Intent ponctuel est pending tant qu'on a pas de post-sale puis devient done
  * Un Intent recurrent est pending tant qu'il n'est pas arrêté, par l'internaute ou via un pb d'expiration si c'est un paiement online
Prévoir l'implementation des affectations plus tards ? Oui

campaign? : ce champs est capturé en fonction de l'url ex http://soutenir.xxx.fr/?cid=8978934
dans un premier temps on va juste supporté un format précis
   ([a-zA-Z0-9\-_]{,8})

* Payment
  * id int(11)
  * intent int(11) Intent::id   many-to-one Order
  * status enum('invalid','canceled', 'authorized', 'denied', 'completed', 'failed')
  * response_code : code de réponse de la banque
  * transaction : Numéro de transaction de la banque
  * autorisation : Numéro d'autorisarion
  * response capture de la réponse complete
  * alias varchar 128
  * created_at  datetime
  * changed_at datetime

Le Formulaire à la validation crée/met à jour un Customer, créer un Intent
L'intetn est envoyé à l'API de paiement, le retour de l'api de paiement créer une Payment, fin de la transaction

Pour un paiement régulier :
Le Formulaire à la validation crée/met à jour un Customer, créer un Order indiquant la configuration du PA CB, une commande envoie mensuellement les commandes à l'API de paiement en batch et créer un Payment par réponses/mois
