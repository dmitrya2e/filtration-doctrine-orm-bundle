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

use Da2e\FiltrationBundle\Filter\Filter\AbstractRangeOrSingleFilter;
use Da2e\FiltrationBundle\Filter\Filter\Doctrine\ORM\DateFilter;

/**
 * Class DateFilterTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class DateFilterTest extends DoctrineORMFilterTestCase
{
    public function testApplyFilter_Ranged()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->never())->method($this->anything());

        $filterMock = $this->getDateFilterMock([
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

    public function testApplyFilter_Ranged_HasAppliedValue()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->never())->method($this->anything());

        $filterMock = $this->getDateFilterMock([
            'checkDoctrineORMHandlerInstance',
            'applySingleFilter',
            'applyRangedFilter'
        ]);

        $filterMock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);
        $filterMock->expects($this->once())->method('applyRangedFilter')->with($handler);
        $filterMock->expects($this->never())->method('applySingleFilter')->with($handler);

        $filterMock->setFromValue(new \DateTime());
        $filterMock->setToValue(new \DateTime());
        $filterMock->applyFilter($handler);
    }

    public function testApplyFilter_Single()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->never())->method($this->anything());

        $filterMock = $this->getDateFilterMock([
            'checkDoctrineORMHandlerInstance',
            'applySingleFilter',
            'applyRangedFilter'
        ]);

        $filterMock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);
        $filterMock->expects($this->never())->method('applySingleFilter')->with($handler);
        $filterMock->expects($this->never())->method('applyRangedFilter')->with($handler);

        $filterMock->setSingle(true);
        $filterMock->setValue(null);
        $filterMock->applyFilter($handler);
    }

    public function testApplyFilter_Single_HasAppliedValue()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->never())->method($this->anything());

        $filterMock = $this->getDateFilterMock([
            'checkDoctrineORMHandlerInstance',
            'applySingleFilter',
            'applyRangedFilter'
        ]);

        $filterMock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);
        $filterMock->expects($this->once())->method('applySingleFilter')->with($handler);
        $filterMock->expects($this->never())->method('applyRangedFilter')->with($handler);

        $filterMock->setSingle(true);
        $filterMock->setValue(new \DateTime());
        $filterMock->applyFilter($handler);
    }

    public function testApplySingleFilter()
    {
        $value = new \DateTime('2015-01-01 12:33:33');
        $valueParam = clone $value;
        $valueParam->setTime(0, 0, 0);

        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->once())->method('andWhere')->with('foo === :bar')->willReturn($handler);
        $handler->expects($this->once())->method('setParameter')->with('bar', $valueParam)->willReturn($handler);
        $handler->expects($this->never())->method($this->logicalNot(
            $this->logicalOr(
                $this->matches('andWhere'),
                $this->matches('setParameter')
            )
        ));

        $filterMock = $this->getDateFilterMock(['getComparisonOperatorForSingleField']);
        $filterMock->setFieldName('foo');
        $filterMock->setName('bar');
        $filterMock->setSingle(true);
        $filterMock->setSingleType(AbstractRangeOrSingleFilter::SINGLE_TYPE_EXACT);
        $filterMock->setValue($value);

        $filterMock->expects($this->any())->method('getComparisonOperatorForSingleField')
            ->with(AbstractRangeOrSingleFilter::SINGLE_TYPE_EXACT)
            ->willReturn('===');

        $this->invokeMethod($filterMock, 'applySingleFilter', [$handler]);
    }

    public function testApplyRangedFilter()
    {
        $fromValue = new \DateTime('2015-01-01 12:33:33');
        $fromValueParam = clone $fromValue;
        $fromValueParam->setTime(0, 0, 0);

        $toValue = new \DateTime('2016-01-01 12:33:33');
        $toValueParam = clone $toValue;
        $toValueParam->setTime(0, 0, 0);

        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->at(0))->method('andWhere')->with('foo >>> :bar_min')->willReturn($handler);
        $handler->expects($this->at(1))->method('andWhere')->with('foo <<< :bar_max')->willReturn($handler);
        $handler->expects($this->at(2))->method('setParameter')->with('bar_min', $fromValueParam)->willReturn($handler);
        $handler->expects($this->at(3))->method('setParameter')->with('bar_max', $toValueParam)->willReturn($handler);
        $handler->expects($this->never())->method($this->logicalNot(
            $this->logicalOr(
                $this->matches('andWhere'),
                $this->matches('setParameter')
            )
        ));

        $filterMock = $this->getDateFilterMock(['getComparisonOperatorForRangedField']);
        $filterMock->setFieldName('foo');
        $filterMock->setName('bar');
        $filterMock->setFromPostfix('min');
        $filterMock->setToPostfix('max');
        $filterMock->setRangedFromType(AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER);
        $filterMock->setRangedToType(AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS);
        $filterMock->setFromValue($fromValue);
        $filterMock->setToValue($toValue);

        $filterMock->expects($this->at(0))->method('getComparisonOperatorForRangedField')
            ->with(AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER)
            ->willReturn('>>>');

        $filterMock->expects($this->at(1))->method('getComparisonOperatorForRangedField')
            ->with(AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS)
            ->willReturn('<<<');

        $this->invokeMethod($filterMock, 'applyRangedFilter', [$handler]);
    }

    /**
     * Gets DateFilter mock object.
     *
     * @param bool|array $methods
     * @param string     $name
     *
     * @return DateFilter|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getDateFilterMock($methods = false, $name = 'name')
    {
        return $this->getCustomMock(
            '\Da2e\FiltrationDoctrineORMBundle\Filter\DateFilter',
            $methods,
            [$name]
        );
    }
}
