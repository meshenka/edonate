<?php
namespace Ecedi\Donate\CmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetCampaignMonitorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('donate:cm:reset')
            ->setDescription('Command reset enrolled customers.')
            ->setHelp(<<<EOF
The <info>donate:cm:reset</info> reinitialize customers enrollment.

Next run of <info>donate:cm:push</info> will re-enroll all opt-in customers

<info>php app/console donate:cm:reset</info>
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
        $em = $this->getContainer()->get('doctrine')->getManager();
        $customerRepository = $em->getRepository('DonateCoreBundle:Customer');
        $r = $customerRepository->resetCustomersoptinSynchronized();

        $output->writeln('Reset of enrolled customes <info>done</info>');
    }
}
