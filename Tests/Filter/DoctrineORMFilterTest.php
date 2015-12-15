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
use Da2e\FiltrationBundle\Filter\Filter\Doctrine\ORM\DoctrineORMFilterTrait;
use Da2e\FiltrationBundle\Tests\TestCase;

/**
 * Class DoctrineORMFilterTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class DoctrineORMFilterTest extends TestCase
{
    public function testGetType()
    {
        /** @var DoctrineORMFilterTrait $mock */
        $mock = $this->getMockForTrait('\Da2e\FiltrationDoctrineORMBundle\Filter\DoctrineORMFilterTrait');
        $this->assertSame('doctrine_orm', $mock->getType());
    }

    public function testCheckSphinxHandlerInstance()
    {
        /** @var DoctrineORMFilterTrait $mock */
        $mock = $this->getMockForTrait('\Da2e\FiltrationDoctrineORMBundle\Filter\DoctrineORMFilterTrait');
        $this->invokeMethod($mock, 'checkDoctrineORMHandlerInstance', [
            $this->getMock('\Doctrine\ORM\QueryBuilder', [], [], '', false)
        ]);
    }

    public function testCheckSphinxHandlerInstance_InvalidHandler()
    {
        $args = [
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
            '',
        ];

        $exceptionCount = 0;
        $mock = $this->getMockForTrait('\Da2e\FiltrationDoctrineORMBundle\Filter\DoctrineORMFilterTrait');

        foreach ($args as $arg) {
            try {
                $this->invokeMethod($mock, 'checkDoctrineORMHandlerInstance', [$arg]);
            } catch (InvalidArgumentException $e) {
                $exceptionCount++;
            }
        }

        $this->assertEquals(count($args), $exceptionCount);
    }
}
