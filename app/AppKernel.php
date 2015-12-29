<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(), // or new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Cnerta\BreadcrumbBundle\CnertaBreadcrumbBundle(),

            new Vich\UploaderBundle\VichUploaderBundle(),

            new Trsteel\CkeditorBundle\TrsteelCkeditorBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),

            //Ecedi bundles
            new Ecedi\Donate\CoreBundle\DonateCoreBundle(),

            // new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            // new Ecedi\Donate\ApiBundle\DonateApiBundle(),

            new Ecedi\Donate\AdminBundle\DonateAdminBundle(),
            new Ecedi\Donate\FrontBundle\DonateFrontBundle(),
            new Ecedi\Donate\OgoneBundle\DonateOgoneBundle(),
            new Ecedi\Donate\CmBundle\DonateCmBundle(),
            new Ecedi\Donate\PaymentBundle\DonatePaymentBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
