<?php

namespace Tests\Chaplean\Bundle\MailerBundle\DependencyInjection;

use Chaplean\Bundle\MailerBundle\DependencyInjection\ChapleanMailerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $extension = new ChapleanMailerExtension();
        $containerBundler = new ContainerBuilder();

        $extension->load([[
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
        ]], $containerBundler);

        $this->assertTrue($containerBundler->hasDefinition('chaplean_mailer.event_listener.mail_logging'));
        $this->assertTrue($containerBundler->hasDefinition('chaplean_mailer.utility.email_configuration'));
        $this->assertTrue($containerBundler->hasDefinition('chaplean_mailer.utility.message_configuration'));

        $this->assertTrue($containerBundler->hasParameter('chaplean_mailer'));
        $this->assertEquals([
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
        ], $containerBundler->getParameter('chaplean_mailer'));
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\DependencyInjection\Configuration::getConfigTreeBuilder()
     *
     * @return void
     */
    public function testGetConfigTreeBuilderDefaultValue()
    {
        $extension = new ChapleanMailerExtension();
        $containerBundler = new ContainerBuilder();

        $extension->load(
            [
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
            ],
            $containerBundler
        );

        $this->assertEquals('', $containerBundler->getParameter('chaplean_mailer.subject.prefix'));
        $this->assertTrue($containerBundler->getParameter('chaplean_mailer.test'));
        $this->assertEquals([], $containerBundler->getParameter('chaplean_mailer.disabled_email_extensions'));
    }

    /**
     * @dataProvider requiredNodeConfiguration
     *
     * @covers       \Chaplean\Bundle\MailerBundle\DependencyInjection\Configuration::getConfigTreeBuilder()
     *
     * @param array  $config
     * @param string $exceptionMessage
     *
     * @return void
     */
    public function testGetConfigTreeBuilderRequiredNode(array $config, $exceptionMessage)
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $extension = new ChapleanMailerExtension();
        $containerBundler = new ContainerBuilder();

        $extension->load([$config], $containerBundler);
    }

    /**
     * @return array
     */
    public function requiredNodeConfiguration()
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
