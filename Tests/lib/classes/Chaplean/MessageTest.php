<?php

namespace Chaplean\Bundle\MailerBundle\Tests\lib\classes\Chaplean;

use Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message;
use Chaplean\Bundle\UnitBundle\Test\LogicalTestCase;

/**
 * Class MessageTest.
 *
 * @package   Chaplean\Bundle\MailerBundle\Tests\lib\classes\Chaplean
 * @author    Benoit - Chaplean <benoit@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.0.0
 */
class MessageTest extends LogicalTestCase
{
    /**
     * Load empty data fixture to generate the database schema even if no data are given
     * TODO: remove this function (used cause of a bug in Unit when a project has no datafixtures)
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
    }

    /**
     * @return void
     */
    public function testCreateMailer()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $result = $this->getContainer()->get('swiftmailer.mailer.default')->send($message);
        static::assertEquals(1, $result);
    }

    /**
     * @return void
     */
    public function testSetSubjectAddsConfiguredPrefix()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
        $message->setSubject('My subject');

        static::assertEquals('[TEST]My subject', $message->getSubject());
    }

    /**
     * @return void
     */
    public function testKeepPreviousAddressesOnAddTo()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
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
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
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
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
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
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
        static::assertCount(1, $message->getBcc());

        $message->setBcc('test2@test.com');
        static::assertCount(2, $message->getBcc());
    }

    /**
     * @return void
     */
    public function testGetTime()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        static::assertInternalType('float', $message->getTime());
    }

    /**
     * @return void
     */
    public function testTransformMail()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
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

    /**
     * @return void
     */
    public function testSetToHandlesArray()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setTo(array('address@example.com'));
        static::assertEquals(array('address_example_com@yopmail.com' => 'address@example.com'), $message->getTo());
    }

    /**
     * @return void
     */
    public function testSetToHandlesSingleAddress()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setTo('address@example.com');
        static::assertEquals(array('address_example_com@yopmail.com' => null), $message->getTo());
    }

    /**
     * @return void
     */
    public function testSetToHandlesSingleAddressWithName()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setTo('address@example.com', 'my test address');
        static::assertEquals(array('address_example_com@yopmail.com' => 'my test address'), $message->getTo());
    }

    /**
     * @return void
     */
    public function testSetCcHandlesArray()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setCc(array('address@example.com'));
        static::assertEquals(array('address_example_com@yopmail.com' => 'address@example.com'), $message->getCc());
    }

    /**
     * @return void
     */
    public function testSetCcHandlesSingleAddress()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setCc('address@example.com');
        static::assertEquals(array('address_example_com@yopmail.com' => null), $message->getCc());
    }

    /**
     * @return void
     */
    public function testSetCcHandlesSingleAddressWithName()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setCc('address@example.com', 'my test address');
        static::assertEquals(array('address_example_com@yopmail.com' => 'my test address'), $message->getCc());
    }

    /**
     * @return void
     */
    public function testSetBccHandlesArray()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setBcc(array('address@example.com'));
        static::assertEquals(
            array(
                'address_example_com@yopmail.com' => 'address@example.com',
                'staff@chaplean.com' => 'staff@chaplean.com'
            ), $message->getBcc()
        );
    }

    /**
     * @return void
     */
    public function testSetBccHandlesSingleAddress()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setBcc('address@example.com');
        static::assertEquals(
            array(
                'address_example_com@yopmail.com' => null,
                'staff@chaplean.com' => 'staff@chaplean.com'
            ), $message->getBcc()
        );
    }

    /**
     * @return void
     */
    public function testSetBccHandlesSingleAddressWithName()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setBcc('address@example.com', 'my test address');
        static::assertEquals(
            array(
                'address_example_com@yopmail.com' => 'my test address',
                'staff@chaplean.com' => 'staff@chaplean.com'
            ), $message->getBcc()
        );
    }
}
