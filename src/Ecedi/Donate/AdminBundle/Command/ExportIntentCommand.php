<?php
namespace Ecedi\Donate\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ecedi\Donate\CoreBundle\Repository\Intent;

class ExportIntentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('donate:export:intent')
            ->setDescription('Command for generating a csv file of intents')
            ->addArgument('month', InputArgument::OPTIONAL, 'A specific month in a year "2011-01", or 3 months ago type "-3"')
            ->setHelp(<<<EOF
The <info>donate:export:intent</info> command generate a csv file containing intents (can be filtered by month).

<info>php app/console donate:export:intent 0</info> export intents of the current month
<info>php app/console donate:export:intent 3</info> export intents 3 months ago
<info>php app/console donate:export:intent 2011-01</info> export intents of january 2011

Use it without any options to export all intents.
<info>php app/console donate:export:intent</info>
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
        $csvFolder = 'csv_export';
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        // paramètres par défaut
        $parameters = array(
            'minCreatedAt' => '01/01/2000',
            'maxCreatedAt' => date('d/m/Y'),
        );
        $exportDate = date("d_m_Y");

        $month = $input->getArgument('month');

        if (isset($month)) {
            if (($month <= 12) && ($month >= 0)) {
                $stamp = strtotime('-'.$month.' month');
                $parameters = array(
                    'minCreatedAt' => date('01/m/Y', $stamp),
                    'maxCreatedAt' => date('t/m/Y', $stamp),
                );
                $exportDate = date('m_Y', $stamp);
            } else {
                $dateArray = explode('-', $month);

                $parameters = array(
                    'minCreatedAt' => '01/'.$dateArray[1].'/'.$dateArray[0],
                    'maxCreatedAt' => date('t/'.$dateArray[1].'/'.$dateArray[0]),
                );
                $exportDate = $dateArray[0].'_'.$dateArray[1];
            }
        }

        $qb = $em->getRepository('DonateCoreBundle:Intent')->getQBIntentsListBy($parameters);
        // Appel du service pour formater les données du csv
        $exporter = $this->getContainer()->get('donate_admin.export.intent');
        $exporter->setExportQb($qb);
        $data = $exporter->getCsvContent();
        // Création du fichier
        $csvExportName = 'export_intent_'.$exportDate.'.csv';
        $handle = fopen($csvFolder.'/'.$csvExportName, 'w') or die('Cannot open file:  '.$csvExportName);
        fwrite($handle, $data) or die('Unable to write in: '.$csvFolder.'/'.$csvExportName);
        $output->writeln('The file "'.$csvExportName.'" has been created in '.$csvFolder.' folder');
    }
}
