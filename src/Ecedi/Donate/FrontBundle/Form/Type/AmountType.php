<?php
namespace Ecedi\Donate\FrontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Translation\TranslatorInterface;


use Ecedi\Donate\FrontBundle\Form\DataTransformer\AmountChoiceToIntentAmountTransformer;

class AmountType extends AbstractType
{   
    private $translator;
       
    public function __construct(TranslatorInterface $translator) 
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
       
        // Ajout d'un champ de saisi manuel si voulu
		$options['choices']['manual'] = $this->translator->trans('Other amount');	
	    $builder
	    	->addViewTransformer(new AmountChoiceToIntentAmountTransformer([
	    		'manual',	
	    		'preselected',	
	    	]))
	    	->add('preselected', 'choice', [
				'choices'   => $options['choices'],
				'required'  => false,
				'expanded' 	=> true,
				'multiple' 	=> false,
				'label' 	=> false,
				'data' 		=> $options['default'],
			])
			->add('manual', 'money', [
				'currency' 	=> 'EUR',
				'required'  => false,
				'label' 	=> false,
				'precision' => 0,
				'constraints' => [
					new Assert\Range(
						[
						  'min' 		=> $options['min_amount'],
						  'max' 		=> $options['max_amount'],
						  'minMessage' 	=> $this->translator->trans('Amount must be greater than %amount% €', ['%amount%' => $options['min_amount']], 'validation'),
						  'maxMessage' 	=> $this->translator->trans('Amount must be lower than  %amount% €', [ '%amount%' => $options['max_amount']], 'validation'),
						]
					)
				]
			]
		);
    }  
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {       
        $resolver->setDefaults([
		    'choices'   		=> [],
		    'min_amount' 		=> 5,
		    'max_amount' 		=> 4000,
		    'default' 			=> ''
		]);    
    }

    public function getName()
    {
        return 'amount_selector';
    }
}
