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

use Da2e\FiltrationBundle\Exception\Filter\Filter\UnexpectedValueException;
use Da2e\FiltrationBundle\Filter\Filter\AbstractRangeOrSingleFilter;

/**
 * Trait with helpful methods for ranged/single filters.
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
trait RangedOrSingleFilterTrait
{
    /**
     * Gets comparison operator for single field.
     *
     * @param string $type AbstractRangeOrSingleFilter::SINGLE_TYPE_*
     *
     * @return string Comparison operator [=|>|>=|<|<=]
     * @throws UnexpectedValueException On invalid single type
     */
    protected function getComparisonOperatorForSingleField($type)
    {
        if (!is_string($type)) {
            throw new UnexpectedValueException('Unexpected single field type.');
        }

        switch ($type) {
            case AbstractRangeOrSingleFilter::SINGLE_TYPE_EXACT:
                $comparisonOperator = '=';
                break;

            case AbstractRangeOrSingleFilter::SINGLE_TYPE_GREATER:
                $comparisonOperator = '>';
                break;

            case AbstractRangeOrSingleFilter::SINGLE_TYPE_GREATER_OR_EQUAL:
                $comparisonOperator = '>=';
                break;

            case AbstractRangeOrSingleFilter::SINGLE_TYPE_LESS:
                $comparisonOperator = '<';
                break;

            case AbstractRangeOrSingleFilter::SINGLE_TYPE_LESS_OR_EQUAL:
                $comparisonOperator = '<=';
                break;

            default:
                throw new UnexpectedValueException(sprintf(
                    'Unexpected single field type "%s".', $type
                ));
        }

        return $comparisonOperator;
    }

    /**
     * Gets comparison operator for ranged field.
     *
     * @param string $type AbstractRangeOrSingleFilter::RANGED_*_TYPE_*
     *
     * @return string Comparison operator [>|>=|<|<=]
     * @throws UnexpectedValueException On invalid range field type
     */
    protected function getComparisonOperatorForRangedField($type)
    {
        if (!is_string($type)) {
            throw new UnexpectedValueException('Unexpected single field type.');
        }

        switch ($type) {
            case AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER:
                $comparisonOperator = '>';
                break;

            case AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER_OR_EQUAL:
                $comparisonOperator = '>=';
                break;

            case AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS:
                $comparisonOperator = '<';
                break;

            case AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS_OR_EQUAL:
                $comparisonOperator = '<=';
                break;

            default:
                throw new UnexpectedValueException(sprintf(
                    'Unexpected ranged field type "%s".', $type
                ));
        }

        return $comparisonOperator;
    }
}
