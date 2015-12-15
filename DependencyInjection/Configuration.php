<?php

/*
 * This file is part of the Da2e FiltrationDoctrineORMBundle package.
 *
 * (c) Dmitry Abrosimov <abrosimovs@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da2e\FiltrationDoctrineORMBundle\DependencyInjection;

use Da2e\FiltrationDoctrineORMBundle\Filter\DoctrineORMHandlerType;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains available configuration for the bundle.
 *
 * @author Dmitry Abrosimov <abrosimovs@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('da2e_filtration_doctrine_orm')
            ->children()
                ->scalarNode('handler_class')
                    ->defaultValue(DoctrineORMHandlerType::CLASS_NAME)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
