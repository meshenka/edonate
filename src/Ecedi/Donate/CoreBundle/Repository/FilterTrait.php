<?php

namespace Ecedi\Donate\CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;

trait FilterTrait
{
    /**
     * Fonction applicant les filtres par défaut pour des filtres de type select, checkbox ...
     * à notre querybuilder.
     *
     * @param object $qb         -- Doctrine\ORM\QueryBuilder
     * @param string $tableAlias -- l'alias de la table
     * @param string $field      -- le champ cible
     * @param mixed  $value      -- la valeur a testé
     */
    public function inFilter(QueryBuilder $qb, $tableAlias, $field, $value)
    {
        if ($this->getClassMetadata()->hasField($field)) {
            $value = !is_array($value) ? $value : (array) $value;
            $qb->andWhere($qb->expr()->in($tableAlias.'.'.$field, ':'.$tableAlias.'_'.$field))
               ->setParameter($tableAlias.'_'.$field, $value);
        }
    }

    /**
     * Fonction applicant les filtres de type text
     * à notre querybuilder.
     *
     * @param object $qb         -- Doctrine\ORM\QueryBuilder
     * @param string $tableAlias -- l'alias de la table
     * @param string $field      -- le champ cible
     * @param mixed  $value      -- la valeur a testé
     */
    public function matchFilter(QueryBuilder $qb, $tableAlias, $field, $operator, $value)
    {
        $qb->andWhere($tableAlias.'.'.$field.' '.$operator.' :'.$tableAlias.'_'.$field);

        switch ($operator) {
            case 'LIKE':
                $qb->setParameter($tableAlias.'_'.$field, '%'.$value.'%');
                break;
            case '=':
                $qb->setParameter($tableAlias.'_'.$field, $value);
        }
    }

    /**
     * Fonction applicant les filtres de comparaison (date range, fourchette de montants)
     * à notre querybuilder.
     *
     * @param object $qb         -- Doctrine\ORM\QueryBuilder
     * @param string $tableAlias -- l'alias de la table
     * @param string $field      -- le champ cible
     * @param mixed  $value      -- la valeur a testé
     * @param mixed  $value2     -- la valeur2 (facultative)
     */
    public function rangeFilter(QueryBuilder $qb, $tableAlias, $field, $operator, $value, $value2 = false)
    {
        switch ($operator) {
            case '>=':
                $qb->andWhere($tableAlias.'.'.$field.' '.$operator.' :'.$tableAlias.'_'.$field)
                   ->setParameter($tableAlias.'_'.$field, $value);
                break;
            case 'BETWEEN':
                $qb->andWhere($qb->expr()->between(
                    $tableAlias.'.'.$field,
                    ':'.$tableAlias.'_'.$field,
                    ':'.$tableAlias.'_'.$field.'_2'
                ))
                   ->setParameter($tableAlias.'_'.$field, $value)
                   ->setParameter($tableAlias.'_'.$field.'_2', $value2);
                break;

            case '<=':
                $qb->andWhere($tableAlias.'.'.$field.' '.$operator.' :'.$tableAlias.'_'.$field)
                   ->setParameter($tableAlias.'_'.$field, $value);
                break;
        }
    }
}
