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

use Da2e\FiltrationBundle\Exception\Filter\Filter\InvalidArgumentException;
use Da2e\FiltrationBundle\Exception\Filter\Filter\UnexpectedValueException;
use Da2e\FiltrationBundle\Filter\Filter\AbstractTextFilter;

/**
 * Doctrine ORM text filter.
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class TextFilter extends AbstractTextFilter
{
    use DoctrineORMFilterTrait;

    // Match types (exact or like variations).
    const MATCH_TYPE_EXACT = 'exact';
    const MATCH_TYPE_WILDCARD_ANY = 'wildcard_any';
    const MATCH_TYPE_WILDCARD_STARTS_WITH = 'wildcard_starts_with';
    const MATCH_TYPE_WILDCARD_ENDS_WITH = 'wildcard_ends_with';

    /**
     * Default match type.
     *
     * @var string
     */
    protected $matchType = self::MATCH_TYPE_EXACT;

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
            ->andWhere(sprintf('%s %s :%s', $this->getFieldName(), $this->getComparisonOperator(), $this->getName()))
            ->setParameter($this->getName(), $this->getConditionValue());

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public static function getValidOptions()
    {
        return array_merge(parent::getValidOptions(), [
            'match_type' => [
                'setter' => 'setMatchType',
                'empty'  => false,
                'type'   => 'string',
            ],
        ]);
    }

    /**
     * Sets match type.
     *
     * @param string $matchType TextFilter::MATCH_TYPE_*
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public function setMatchType($matchType)
    {
        $validMatchTypes = [
            self::MATCH_TYPE_EXACT,
            self::MATCH_TYPE_WILDCARD_ANY,
            self::MATCH_TYPE_WILDCARD_STARTS_WITH,
            self::MATCH_TYPE_WILDCARD_ENDS_WITH,
        ];

        if (!is_string($matchType) || !in_array($matchType, $validMatchTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid match type. Allowed match types: [%s].', implode(', ', $validMatchTypes)
            ));
        }

        $this->matchType = $matchType;

        return $this;
    }

    /**
     * Gets match type.
     *
     * @return string TextFilter::MATCH_TYPE_*
     */
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * Gets comparison operator considering match type.
     *
     * @return string Comparison operator
     * @throws UnexpectedValueException On invalid match type
     */
    protected function getComparisonOperator()
    {
        switch ($this->getMatchType()) {
            case self::MATCH_TYPE_EXACT:
                return '=';

            case self::MATCH_TYPE_WILDCARD_ANY:
            case self::MATCH_TYPE_WILDCARD_STARTS_WITH:
            case self::MATCH_TYPE_WILDCARD_ENDS_WITH:
                return 'LIKE';

            default:
                throw new UnexpectedValueException('Invalid match type.');
        }
    }

    /**
     * Gets correct condition value considering match type.
     *
     * @return string
     * @throws UnexpectedValueException On invalid match type
     */
    protected function getConditionValue()
    {
        $value = $this->getConvertedValue();

        switch ($this->getMatchType()) {
            case self::MATCH_TYPE_EXACT:
                return $value;

            case self::MATCH_TYPE_WILDCARD_ANY:
                return sprintf('%%%s%%', $value);

            case self::MATCH_TYPE_WILDCARD_STARTS_WITH:
                return sprintf('%s%%', $value);

            case self::MATCH_TYPE_WILDCARD_ENDS_WITH:
                return sprintf('%%%s', $value);

            default:
                throw new UnexpectedValueException('Invalid match type.');
        }
    }
}
