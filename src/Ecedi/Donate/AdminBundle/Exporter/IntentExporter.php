<?php
/**
 * @author Alexandre Fayolle <alf@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\AdminBundle\Exporter;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Export.
 */
class IntentExporter
{
    private $name;

    private $charset;

    private $exportQb;

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Contact
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set charset.
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Get charset.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set QueryBuilder exportQb.
     *
     * @param string exportQb
     */
    public function setExportQb(QueryBuilder $exportQb)
    {
        $this->exportQb = $exportQb;

        return $this;
    }

    /**
     * Get QueryBuilder exportQb.
     *
     * @return exportQb
     */
    public function getExportQb()
    {
        return $this->exportQb;
    }

    public function getCsvContent()
    {
        $threeMBs = 3 * 1024 * 1024;
        $handle = fopen("php://temp/maxmemory:$threeMBs", 'r+');

        // En-tête du csv
        $csvHeader = array(
            'id don',
            'Montant',
            'Devise',
            utf8_decode('Créé le'),
            'Statut',
            'Type de don',
            utf8_decode('Méthode de paiement'),
            utf8_decode('Reçu fiscal'),
            'Campagne',
            'Affectation',
            utf8_decode('Civilité'),
            'Nom',
            utf8_decode('Prénom'),
            'id distant(ogone ...)',
            utf8_decode('Société'),
            'Date de naissance',
            'Email',
            utf8_decode('Téléphone'),
            utf8_decode('N°'),
            'rue, voirie...',
            utf8_decode("Complément d'adresse"),
            'Boite postale',
            'Code postal',
            'Ville',
            'Pays',
            'Site web',
        );

        fputcsv($handle, $csvHeader, ';');

        $exportQb = $this->getExportQb();
        $nbIntents = $exportQb->select('COUNT(i.id)')
                              ->getQuery()
                              ->getSingleScalarResult();

        /* OPTIMISATION 1 -- Séquençage de la requête pour optimiser la consommation mémoire php
        *
        * Si vous modifiez le nombre de résultats demandés par requête ($nbResultsRequested), vous devez
        * vérifier que vous disposez de suffisamment de mémoire pour réaliser l'export
        *
        */
        $nbResultsRequested = 500;
        $iMax = ceil($nbIntents / $nbResultsRequested); // Calcul du nombre de requêtes à réaliser

        for ($i = 0; $i <= $iMax; ++$i) {
            $exportQb->select('i, c') // cf repository pour les alias, on récupère les infos des intents et des customers
                     ->setFirstResult($i * $nbResultsRequested)
                     ->setMaxResults($nbResultsRequested);

            // OPTIMISATION 2 -- le résultat est un tableau d'array plutot que tableau d'objet
            // !!! NE PAS MODIFIER, c'est très consommateur de ressources
            $results = $exportQb->getQuery()->getResult(Query::HYDRATE_ARRAY);

            foreach ($results as $intent) {
                $status = $this->translator->trans($intent['status']);
                $type = ($intent['type'] == 0) ? 'ponctuel' : 'récurrent';
                $fiscalreceipt = ($intent['fiscal_receipt'] == 0) ? 'email' : 'courrier';

                $fieldsValue = [
                    utf8_decode($intent['id']),
                    utf8_decode($intent['amount']),
                    utf8_decode($intent['currency']),
                    utf8_decode($intent['createdAt']->format('d/m/Y')),
                    utf8_decode($status),
                    utf8_decode($type),
                    utf8_decode($intent['paymentMethod']),
                    utf8_decode($fiscalreceipt),
                    utf8_decode($intent['campaign']),
                    isset($intent['affectationCode']) ? utf8_decode($intent['affectationCode']) : '',
                    isset($intent['customer']['civility']) ? utf8_decode($intent['customer']['civility']) : '',
                    isset($intent['customer']['firstName']) ? utf8_decode($intent['customer']['firstName']) : '',
                    isset($intent['customer']['lastName']) ? utf8_decode($intent['customer']['lastName']) : '',
                    isset($intent['customer']['remoteId']) ? utf8_decode($intent['customer']['remoteId']) : '',
                    isset($intent['customer']['company']) ? utf8_decode($intent['customer']['company']) : '',
                    isset($intent['customer']['birthday']) ? utf8_decode($intent['customer']['birthday']->format('d/m/Y')) : '',
                    isset($intent['customer']['email']) ? utf8_decode($intent['customer']['email']) : '',
                    isset($intent['customer']['phone']) ? utf8_decode('#'.$intent['customer']['phone']) : '',
                    isset($intent['customer']['addressNber']) ? utf8_decode($intent['customer']['addressNber']) : '',
                    isset($intent['customer']['addressStreet']) ? utf8_decode($intent['customer']['addressStreet']) : '',
                    isset($intent['customer']['addressExtra']) ? utf8_decode($intent['customer']['addressExtra']) : '',
                    isset($intent['customer']['addressPb']) ? utf8_decode($intent['customer']['addressPb']) : '',
                    isset($intent['customer']['addressZipcode']) ? utf8_decode($intent['customer']['addressZipcode']) : '',
                    isset($intent['customer']['addressCity']) ? utf8_decode($intent['customer']['addressCity']) : '',
                    isset($intent['customer']['addressCountry']) ? utf8_decode($intent['customer']['addressCountry']) : '',
                    isset($intent['customer']['website']) ? utf8_decode($intent['customer']['website']) : '',
                ];

                fputcsv($handle, $fieldsValue, ';');
            }
        }
        /*print memory_get_usage()/(1024).'Ko pour '.$nbResultsRequested;
        die;*/
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }
}
