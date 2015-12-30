<?php

namespace Chaplean\Bundle\MailerBundle\Tests\lib\classes\Chaplean;

use Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message;
use Chaplean\Bundle\UnitBundle\Test\LogicalTest;

/**
 * Class MessageTest.
 *
 * @package   Chaplean\Bundle\MailerBundle\Tests\lib\classes\Chaplean
 * @author    Benoit - Chaplean <benoit@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.0.0
 */
class MessageTest extends LogicalTest
{
    public function testCreateMailer()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);

        $result = $this->getContainer()->get('swiftmailer.mailer.default')->send($message);

        $this->assertEquals(1, $result);
    }
}
