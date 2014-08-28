<?php

namespace Ecedi\Donate\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ecedi\Donate\CoreBundle\DependencyInjection\Compiler\PaymentMethodCompilerPass;

class DonateCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PaymentMethodCompilerPass());
    }
}
