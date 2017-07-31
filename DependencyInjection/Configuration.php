<?php

namespace Chaplean\Bundle\MailerBundle\DependencyInjection;

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
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('chaplean_mailer');

        $rootNode
            ->children()
            ->scalarNode('bcc_address')->end()
            ->scalarNode('bounce_address')->end()
            ->scalarNode('sender_address')->isRequired()->end()
            ->scalarNode('sender_name')->isRequired()->end()
            ->arrayNode('subject')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('prefix')->defaultValue('')->end()
                    ->end()
                ->end()
            ->booleanNode('test')->defaultTrue()->end()
            ->arrayNode('amazon_tags')
                ->children()
                    ->scalarNode('configuration_set')->isRequired()->end()
                    ->scalarNode('project_name')->isRequired()->end()
                    ->scalarNode('env')->isRequired()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
