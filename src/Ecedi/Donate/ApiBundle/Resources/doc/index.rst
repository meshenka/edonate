# Introduction

L'ApiBundle fourni des WebServices RESTful pour l'intégration des data relatives aux dons
dans le système d'information du client :
  * Reporting
  * Business Intelligence
  * eCRM
  * You name it

# Fonctionnalités et limitations

  * Securité : Le WebService passe sur le canal https
  * Securité : Le WebService utilise l'autentification Oauth pour controller qui accède à quoi
  * Format standard : Nativement les Webservices peuvent produire du Json ou de l'XML. Nous recommandons le Json qui est plus léger
  * Limitations : Pour ne pas surcharger le serveur nous vous recommandons de ne pas appeler les endpoints plus d'une fois par heures.
  * Data : Expose l'ensemble du data-model relative au dons


# Endpoints

Les Endpoints sont les points d'accès du WebService.
La grande majorité des endpoints sont en GET (lecture) uniquement.

## GET /api/v1/customers
Liste des donateurs, sans les intents & payments, par ordre de nouveauté
  * param : since (timestamp)
  * param : limit (int) 10 par défaut, 100 max
  * param : offset (int) 0 par défaut, si il y a plus de limit résultats c'est intéressant

Format de la réponse :
'''
{
	nbResults : "564",
	data : [
		'customerId' : {
			id : 4564,
			email : sgogel@ecedi.fr,
			...
		},
		...
	]
}
'''

## GET /api/v1/customer/{customerId}
Détail d'un donateurs, incluant les Intents
Format de la réponse :
'''
{
	id : 4564,
	email : sgogel@ecedi.fr,
	...
	intents : [
		'0' : {
			'id' : '65',
			'amount' : '5000',
			'currency' : 'EUR',
			'status' : 'done',
		}
	]
}
'''

## A prévoir également avec le remoteID


## UPDATE /api/v1/customer/{customerId}
Mise à jour des champs donateurs (remote_id, address etc...)

## GET /api/v1/intents
Liste des nouveaux dons, incluant micro information sur le donateur (id, lastname, firstname, email)

  * param : since (timestamp)
  * param : limit (int) 10 par défaut, 100 max
  * param : offset (int) 0 par défaut, si il y a plus de limit résultats c'est intéressant

## GET /api/v1/intent/{intentId} 
Détail d'un dons, avec liste des paiements

## GET /api/v1/payments

## GET /api/v1/payment/{paymentId}

# Oauth

@see http://www.youtube.com/watch?v=AcLHvOT5Ekg

@see http://blog.logicexception.com/2012/04/securing-syfmony2-rest-service-wiith.html

@see http://welcometothebundle.com/symfony2-rest-api-the-best-2013-way/
@see http://welcometothebundle.com/web-api-rest-with-symfony2-the-best-way-the-post-method/
@see http://welcometothebundle.com/symfony2-rest-api-the-best-way-part-3/

@see http://blog.tankist.de/blog/2013/07/16/oauth2-explained-part-1-principles-and-terminology/

@see http://aaronparecki.com/articles/2012/07/29/1/oauth2-simplified

## test the api


Avec l'utilitaire en ligne de commande CURL

### Créer un client oAuth

`̀``
$ app/console donate:api:client:create --redirect-uri="http://client.local/" --grant-type="authorization_code" --grant-type="password" --grant-type="refresh_token" --grant-type="token" --grant-type="client_credentials" client.local

 public id 5_3e5eh8pzc1k448s00k8sc0ocw4c0ss044w000s0sw484sswgc0 and secret 23hvk3qa07gg4cogo0c08csog444c4o4sk48wg4gkcgggw08w4 .

`̀``

### Obtenir un token

Le public id est utilisé comme client_id pour obtenir un token

```
curl -I  -H "Accept: application/xml"  http://donate.loc/app_dev.php/oauth/v2/token?client_id=5_3e5eh8pzc1k448s00k8sc0ocw4c0ss044w000s0sw484sswgc0&client_secret=23hvk3qa07gg4cogo0c08csog444c4o4sk48wg4gkcgggw08w4&grant_type=client_credentials
```

Ca réponds bien 
`̀``
{"access_token":"ZDgyYzM2Njk4MWU4ZDRlYWIxMzhhZjU3YmZkZWQ4M2ZmNzM1NTE1YTc1YmFhY2YwN2Q2ODg2YmUyNWJjN2VkOQ","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"MDA4MWI2MDc4OTRlYmY4ZjQ4YjQyNzdhNGJjYWJkNTdjNDRjODU1Y2JjZTQxMWJmNDc5NzFlNGJiNGYzYzkzNg"}
`̀``

### Faire une requete

Maintenant que j'ai un token je peux l'utiliser pour soliciter une resource REST

`̀``
curl -H "Accept: application/json"  "http://donate.loc/app_dev.php/api/v1/customers.xml?access_token=ZDgyYzM2Njk4MWU4ZDRlYWIxMzhhZjU3YmZkZWQ4M2ZmNzM1NTE1YTc1YmFhY2YwN2Q2ODg2YmUyNWJjN2VkOQ"
`̀``