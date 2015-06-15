<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ecedi\Donate\CoreBundle\Entity\Block;
use Ecedi\Donate\CoreBundle\Entity\Layout;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GenerateBlockCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('donate:generate:layout')
            ->setDescription('Generate default empty blocks for back office edition')
            ->setHelp(<<<EOF
The <info>donate:generate:layout</info> command generate default layout and block entities. It must be run before any back office access

<info>php app/console donate:generate:layout</info>
EOF
            );
    }
    /**
    * Exécution de la commande
    *
    *
    * @param InputInterface $input
    * @param OutputInterface $output
    */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $layoutFr = $this->createLayout('fr');
        $layoutEn = $this->createLayout('en');

        $em->persist($layoutFr);
        $em->persist($layoutEn);

        $em->flush();

        $output->writeln('<info>layouts generated</info>');
    }

    protected function createLayout($lang)
    {
        $layout = new Layout($lang, "default-$lang");
        $logoPath = $this->getContainer()->get('kernel')->getRootDir().'/../web/bundles/donatefront/images/logo.png';
        $bgPath = $this->getContainer()->get('kernel')->getRootDir().'/../web/bundles/donatefront/images/fd-body.jpg';

        $f = new File($logoPath);
        $logo = new UploadedFile($logoPath, 'ulogo.png', $f->getMimeType(), $f->getSize());

        $f = new File($bgPath);
        $bg = new UploadedFile($bgPath, 'ubg.png', $f->getMimeType(), $f->getSize());

        $layout->setLogo($logo);
        $layout->setBackground($bg);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('DonateCoreBundle:Layout');

        $defaultLayout = $repo->findDefaultLayout($lang);
        if (count($defaultLayout) == 0) {
            $layout->setIsDefault(true);
        }

        return $layout;
    }
}
