<?php

namespace Ecedi\Donate\OgoneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ecedi\Donate\CoreBundle\Entity\Payment;
use Ecedi\Donate\CoreBundle\Event\DonateEvents;
use Ecedi\Donate\CoreBundle\Event\PaymentReceivedEvent;

class OgonePostsaleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('donate:ogone:postsale')
            ->setDescription('Do postsale handling when async mode is enabled')
            ->setHelp(<<<EOF
The <info>donate:ogone:postsale</info> find new, unhandled payment postsales and do validation etc...

<info>php app/console donate:ogone:postsale</info>
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
        $container = $this->getContainer();
        $async = $container->getParameter('donate_ogone.async_postsale');

        if ($async == true) {
            $output->writeln("Will process unhandled postsales");
            $em = $container->get('doctrine')->getManager();

            $paymentRepository = $em->getRepository('DonateCoreBundle:Payment');

            $payments = $paymentRepository->getNewPayments(100);

            $output->writeln('Found <info>'.count($payments).'</info> unhandled postsales');
            foreach ($payments as $payment) {
                $container->get('event_dispatcher')->dispatch(DonateEvents::PAYMENT_RECEIVED,  new PaymentReceivedEvent($payment));

                $em->persist($payment);
            }

            $em->flush();

            $output->writeln("<info>Done</info>");
        } else {
            $output->writeln('<error>Async postsale handling is disabled. Nothing done</error>');
        }

        return;
    }
}
