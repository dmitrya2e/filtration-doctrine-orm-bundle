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

use Da2e\FiltrationDoctrineORMBundle\DependencyInjection\Configuration;
use Da2e\FiltrationBundle\Tests\TestCase;
use Da2e\FiltrationDoctrineORMBundle\Filter\DoctrineORMHandlerType;
use Symfony\Component\Config\Definition\ArrayNode;

/**
 * Class ConfigurationTest
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class ConfigurationTest extends TestCase
{
    public function testGetConfigurationTreeBuilder()
    {
        $configuration = new Configuration();

        $result = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf('\Symfony\Component\Config\Definition\Builder\TreeBuilder', $result);

        /** @var ArrayNode $tree */
        $tree = $result->buildTree();
        $this->assertInstanceOf('\Symfony\Component\Config\Definition\ArrayNode', $tree);
        $this->assertSame('da2e_filtration_doctrine_orm', $tree->getName());

        $children = $tree->getChildren();
        $this->assertTrue(is_array($children));
        $this->assertArrayHasKey('handler_class', $children);
        $this->assertCount(1, $children);

        $handlerClass = $children['handler_class'];
        $this->assertInstanceOf('\Symfony\Component\Config\Definition\ScalarNode', $handlerClass);
        $this->assertTrue($handlerClass->hasDefaultValue());
        $this->assertSame(DoctrineORMHandlerType::CLASS_NAME, $handlerClass->getDefaultValue());
    }
}
