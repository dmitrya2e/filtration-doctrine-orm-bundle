<?php

/*
 * This file is part of the Da2e FiltrationDoctrineORMBundle package.
 *
 * (c) Dmitry Abrosimov <abrosimovs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da2e\FiltrationDoctrineORMBundle\Filter;

use Da2e\FiltrationBundle\Filter\Filter\AbstractDateFilter;
use Doctrine\ORM\QueryBuilder;

/**
 * Doctrine ORM date (without time) filter.
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class DateFilter extends AbstractDateFilter
{
    use DoctrineORMFilterTrait;
    use RangedOrSingleFilterTrait;

    /**
     * {@inheritDoc}
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function applyFilter($queryBuilder)
    {
        $this->checkDoctrineORMHandlerInstance($queryBuilder);

        if ($this->hasAppliedValue() === false) {
            return $this;
        }

        if ($this->isSingle() === true) {
            return $this->applySingleFilter($queryBuilder);
        }

        return $this->applyRangedFilter($queryBuilder);
    }

    /**
     * Applies single filter.
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return static
     */
    protected function applySingleFilter(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere(sprintf(
                '%s %s :%s',
                $this->getFieldName(),
                $this->getComparisonOperatorForSingleField($this->getSingleType()),
                $this->getName()
            ))
            ->setParameter($this->getName(), $this->getConvertedValue());

        return $this;
    }

    /**
     * Applies ranged filter.
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return static
     */
    protected function applyRangedFilter(QueryBuilder $queryBuilder)
    {
        $fromValue = $this->getConvertedFromValue();
        $toValue = $this->getConvertedToValue();

        $fromComparisonOperator = $this->getComparisonOperatorForRangedField($this->getRangedFromType());
        $toComparisonOperator = $this->getComparisonOperatorForRangedField($this->getRangedToType());
        $fromValueParam = sprintf('%s_%s', $this->getName(), $this->getFromPostfix());
        $toValueParam = sprintf('%s_%s', $this->getName(), $this->getToPostfix());

        $queryBuilder
            ->andWhere(sprintf(
                '%s %s :%s', $this->getFieldName(), $fromComparisonOperator, $fromValueParam
            ))
            ->andWhere(sprintf(
                '%s %s :%s', $this->getFieldName(), $toComparisonOperator, $toValueParam
            ))
            ->setParameter($fromValueParam, $fromValue)
            ->setParameter($toValueParam, $toValue);

        return $this;
    }
}
