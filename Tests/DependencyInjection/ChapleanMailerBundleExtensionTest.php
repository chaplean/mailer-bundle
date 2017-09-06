<?php

namespace Tests\Chaplean\Bundle\MailerBundle\DependencyInjection;

use Chaplean\Bundle\MailerBundle\DependencyInjection\ChapleanMailerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ChapleanMailerBundleExtensionTest.
 *
 * @package   Tests\Chaplean\Bundle\MailerBundle\DependencyInjection
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     3.4.0
 */
class ChapleanMailerBundleExtensionTest extends TestCase
{
    /**
     * @covers \Chaplean\Bundle\MailerBundle\DependencyInjection\ChapleanMailerExtension::load()
     * @covers \Chaplean\Bundle\MailerBundle\DependencyInjection\ChapleanMailerExtension::setParameters()
     *
     * @return void
     */
    public function testLoad()
    {
        $extension = new ChapleanMailerExtension();
        $containerBuilder = new ContainerBuilder();

        $extension->load(
            [
                [
                    'bcc_address'    => null,
                    'bounce_address' => null,
                    'sender_address' => null,
                    'sender_name'    => null,
                    'subject'        => [
                        'prefix' => null,
                    ],
                    'test'           => false,
                    'amazon_tags'    => [
                        'configuration_set' => null,
                        'project_name'      => null,
                        'env'               => null,
                    ]
                ]
            ],
            $containerBuilder
        );

        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.bcc_address'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.bounce_address'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.sender_address'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.sender_name'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.subject'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.subject.prefix'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.test'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.amazon_tags'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.amazon_tags.configuration_set'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.amazon_tags.project_name'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.amazon_tags.env'));
        $this->assertTrue($containerBuilder->hasParameter('chaplean_mailer.disabled_email_extensions'));
    }
}
