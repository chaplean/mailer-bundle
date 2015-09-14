<?php
/**
 * MessageTest.php.
 *
 * @author    Valentin - Chaplean <valentin@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.0.0
 */

namespace Chaplean\Bundle\MailerBundle\Tests\lib\classes\Chaplean;

use Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message;
use Chaplean\Bundle\UnitBundle\Test\LogicalTest;

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
