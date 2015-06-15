<?php
/**
 * @author Alexandre Fayolle <alf@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCustomerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('donate:export:customer')
            ->setDescription('Command for generating a csv file of donators')
            ->setHelp(<<<EOF
The <info>donate:export</info> command generate a csv file containing donators (can be filtered by month).

<info>php app/console donate:export:customer</info>
EOF
            );
    }
    /**
    * Exécution de la commande
    *
    * @param InputInterface $input
    * @param OutputInterface $output
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $csvFolder = 'csv_export';
        $exportDate = date("d_m_Y");

        $query = $em->getRepository('DonateCoreBundle:Customer')->getCustomersListBy(false);
        // Appel du service pour formater les données du csv
        $exporter = $this->getContainer()->get('donate_admin.export.customer');
        $exporter->setExportQuery($query);
        $data = $exporter->getCsvContent();
        // Création du fichier
        $csvExportName = 'export_donator_'.$exportDate.'.csv';
        $handle = fopen($csvFolder.'/'.$csvExportName, 'w') or die('Cannot open file:  '.$csvExportName);
        fwrite($handle, $data) or die('Unable to write in: '.$csvFolder.'/'.$csvExportName);
        $output->writeln('The file "'.$csvExportName.'" has been created in '.$csvFolder.' folder');
    }
}
