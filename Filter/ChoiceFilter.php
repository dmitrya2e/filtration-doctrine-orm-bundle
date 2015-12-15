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

use Da2e\FiltrationBundle\Filter\Filter\AbstractChoiceFilter;

/**
 * Doctrine ORM choice filter.
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class ChoiceFilter extends AbstractChoiceFilter
{
    use DoctrineORMFilterTrait;

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

        $queryBuilder
            ->andWhere(sprintf('%s IN (:%s)', $this->getFieldName(), $this->getName()))
            ->setParameter($this->getName(), $this->getConvertedValue());

        return $this;
    }
}
