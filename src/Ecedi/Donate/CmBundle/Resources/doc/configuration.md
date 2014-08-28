Le bundle nécessite la configuration suivante

dans app/config/ecollecte.yml

``
donate_cm:
    api_key: %campaign_monitor_api_key%
    list_id: %campaign_monitor_list_id%
    lot_import_nb_max: %campaign_monitor_lot_import_nb_max%
    custom_fields:
        getter:
            cm_custom_field_name: 'nom du champ dans CM'
            options: (facultatif)
                valeur dans BDD: 'valeur équivalente dans CM'
                valeur dans BDD: 'valeur équivalente dans CM'
                ...
        getter:
            cm_custom_field_name: 'nom du champ dans CM'
            options:
                valeur dans BDD: 'valeur équivalente dans CM'
                valeur dans BDD: 'valeur équivalente dans CM'
                ...
        getter:
            cm_custom_field_name: 'nom du champ dans CM'
            options:
                valeur dans BDD: 'valeur équivalente dans CM'
                valeur dans BDD: 'valeur équivalente dans CM'
                ...

exemples :
        getFirstname:
            cm_custom_field_name: 'prénom'
        getCivility:
            cm_custom_field_name: 'civilité'
            options:
                mrs: 'Madame'
                mr: 'Monsieur'
        ....
``

Il faut configurer l'api_key et list_id dans le fichier app/config/parameters.yml
L'id list de campaign monitor s'obtient en cliquant sur " (change name/type) " dans le BO cm.ecedi.fr.
Il ne s'agit pas du LIST_ID présent dans l'URL.

Par défaut api_key et list_id vallent FALSE, ce qui indique que rien ne sera fait lors de lancement de la commande