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
    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        // No Datafixtures
    }

    /**
     * @return void
     */
    public function testCreateMailer()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);

        $result = $this->getContainer()->get('swiftmailer.mailer.default')->send($message);
        $this->assertEquals(1, $result);
    }

    /**
     * @return void
     */
    public function testSetSubjectAddsConfiguredPrefix()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);
        $message->setSubject('My subject');
        
        static::assertEquals('[TEST]My subject', $message->getSubject());
    }

    /**
     * @return void
     */
    public function testKeepPreviousAddressesOnAddTo()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);
        $message->addTo('test1@test.com');
        static::assertCount(1, $message->getTo());

        $message->addTo('test2@test.com');
        static::assertCount(2, $message->getTo());
    }

    /**
     * @return void
     */
    public function testKeepPreviousAddressesOnAddCc()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);
        $message->addCc('test1@test.com');
        static::assertCount(1, $message->getCc());

        $message->addCc('test2@test.com');
        static::assertCount(2, $message->getCc());
    }

    /**
     * @return void
     */
    public function testKeepPreviousAddressesOnAddBcc()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);
        $message->addBcc('test1@test.com');
        static::assertCount(2, $message->getBcc());

        $message->addBcc('test2@test.com');
        static::assertCount(3, $message->getBcc());
    }

    /**
     * @return void
     */
    public function testKeepAutomaticConfiguredBccEvenOnSetBcc()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);
        static::assertCount(1, $message->getBcc());

        $message->setBcc('test2@test.com');
        static::assertCount(2, $message->getBcc());
    }

    /**
     * @return void
     */
    public function testGetTime()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);

        $this->assertInternalType('float', $message->getTime());
    }
    
    /**
     * @return void
     */
    public function testTransformMail()
    {
        $chapleaConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleaConfig);
        $addresses = array(
            'already_transformed@yopmail.com' => null,
            'to_transform@example.com' => null,
            'other_to_transform@example.com',
            'named_to_transform@example.com' => 'named address',
        );
        
        static::assertEquals(
            array(
                'already_transformed@yopmail.com' => null,
                'to_transform_example_com@yopmail.com' => null,
                'other_to_transform_example_com@yopmail.com' => 'other_to_transform@example.com',
                'named_to_transform_example_com@yopmail.com' => 'named address',
            ),
            $message->transformMail($addresses)
        );
    }
}
