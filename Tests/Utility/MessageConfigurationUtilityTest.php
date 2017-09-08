<?php

namespace Tests\Chaplean\Bundle\MailerBundle\Utility;

use Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class MessageConfigurationUtilityTest.
 *
 * @package   Tests\Chaplean\Bundle\MailerBundle\Utility
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     4.0.0
 */
class MessageConfigurationUtilityTest extends MockeryTestCase
{
    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyAmazonTags
     *
     * @return void
     */
    public function testApplyAmazonTagsMissingConfig()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([]);
        $message = new \Swift_Message();
        $headers = $message->getHeaders();

        $messageConfigurationUtility->applyAmazonTags($message);

        $this->assertNull($headers->get('X-SES-CONFIGURATION-SET'));
        $this->assertNull($headers->get('X-SES-MESSAGE-TAGS'));
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyAmazonTags
     *
     * @return void
     */
    public function testApplyAmazonTags()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'amazon_tags' => [
                'configuration_set' => 'A',
                'project_name'      => 'B',
                'env'               => 'C',
            ]
        ]);

        $message = new \Swift_Message();
        $headers = $message->getHeaders();

        $messageConfigurationUtility->applyAmazonTags($message);

        $this->assertEquals("X-SES-CONFIGURATION-SET: A\r\n", $headers->get('X-SES-CONFIGURATION-SET')->toString());
        $this->assertEquals("X-SES-MESSAGE-TAGS: project_name=B, environment=C\r\n", $headers->get('X-SES-MESSAGE-TAGS')->toString());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyBccAddress()
     *
     * @return void
     */
    public function testApplyBccAddress()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'bcc_address' => 'foo@bar.com'
        ]);

        $message = new \Swift_Message();

        $messageConfigurationUtility->applyBccAddress($message);

        $this->assertEquals(['foo@bar.com' => null], $message->getBcc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyBccAddress()
     *
     * @return void
     */
    public function testApplyBccAddressWithNull()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'bcc_address' => null
        ]);

        $message = new \Swift_Message();

        $messageConfigurationUtility->applyBccAddress($message);

        $this->assertEmpty($message->getBcc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyBounceAddress()
     *
     * @return void
     */
    public function testApplyBounceAddress()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'bounce_address' => 'foo@bar.com'
        ]);

        $message = new \Swift_Message();
        $headers = $message->getHeaders();

        $messageConfigurationUtility->applyBounceAddress($message);

        $this->assertEquals("Return-Path: <foo@bar.com>\r\n", $headers->get('Return-Path')->toString());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyBounceAddress()
     *
     * @return void
     */
    public function testApplyBounceAddressNullAddress()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'bounce_address' => null
        ]);

        $message = new \Swift_Message();
        $headers = $message->getHeaders();

        $messageConfigurationUtility->applyBounceAddress($message);

        $this->assertEquals("Return-Path: \r\n", $headers->get('Return-Path')->toString());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyFrom()
     *
     * @return void
     */
    public function testApplyFrom()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility(
            [
                'sender_address' => 'foo@bar.com',
                'sender_name'    => 'God'
            ]
        );

        $message = new \Swift_Message();

        $messageConfigurationUtility->applyFrom($message);

        $this->assertEquals(['foo@bar.com' => 'God'], $message->getFrom());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applySubjectPrefix()
     *
     * @return void
     */
    public function testApplySubjectPrefix()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'subject' => [
                'prefix' => '[FOO]'
            ]
        ]);

        $message = new \Swift_Message('Welcome undefined');

        $messageConfigurationUtility->applySubjectPrefix($message);

        $this->assertEquals('[FOO] Welcome undefined', $message->getSubject());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyYopmail()
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyYopmailOn()
     *
     * @return void
     */
    public function testApplyYopmail()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'bcc_address' => null,
            'test'        => true
        ]);

        $message = new \Swift_Message('Welcome undefined');
        $message->setTo(
            [
                // email      => name
                'foo@bar.com' => 'foo@bar.com',
                'bar@foo.com' => 'bar@foo.com'
            ]
        );
        $message->setCc(
            [
                // email      => name
                'foo@bar.com' => 'foo@bar.com',
                'bar@foo.com' => 'bar@foo.com'
            ]
        );

        $messageConfigurationUtility->applyYopmail($message);

        $this->assertEquals(
            [
                'foo_bar_com@yopmail.com' => 'foo@bar.com',
                'bar_foo_com@yopmail.com' => 'bar@foo.com'
            ],
            $message->getTo()
        );

        $this->assertEquals(
            [
                'foo_bar_com@yopmail.com' => 'foo@bar.com',
                'bar_foo_com@yopmail.com' => 'bar@foo.com'
            ],
            $message->getCc()
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyYopmail()
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyYopmailOn()
     *
     * @return void
     */
    public function testApplyYopmailWithoutCc()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'bcc_address' => null,
            'test'        => true
        ]);

        $message = new \Swift_Message('Welcome undefined');
        $message->setTo(
            [
                // email      => name
                'foo@bar.com' => 'foo@bar.com',
                'bar@foo.com' => 'bar@foo.com'
            ]
        );
        $messageConfigurationUtility->applyYopmail($message);

        $this->assertEquals(
            [
                'foo_bar_com@yopmail.com' => 'foo@bar.com',
                'bar_foo_com@yopmail.com' => 'bar@foo.com'
            ],
            $message->getTo()
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyYopmail()
     *
     * @return void
     */
    public function testApplyYopmailNotInTest()
    {
        $messageConfigurationUtility = new MessageConfigurationUtility([
            'bcc_address' => null,
            'test'        => false
        ]);

        $message = new \Swift_Message('Welcome undefined');
        $message->setTo(
            [
                // email      => name
                'foo@bar.com' => 'foo@bar.com',
                'bar@foo.com' => 'bar@foo.com'
            ]
        );

        $messageConfigurationUtility->applyYopmail($message);

        $this->assertEquals(
            [
                'foo@bar.com' => 'foo@bar.com',
                'bar@foo.com' => 'bar@foo.com'
            ],
            $message->getTo()
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::__construct
     * @covers \Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility::applyAll()
     *
     * @return void
     */
    public function testApplyAll()
    {
        /** @var MessageConfigurationUtility|\Mockery\MockInterface $messageConfigurationUtility */
        $messageConfigurationUtility = \Mockery::mock(MessageConfigurationUtility::class)->makePartial();
        $message = new \Swift_Message();

        $messageConfigurationUtility->shouldReceive('applyAmazonTags')->once()->andReturn(new \Swift_Message());
        $messageConfigurationUtility->shouldReceive('applyBccAddress')->once()->andReturn(new \Swift_Message());
        $messageConfigurationUtility->shouldReceive('applyBounceAddress')->once()->andReturn(new \Swift_Message());
        $messageConfigurationUtility->shouldReceive('applyFrom')->once()->andReturn(new \Swift_Message());
        $messageConfigurationUtility->shouldReceive('applySubjectPrefix')->once()->andReturn(new \Swift_Message());
        $messageConfigurationUtility->shouldReceive('applyYopmail')->once()->andReturn(new \Swift_Message());

        $messageConfigurationUtility->applyAll($message);
    }
}
