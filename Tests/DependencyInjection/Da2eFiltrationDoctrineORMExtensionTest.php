<?php

/*
 * This file is part of the Da2e FiltrationDoctrineORMBundle package.
 *
 * (c) Dmitry Abrosimov <abrosimovs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da2e\FiltrationDoctrineORMBundle\Tests\DependencyInjection;

use Da2e\FiltrationDoctrineORMBundle\DependencyInjection\Da2eFiltrationDoctrineORMExtension;
use Da2e\FiltrationBundle\Tests\TestCase;
use Da2e\FiltrationDoctrineORMBundle\Filter\DoctrineORMHandlerType;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Da2eFiltrationDoctrineORMExtensionTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class Da2eFiltrationDoctrineORMExtensionTest extends TestCase
{
    public function testLoad()
    {
        $containerBuilder = new ContainerBuilder();
        $extension = new Da2eFiltrationDoctrineORMExtension();

        $configs = [
            'da2e_filtration_doctrine_orm' => [
                'handler_class' => '\stdClass',
            ],
        ];

        $extension->load($configs, $containerBuilder);

        $result = $containerBuilder->getParameter('da2e.filtration.config.handler_types');
        $this->assertTrue(is_array($result));
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(DoctrineORMHandlerType::TYPE, $result);
        $this->assertSame('\stdClass', $result[DoctrineORMHandlerType::TYPE]);

        $enabledServices = [
            'da2e.filtration_doctrine_orm.filter.text_filter',
            'da2e.filtration_doctrine_orm.filter.number_filter',
            'da2e.filtration_doctrine_orm.filter.date_filter',
            'da2e.filtration_doctrine_orm.filter.choice_filter',
            'da2e.filtration_doctrine_orm.filter.entity_filter',
        ];

        foreach ($enabledServices as $service) {
            $this->assertTrue($containerBuilder->has($service));
        }
    }

    public function testLoad_DefaultHandlerClass()
    {
        $containerBuilder = new ContainerBuilder();
        $extension = new Da2eFiltrationDoctrineORMExtension();

        $extension->load([], $containerBuilder);

        $result = $containerBuilder->getParameter('da2e.filtration.config.handler_types');
        $this->assertTrue(is_array($result));
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(DoctrineORMHandlerType::TYPE, $result);
        $this->assertSame(DoctrineORMHandlerType::CLASS_NAME, $result[DoctrineORMHandlerType::TYPE]);
    }

    public function testLoad_ContainerHasOtherHandlerTypes()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('da2e.filtration.config.handler_types', ['foo' => 'bar']);

        $extension = new Da2eFiltrationDoctrineORMExtension();
        $extension->load([], $containerBuilder);

        $result = $containerBuilder->getParameter('da2e.filtration.config.handler_types');
        $this->assertTrue(is_array($result));
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('foo', $result);
        $this->assertArrayHasKey(DoctrineORMHandlerType::TYPE, $result);
        $this->assertSame('bar', $result['foo']);
        $this->assertSame(DoctrineORMHandlerType::CLASS_NAME, $result[DoctrineORMHandlerType::TYPE]);
    }
}
