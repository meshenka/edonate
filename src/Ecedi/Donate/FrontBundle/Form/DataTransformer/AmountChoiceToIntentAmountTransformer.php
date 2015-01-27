<?php
namespace Ecedi\Donate\FrontBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AmountChoiceToIntentAmountTransformer implements DataTransformerInterface
{
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    /**
     * Duplicates the given value through the array.
     *
     * @param mixed $value The value
     *
     * @return array The array
     */
    public function transform($value)
    {
        $result = array();

        foreach ($this->keys as $key) {
            $result[$key] = $value;
        }

        return $result;
    }

     /**
     * Extrait le montant de don de notre champ custom AmountType
     *
     * @param  array $array
     * @return mixed The value
     */
    public function reverseTransform($array)
    {
        if (!is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }
        $donationAmount = [];

        foreach ($this->keys as $key) {
            if ($array[$key] != 'manual') {
                $donationAmount[] = $array[$key]; // sans js, nous prenons la valeur supérieure (si 2 valeures ont ete renseignées)
            }
        }

        return max($donationAmount) * 100;
    }
}
