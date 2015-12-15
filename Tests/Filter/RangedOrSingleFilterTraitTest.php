<?php

/*
 * This file is part of the Da2e FiltrationDoctrineORMBundle package.
 *
 * (c) Dmitry Abrosimov <abrosimovs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da2e\FiltrationDoctrineORMBundle\Tests\Filter;

use Da2e\FiltrationBundle\Exception\Filter\Filter\UnexpectedValueException;
use Da2e\FiltrationBundle\Filter\Filter\AbstractRangeOrSingleFilter;
use Da2e\FiltrationBundle\Filter\Filter\Doctrine\ORM\RangedOrSingleFilterTrait;
use Da2e\FiltrationBundle\Tests\TestCase;

/**
 * Class RangedOrSingleFilterTraitTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class RangedOrSingleFilterTraitTest extends TestCase
{
    public function testGetComparisonOperatorForSingleField()
    {
        /** @var RangedOrSingleFilterTrait $mock */
        $mock = $this->getMockForTrait('\Da2e\FiltrationDoctrineORMBundle\Filter\RangedOrSingleFilterTrait');

        $args = [
            AbstractRangeOrSingleFilter::SINGLE_TYPE_EXACT            => '=',
            AbstractRangeOrSingleFilter::SINGLE_TYPE_GREATER          => '>',
            AbstractRangeOrSingleFilter::SINGLE_TYPE_GREATER_OR_EQUAL => '>=',
            AbstractRangeOrSingleFilter::SINGLE_TYPE_LESS             => '<',
            AbstractRangeOrSingleFilter::SINGLE_TYPE_LESS_OR_EQUAL    => '<=',
        ];

        foreach ($args as $value => $assertion) {
            $this->assertSame($assertion, $this->invokeMethod($mock, 'getComparisonOperatorForSingleField', [$value]));
        }
    }

    public function testGetComparisonOperatorForSingleField_InvalidType()
    {
        /** @var RangedOrSingleFilterTrait $mock */
        $mock = $this->getMockForTrait('\Da2e\FiltrationDoctrineORMBundle\Filter\RangedOrSingleFilterTrait');

        $args = [
            '',
            'foobar',
            AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER,
            AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER_OR_EQUAL,
            AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS,
            AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS_OR_EQUAL,
            1,
            1.0,
            null,
            0,
            new \stdClass(),
            [],
            function () {
            },
            true,
            false,
        ];

        $exceptionCount = 0;

        foreach ($args as $arg) {
            try {
                $this->invokeMethod($mock, 'getComparisonOperatorForSingleField', [$arg]);
            } catch (UnexpectedValueException $e) {
                $exceptionCount++;
            }
        }

        $this->assertEquals(count($args), $exceptionCount);
    }

    public function testGetComparisonOperatorForRangedField()
    {
        /** @var RangedOrSingleFilterTrait $mock */
        $mock = $this->getMockForTrait('\Da2e\FiltrationDoctrineORMBundle\Filter\RangedOrSingleFilterTrait');

        $args = [
            AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER          => '>',
            AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER_OR_EQUAL => '>=',
            AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS               => '<',
            AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS_OR_EQUAL      => '<=',
        ];

        foreach ($args as $value => $assertion) {
            $this->assertSame($assertion, $this->invokeMethod($mock, 'getComparisonOperatorForRangedField', [$value]));
        }
    }

    public function testGetComparisonOperatorForRangedField_InvalidType()
    {
        /** @var RangedOrSingleFilterTrait $mock */
        $mock = $this->getMockForTrait('\Da2e\FiltrationDoctrineORMBundle\Filter\RangedOrSingleFilterTrait');

        $args = [
            '',
            'foobar',
            AbstractRangeOrSingleFilter::SINGLE_TYPE_EXACT,
            AbstractRangeOrSingleFilter::SINGLE_TYPE_GREATER,
            AbstractRangeOrSingleFilter::SINGLE_TYPE_GREATER_OR_EQUAL,
            AbstractRangeOrSingleFilter::SINGLE_TYPE_LESS,
            AbstractRangeOrSingleFilter::SINGLE_TYPE_LESS_OR_EQUAL,
            1,
            1.0,
            null,
            0,
            new \stdClass(),
            [],
            function () {
            },
            true,
            false,
        ];

        $exceptionCount = 0;

        foreach ($args as $arg) {
            try {
                $this->invokeMethod($mock, 'getComparisonOperatorForRangedField', [$arg]);
            } catch (UnexpectedValueException $e) {
                $exceptionCount++;
            }
        }

        $this->assertEquals(count($args), $exceptionCount);
    }
}
