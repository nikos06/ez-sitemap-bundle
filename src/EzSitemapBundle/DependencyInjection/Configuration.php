<?php

namespace Blend\EzSitemapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('blend_ez_sitemap');

        $rootNode
            ->children()
                ->arrayNode('allowed_content_types')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('allowed_sections')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('main_url')->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
