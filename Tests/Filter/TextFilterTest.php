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

use Da2e\FiltrationBundle\Exception\Filter\Filter\InvalidArgumentException;
use Da2e\FiltrationBundle\Filter\Filter\Doctrine\ORM\TextFilter;

/**
 * Class TextFilterTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class TextFilterTest extends DoctrineORMFilterTestCase
{
    public function testApplyFilter()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->never())->method($this->anything());

        $filterMock = $this->getTextFilterMock([
            'checkDoctrineORMHandlerInstance',
            'applySingleFilter',
            'applyRangedFilter'
        ]);

        $filterMock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);
        $filterMock->expects($this->never())->method('applySingleFilter')->with($handler);
        $filterMock->expects($this->never())->method('applyRangedFilter')->with($handler);

        $filterMock->setValue(null);
        $filterMock->applyFilter($handler);
    }

    public function testApplyFilter_HasAppliedValue()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->once())->method('andWhere')->with('foo === :bar')->willReturn($handler);
        $handler->expects($this->once())->method('setParameter')->with('bar', 'foobar')->willReturn($handler);
        $handler->expects($this->never())->method($this->logicalNot(
            $this->logicalOr(
                $this->matches('andWhere'),
                $this->matches('setParameter')
            )
        ));

        $filterMock = $this->getTextFilterMock([
            'checkDoctrineORMHandlerInstance',
            'getComparisonOperator',
            'getConditionValue'
        ]);

        $filterMock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);
        $filterMock->expects($this->any())->method('getComparisonOperator')->willReturn('===');
        $filterMock->expects($this->any())->method('getConditionValue')->willReturn('foobar');

        $filterMock->setFieldName('foo');
        $filterMock->setName('bar');
        $filterMock->setValue('test');
        $filterMock->applyFilter($handler);
    }

    public function testGetValidOptions()
    {
        $this->assertTrue(is_array(TextFilter::getValidOptions()));
        $this->assertSame(
            array_merge($this->getAbstractFilterValidOptions(), [
                'match_type' => [
                    'setter' => 'setMatchType',
                    'empty'  => false,
                    'type'   => 'string',
                ],
            ]),
            TextFilter::getValidOptions()
        );
    }

    public function testSetMatchType()
    {
        $filterMock = $this->getTextFilterMock();
        $args = [
            TextFilter::MATCH_TYPE_EXACT,
            TextFilter::MATCH_TYPE_WILDCARD_ANY,
            TextFilter::MATCH_TYPE_WILDCARD_STARTS_WITH,
            TextFilter::MATCH_TYPE_WILDCARD_ENDS_WITH,
        ];

        foreach ($args as $arg) {
            $filterMock->setMatchType($arg);
            $this->assertSame($arg, $filterMock->getMatchType());
        }
    }

    public function testSetMatchType_InvalidArgument()
    {
        $filterMock = $this->getTextFilterMock();
        $args = [
            '',
            'foobar',
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
                $filterMock->setMatchType($arg);
            } catch (InvalidArgumentException $e) {
                $exceptionCount++;
            }
        }

        $this->assertEquals(count($args), $exceptionCount);
    }

    public function testGetMatchType()
    {
        $filterMock = $this->getTextFilterMock();
        $this->assertSame(TextFilter::MATCH_TYPE_EXACT, $filterMock->getMatchType());
    }

    public function testGetComparisonOperator()
    {
        $filterMock = $this->getTextFilterMock();
        $args = [
            TextFilter::MATCH_TYPE_EXACT                => '=',
            TextFilter::MATCH_TYPE_WILDCARD_ANY         => 'LIKE',
            TextFilter::MATCH_TYPE_WILDCARD_STARTS_WITH => 'LIKE',
            TextFilter::MATCH_TYPE_WILDCARD_ENDS_WITH   => 'LIKE',
        ];

        foreach ($args as $arg => $assertion) {
            $filterMock->setMatchType($arg);
            $this->assertSame($assertion, $this->invokeMethod($filterMock, 'getComparisonOperator'));
        }
    }

    /**
     * @expectedException \Da2e\FiltrationBundle\Exception\Filter\Filter\UnexpectedValueException
     */
    public function testGetComparisonOperator_InvalidMatchType()
    {
        $filterMock = $this->getTextFilterMock(['getMatchType']);
        $filterMock->expects($this->any())->method('getMatchType')->willReturn('foobar');
        $this->invokeMethod($filterMock, 'getComparisonOperator');
    }

    public function testGetConditionValue()
    {
        $filterMock = $this->getTextFilterMock();
        $filterMock->setValue('foobar');

        $args = [
            TextFilter::MATCH_TYPE_EXACT                => 'foobar',
            TextFilter::MATCH_TYPE_WILDCARD_ANY         => '%foobar%',
            TextFilter::MATCH_TYPE_WILDCARD_STARTS_WITH => 'foobar%',
            TextFilter::MATCH_TYPE_WILDCARD_ENDS_WITH   => '%foobar',
        ];

        foreach ($args as $arg => $assertion) {
            $filterMock->setMatchType($arg);
            $this->assertSame($assertion, $this->invokeMethod($filterMock, 'getConditionValue'));
        }
    }

    /**
     * @expectedException \Da2e\FiltrationBundle\Exception\Filter\Filter\UnexpectedValueException
     */
    public function testGetConditionValue_InvalidMatchType()
    {
        $filterMock = $this->getTextFilterMock(['getMatchType']);
        $filterMock->expects($this->any())->method('getMatchType')->willReturn('foobar');
        $this->invokeMethod($filterMock, 'getConditionValue');
    }

    /**
     * Gets TextFilter mock object.
     *
     * @param bool|array $methods
     * @param string     $name
     *
     * @return TextFilter|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getTextFilterMock($methods = null, $name = 'name')
    {
        return $this->getCustomMock(
            '\Da2e\FiltrationDoctrineORMBundle\Filter\TextFilter',
            $methods,
            [$name]
        );
    }
}
