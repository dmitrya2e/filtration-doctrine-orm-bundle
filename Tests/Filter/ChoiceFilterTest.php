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

use Da2e\FiltrationBundle\Filter\Filter\Doctrine\ORM\ChoiceFilter;

/**
 * Class ChoiceFilterTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class ChoiceFilterTest extends DoctrineORMFilterTestCase
{
    public function testApplyFilter()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();

        /** @var ChoiceFilter|\PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getCustomMock('Da2e\FiltrationDoctrineORMBundle\Filter\ChoiceFilter', [
            'checkDoctrineORMHandlerInstance',
        ]);

        $mock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);

        $mock->setValue([]);
        $mock->applyFilter($handler);
    }

    public function testApplyFilter_HasAppliedValue()
    {
        $handler = $this->getDoctrineORMQueryBuilderMock();
        $handler->expects($this->once())->method('andWhere')->with('foo IN (:bar)')->willReturn($handler);
        $handler->expects($this->once())->method('setParameter')->with('bar', [1, 2, 3]);
        $handler->expects($this->never())->method($this->logicalNot(
            $this->logicalOr(
                $this->matches('andWhere'),
                $this->matches('setParameter')
            )
        ));

        /** @var ChoiceFilter|\PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getCustomMock('Da2e\FiltrationDoctrineORMBundle\Filter\ChoiceFilter', [
            'checkDoctrineORMHandlerInstance',
        ]);

        $mock->expects($this->once())->method('checkDoctrineORMHandlerInstance')->with($handler);

        $mock->setValue([1, 2, 3]);
        $mock->setFieldName('foo');
        $mock->setName('bar');
        $mock->applyFilter($handler);
    }
}
