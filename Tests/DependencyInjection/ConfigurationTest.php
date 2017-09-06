<?php

namespace Tests\Chaplean\Bundle\MailerBundle\DependencyInjection;

use Chaplean\Bundle\MailerBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class ConfigurationTest.
 *
 * @package   Tests\Chaplean\Bundle\MailerBundle\DependencyInjection
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     3.4.0
 */
class ConfigurationTest extends TestCase
{
    /**
     * @covers \Chaplean\Bundle\MailerBundle\DependencyInjection\Configuration::getConfigTreeBuilder()
     *
     * @return void
     */
    public function testGetConfigTreeBuilderFullyInformed()
    {
        $config = new Configuration();

        $configTree = $config->getConfigTreeBuilder()->buildTree();

        $normalized = $configTree->normalize(
            [
                'bcc_address'               => null,
                'bounce_address'            => null,
                'sender_address'            => null,
                'sender_name'               => null,
                'subject'                   => [
                    'prefix' => null,
                ],
                'test'                      => false,
                'amazon_tags'               => [
                    'configuration_set' => null,
                    'project_name'      => null,
                    'env'               => null,
                ],
                'disabled_email_extensions' => [
                    'foo.com',
                    'bar.com',
                ]
            ]
        );
        $finalized = $configTree->finalize($normalized);

        $this->assertEquals(
            [
                'bcc_address'               => null,
                'bounce_address'            => null,
                'sender_address'            => null,
                'sender_name'               => null,
                'subject'                   => [
                    'prefix' => null,
                ],
                'test'                      => false,
                'amazon_tags'               => [
                    'configuration_set' => null,
                    'project_name'      => null,
                    'env'               => null,
                ],
                'disabled_email_extensions' => [
                    'foo.com',
                    'bar.com',
                ]
            ],
            $finalized
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\DependencyInjection\Configuration::getConfigTreeBuilder()
     *
     * @return void
     */
    public function testGetConfigTreeBuilderDefaultValue()
    {
        $config = new Configuration();

        $configTree = $config->getConfigTreeBuilder()->buildTree();

        $normalized = $configTree->normalize(
            [
                'bcc_address'    => null,
                'bounce_address' => null,
                'sender_address' => null,
                'sender_name'    => null,
                'amazon_tags'    => [
                    'configuration_set' => null,
                    'project_name'      => null,
                    'env'               => null,
                ]
            ]
        );
        $finalized = $configTree->finalize($normalized);

        $this->assertEquals(
            [
                'bcc_address'               => null,
                'bounce_address'            => null,
                'sender_address'            => null,
                'sender_name'               => null,
                'subject'                   => [
                    'prefix' => '',
                ],
                'test'                      => true,
                'amazon_tags'               => [
                    'configuration_set' => null,
                    'project_name'      => null,
                    'env'               => null,
                ],
                'disabled_email_extensions' => []
            ],
            $finalized
        );
    }

    /**
     * @dataProvider requiredNodeConfuguration
     *
     * @covers       \Chaplean\Bundle\MailerBundle\DependencyInjection\Configuration::getConfigTreeBuilder()
     *
     * @param array  $inputConfig
     * @param string $exceptionMessage
     *
     * @return void
     */
    public function testtestGetConfigTreeBuilderRequiredNode(array $inputConfig, $exceptionMessage)
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $config = new Configuration();

        $configTree = $config->getConfigTreeBuilder()->buildTree();

        $normalized = $configTree->normalize($inputConfig);
        $configTree->finalize($normalized);
    }

    /**
     * @return array
     */
    public function requiredNodeConfuguration()
    {
        return [
            'sender_address'    => [
                [],
                'The child node "sender_address" at path "chaplean_mailer" must be configured.'
            ],
            'sender_name'       => [
                [
                    'sender_address' => '',
                ],
                'The child node "sender_name" at path "chaplean_mailer" must be configured.'
            ],
            'configuration_set' => [
                [
                    'sender_address' => '',
                    'sender_name'    => '',
                    'amazon_tags'    => []
                ],
                'The child node "configuration_set" at path "chaplean_mailer.amazon_tags" must be configured.'
            ],
            'project_name'      => [
                [
                    'sender_address' => '',
                    'sender_name'    => '',
                    'amazon_tags'    => [
                        'configuration_set' => ''
                    ]
                ],
                'The child node "project_name" at path "chaplean_mailer.amazon_tags" must be configured.'
            ],
            'env'               => [
                [
                    'sender_address' => '',
                    'sender_name'    => '',
                    'amazon_tags'    => [
                        'configuration_set' => '',
                        'project_name'      => '',
                    ]
                ],
                'The child node "env" at path "chaplean_mailer.amazon_tags" must be configured.'
            ],
        ];
    }
}
