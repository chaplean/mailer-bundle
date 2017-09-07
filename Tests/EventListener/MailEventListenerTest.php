<?php

namespace Tests\Chaplean\Bundle\MailerBundle\EventListener;

use Chaplean\Bundle\MailerBundle\EventListener\MailEventListener;
use Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility;
use Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Log\LoggerInterface;

/**
 * Class MailEventListenerTest.
 *
 * @package   Chaplean\Bundle\MailerBundle\EventListener
 * @author    Matthias - Chaplean <matthias@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     3.0.3
 */
class MailEventListenerTest extends MockeryTestCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->config = [
            'bcc_address'    => 'staff@chaplean.coop',
            'bounce_address' => 'staff@chaplean.coop',
            'sender_address' => 'staff@chaplean.coop',
            'sender_name'    => 'Chaplean Staff',
            'subject'        => [
                'prefix' => '[TEST]'
            ],
            'test'           => 'true'
        ];
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\EventListener\MailEventListener::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\EventListener\MailEventListener::sendPerformed()
     *
     * @return void
     */
    public function testLoggingUnknownTransport()
    {
        $message = new \Swift_Message();

        $expected = 'Mail unknown transport: ' . $message->toString();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')->once()->with($expected);
        $messageConfigurationUtility = new MessageConfigurationUtility([]);
        $emailConfigurationUtility = new EmailConfigurationUtility([]);

        $swiftTransportMock = \Mockery::mock(\Swift_Transport::class);

        $swiftEventSendEvent = new \Swift_Events_SendEvent($swiftTransportMock, $message);

        $mailLoggingEventListener = new MailEventListener($logger, $messageConfigurationUtility, $emailConfigurationUtility);
        $mailLoggingEventListener->sendPerformed($swiftEventSendEvent);
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\EventListener\MailEventListener::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\EventListener\MailEventListener::sendPerformed()
     *
     * @return void
     */
    public function testLoggingSpoolTransport()
    {
        $message = new \Swift_Message();

        $expected = 'Mail queued: ' . $message->toString();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')->once()->with($expected);
        $messageConfigurationUtility = new MessageConfigurationUtility([]);
        $emailConfigurationUtility = new EmailConfigurationUtility([]);

        $swiftTransportMock = \Mockery::mock(\Swift_Transport_SpoolTransport::class);

        $swiftEventSendEvent = new \Swift_Events_SendEvent($swiftTransportMock, $message);

        $mailLoggingEventListener = new MailEventListener($logger, $messageConfigurationUtility, $emailConfigurationUtility);
        $mailLoggingEventListener->sendPerformed($swiftEventSendEvent);
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\EventListener\MailEventListener::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\EventListener\MailEventListener::sendPerformed()
     *
     * @return void
     */
    public function testLoggingAbstractSmtpTransport()
    {
        $message = new \Swift_Message();

        $expected = 'Mail sent: ' . $message->toString();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')->once()->with($expected);
        $messageConfigurationUtility = new MessageConfigurationUtility([]);
        $emailConfigurationUtility = new EmailConfigurationUtility([]);

        $swiftTransportMock = \Mockery::mock(\Swift_Transport_AbstractSmtpTransport::class);
        $swiftEventSendEvent = new \Swift_Events_SendEvent($swiftTransportMock, $message);

        $mailLoggingEventListener = new MailEventListener($logger, $messageConfigurationUtility, $emailConfigurationUtility);
        $mailLoggingEventListener->sendPerformed($swiftEventSendEvent);
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\EventListener\MailEventListener::beforeSendPerformed()
     *
     * @return void
     */
    public function testBeforeSendPerformedNotEmptyAddresses()
    {
        $message = new \Swift_Message();
        $message->addTo('foo@bar.com');

        $logger = \Mockery::mock(LoggerInterface::class);
        $messageConfigurationUtility = \Mockery::mock(MessageConfigurationUtility::class);
        $messageConfigurationUtility->shouldReceive('applyAll')->once();
        $emailConfigurationUtility = \Mockery::mock(EmailConfigurationUtility::class);
        $emailConfigurationUtility->shouldReceive('removeEmailDisabled')->once()->andReturn(['foo@bar.com']);

        $swiftTransportMock = \Mockery::mock(\Swift_Transport_AbstractSmtpTransport::class);
        $swiftEventSendEvent = new \Swift_Events_SendEvent($swiftTransportMock, $message);

        $mailLoggingEventListener = new MailEventListener($logger, $messageConfigurationUtility, $emailConfigurationUtility);

        $mailLoggingEventListener->beforeSendPerformed($swiftEventSendEvent);
    }
}
