<?php
/**
 * @author Alexandre Fayolle <alf@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\AdminBundle\Exporter;

/**
 * Export
 *
 */
class CustomerExporter
{
    protected $name;

    protected $charset;

    protected $exportQuery;

    /**
     * Set name
     *
     * @param  string  $name
     * @return Contact
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set charset
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Get charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set query
     *
     * @param string exportQuery
     */
    public function setExportQuery($exportQuery)
    {
        $this->exportQuery = $exportQuery;

        return $this;
    }

    /**
     * Get exportQuery
     *
     * @return exportQuery
     */
    public function getExportQuery()
    {
        return $this->exportQuery;
    }

    public function getCsvContent()
    {
        $iterableResult = $this->getExportQuery()->iterate();

        $threeMBs = 3 * 1024 * 1024;
        $handle = fopen("php://temp/maxmemory:$threeMBs", 'r+');

        $csvHeader = array(utf8_decode("Civilité"),"Nom",utf8_decode("Prénom"),"id distant(ogone ...)",utf8_decode("Société"),"Date de naissance","Email",utf8_decode("Téléphone"),utf8_decode("N°"),"rue, voirie...",utf8_decode("Complément d'adresse"),"Boite postale","Code postal","Ville","Pays","Site web");

        fputcsv($handle, $csvHeader, ';');

        while (false !== ($row = $iterableResult->next())) {
            $birthDay = "";
            if (!empty($row[0]->getBirthday())) {
                $birthDay = $row[0]->getBirthday()->format('d/m/Y');
            }

            $fieldsValue = [
                utf8_decode($row[0]->getCivility()),
                utf8_decode($row[0]->getLastName()),
                utf8_decode($row[0]->getFirstName()),
                utf8_decode($row[0]->getRemoteId()),
                utf8_decode($row[0]->getCompany()),
                utf8_decode($birthDay),
                utf8_decode($row[0]->getEmail()),
                utf8_decode('#'.$row[0]->getPhone()),
                utf8_decode($row[0]->getAddressNber()),
                utf8_decode($row[0]->getAddressStreet()),
                utf8_decode($row[0]->getAddressExtra()),
                utf8_decode($row[0]->getAddressPb()),
                utf8_decode($row[0]->getAddressZipcode()),
                utf8_decode($row[0]->getAddressCity()),
                utf8_decode($row[0]->getAddressCountry()),
                utf8_decode($row[0]->getWebsite()),
            ];

            fputcsv($handle, $fieldsValue, ';');
            $this->getExportQuery()->getEntityManager()->detach($row[0]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }
}
