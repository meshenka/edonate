<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * on ajoute une passe de compilation pour découvrir automatiquement
 * les services taggé par donate.payment_method.
 * ps : on doit tagger les services qui implémente l'interface Ecedi\Donate\CoreBundle\Service\PaymentMethodInterface
 */
namespace Ecedi\Donate\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class PaymentMethodCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('donate_core.payment_method_discovery')) {
            return;
        }

        $definition = $container->getDefinition(
            'donate_core.payment_method_discovery'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'donate.payment_method'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addMethod',
                array(new Reference($id))
            );
        }
    }
}
