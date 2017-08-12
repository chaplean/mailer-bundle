<?php

namespace Chaplean\Bundle\MailerBundle\EventListener;

use Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class MailLoggingEventListenerTest.
 *
 * @package   Chaplean\Bundle\MailerBundle\EventListener
 * @author    Matthias - Chaplean <matthias@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     3.0.3
 */
class MailLoggingEventListenerTest extends TestCase
{
    private $chapleanMailerConfig;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->chapleanMailerConfig = [
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
     * @covers \Chaplean\Bundle\MailerBundle\EventListener::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\EventListener::sendPerformed()
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testLoggingUnknownTransport()
    {
        $message = new Message($this->chapleanMailerConfig);

        $expected = 'Mail unknown transport: ' . $message->toString();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')
            ->with($expected)
            ->once();

        $swiftTransportMock = \Mockery::mock(\Swift_Transport::class);

        $swiftEventSendEvent = new \Swift_Events_SendEvent($swiftTransportMock, $message);

        $mailLoggingEventListener = new MailLoggingEventListener($logger);
        $mailLoggingEventListener->sendPerformed($swiftEventSendEvent);
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\EventListener::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\EventListener::sendPerformed()
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testLoggingSpoolTransport()
    {
        $message = new Message($this->chapleanMailerConfig);

        $expected = 'Mail queued: ' . $message->toString();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')
            ->with($expected)
            ->once();

        $swiftTransportMock = \Mockery::mock(\Swift_Transport_SpoolTransport::class);

        $swiftEventSendEvent = new \Swift_Events_SendEvent($swiftTransportMock, $message);

        $mailLoggingEventListener = new MailLoggingEventListener($logger);
        $mailLoggingEventListener->sendPerformed($swiftEventSendEvent);
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\EventListener::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\EventListener::sendPerformed()
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testLoggingAbstractSmtpTransport()
    {
        $message = new Message($this->chapleanMailerConfig);

        $expected = 'Mail sent: ' . $message->toString();

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')
            ->with($expected)
            ->once();

        $swiftTransportMock = \Mockery::mock(\Swift_Transport_AbstractSmtpTransport::class);

        $swiftEventSendEvent = new \Swift_Events_SendEvent($swiftTransportMock, $message);

        $mailLoggingEventListener = new MailLoggingEventListener($logger);
        $mailLoggingEventListener->sendPerformed($swiftEventSendEvent);
    }
}
