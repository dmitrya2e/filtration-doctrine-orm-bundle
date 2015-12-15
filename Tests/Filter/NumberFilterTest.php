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
use Da2e\FiltrationBundle\Filter\Filter\Doctrine\ORM\NumberFilter;

/**
 * Class NumberFilterTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class NumberFilterTest extends DoctrineORMFilterTestCase
{
    public function testApplyFilter_Ranged()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->never())->method($this->anything());

        $filterMock = $this->getNumberFilterMock([
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

        $filterMock = $this->getNumberFilterMock([
            'checkDoctrineORMHandlerInstance',
            'applySingleFilter',
            'applyRangedFilter'
        ]);

        $filterMock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);
        $filterMock->expects($this->once())->method('applyRangedFilter')->with($handler);
        $filterMock->expects($this->never())->method('applySingleFilter')->with($handler);

        $filterMock->setFromValue(1);
        $filterMock->setToValue(2);
        $filterMock->applyFilter($handler);
    }

    public function testApplyFilter_Single()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->never())->method($this->anything());

        $filterMock = $this->getNumberFilterMock([
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

        $filterMock = $this->getNumberFilterMock([
            'checkDoctrineORMHandlerInstance',
            'applySingleFilter',
            'applyRangedFilter'
        ]);

        $filterMock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);
        $filterMock->expects($this->once())->method('applySingleFilter')->with($handler);
        $filterMock->expects($this->never())->method('applyRangedFilter')->with($handler);

        $filterMock->setSingle(true);
        $filterMock->setValue(123);
        $filterMock->applyFilter($handler);
    }

    public function testApplySingleFilter()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->once())->method('andWhere')->with('foo === :bar')->willReturn($handler);
        $handler->expects($this->once())->method('setParameter')->with('bar', 123)->willReturn($handler);
        $handler->expects($this->never())->method($this->logicalNot(
            $this->logicalOr(
                $this->matches('andWhere'),
                $this->matches('setParameter')
            )
        ));

        $filterMock = $this->getNumberFilterMock(['getComparisonOperatorForSingleField']);
        $filterMock->setFieldName('foo');
        $filterMock->setName('bar');
        $filterMock->setSingle(true);
        $filterMock->setSingleType(AbstractRangeOrSingleFilter::SINGLE_TYPE_EXACT);
        $filterMock->setValue(123);

        $filterMock->expects($this->any())->method('getComparisonOperatorForSingleField')
            ->with(AbstractRangeOrSingleFilter::SINGLE_TYPE_EXACT)
            ->willReturn('===');

        $this->invokeMethod($filterMock, 'applySingleFilter', [$handler]);
    }

    public function testApplyRangedFilter()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->at(0))->method('andWhere')->with('foo >>> :bar_min')->willReturn($handler);
        $handler->expects($this->at(1))->method('andWhere')->with('foo <<< :bar_max')->willReturn($handler);
        $handler->expects($this->at(2))->method('setParameter')->with('bar_min', 1)->willReturn($handler);
        $handler->expects($this->at(3))->method('setParameter')->with('bar_max', 2)->willReturn($handler);
        $handler->expects($this->never())->method($this->logicalNot(
            $this->logicalOr(
                $this->matches('andWhere'),
                $this->matches('setParameter')
            )
        ));

        $filterMock = $this->getNumberFilterMock(['getComparisonOperatorForRangedField']);
        $filterMock->setFieldName('foo');
        $filterMock->setName('bar');
        $filterMock->setFromPostfix('min');
        $filterMock->setToPostfix('max');
        $filterMock->setRangedFromType(AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER);
        $filterMock->setRangedToType(AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS);
        $filterMock->setFromValue(1);
        $filterMock->setToValue(2);

        $filterMock->expects($this->at(0))->method('getComparisonOperatorForRangedField')
            ->with(AbstractRangeOrSingleFilter::RANGED_FROM_TYPE_GREATER)
            ->willReturn('>>>');

        $filterMock->expects($this->at(1))->method('getComparisonOperatorForRangedField')
            ->with(AbstractRangeOrSingleFilter::RANGED_TO_TYPE_LESS)
            ->willReturn('<<<');

        $this->invokeMethod($filterMock, 'applyRangedFilter', [$handler]);
    }

    /**
     * Gets NumberFilter mock object.
     *
     * @param bool|array $methods
     * @param string     $name
     *
     * @return NumberFilter|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getNumberFilterMock($methods = false, $name = 'name')
    {
        return $this->getCustomMock(
            '\Da2e\FiltrationDoctrineORMBundle\Filter\NumberFilter',
            $methods,
            [$name]
        );
    }
}
