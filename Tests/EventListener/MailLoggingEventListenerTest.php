<?php

namespace Chaplean\Bundle\MailerBundle\EventListener;

use Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message;
use Chaplean\Bundle\UnitBundle\Test\LogicalTestCase;
use Monolog\Logger;

/**
 * Class MailLoggingEventListenerTest.
 *
 * @package   Chaplean\Bundle\MailerBundle\EventListener
 * @author    Matthias - Chaplean <matthias@chaplean.com>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.com)
 * @since     3.0.3
 */
class MailLoggingEventListenerTest extends LogicalTestCase
{
    /**
     * Load empty data fixture to generate the database schema even if no data are given
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$datafixturesEnabled = false;
        parent::setUpBeforeClass();
    }

    /**
     * @return void
     */
    public function testLoggingOnSendMail()
    {
        $this->markTestSkipped("Logger mock doen't work.");

        $expected = 'test subject - 1';

        $logger = \Mockery::mock(Logger::class);
        $logger->makePartial();
        $logger->shouldReceive('info')->withArgs(array('message' => $expected))->once();
        $this->getContainer()->set('logger', $logger);
        $this->getContainer()->set('chaplean_mailer.event_listener.mail_logging', null);

        $mail = new Message($this->getContainer()->getParameter('chaplean_mailer'));
        $mail->setSubject('test subject');

        /** @var \Swift_Mailer $sendMail */
        $sendMail = $this->getContainer()->get('swiftmailer.mailer');
        $sendMail->send($mail);
    }
}
