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
    public function testCreateMailer()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $result = $this->getContainer()->get('swiftmailer.mailer.default')->send($message);
        $this->assertEquals(1, $result);
    }

    /**
     * @return void
     */
    public function testSetSubjectAddsConfiguredPrefix()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
        $message->setSubject('My subject');

        $this->assertEquals('[TEST] My subject', $message->getSubject());
    }

    /**
     * @return void
     */
    public function testKeepPreviousAddressesOnAddTo()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
        $message->addTo('test1@test.com');
        $this->assertCount(1, $message->getTo());

        $message->addTo('test2@test.com');
        $this->assertCount(2, $message->getTo());
    }

    /**
     * @return void
     */
    public function testKeepPreviousAddressesOnAddCc()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
        $message->addCc('test1@test.com');
        $this->assertCount(1, $message->getCc());

        $message->addCc('test2@test.com');
        $this->assertCount(2, $message->getCc());
    }

    /**
     * @return void
     */
    public function testKeepPreviousAddressesOnAddBcc()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
        $message->addBcc('test1@test.com');
        $this->assertCount(2, $message->getBcc());

        $message->addBcc('test2@test.com');
        $this->assertCount(3, $message->getBcc());
    }

    /**
     * @return void
     */
    public function testKeepAutomaticConfiguredBccEvenOnSetBcc()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);
        $this->assertCount(1, $message->getBcc());

        $message->setBcc('test2@test.com');
        $this->assertCount(2, $message->getBcc());
    }

    /**
     * @return void
     */
    public function testGetTime()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $this->assertInternalType('float', $message->getTime());
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

        $this->assertEquals(
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
        $this->assertEquals(array('address_example_com@yopmail.com' => 'address@example.com'), $message->getTo());
    }

    /**
     * @return void
     */
    public function testSetToHandlesSingleAddress()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setTo('address@example.com');
        $this->assertEquals(array('address_example_com@yopmail.com' => null), $message->getTo());
    }

    /**
     * @return void
     */
    public function testSetToHandlesSingleAddressWithName()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setTo('address@example.com', 'my test address');
        $this->assertEquals(array('address_example_com@yopmail.com' => 'my test address'), $message->getTo());
    }

    /**
     * @return void
     */
    public function testSetCcHandlesArray()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setCc(array('address@example.com'));
        $this->assertEquals(array('address_example_com@yopmail.com' => 'address@example.com'), $message->getCc());
    }

    /**
     * @return void
     */
    public function testSetCcHandlesSingleAddress()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setCc('address@example.com');
        $this->assertEquals(array('address_example_com@yopmail.com' => null), $message->getCc());
    }

    /**
     * @return void
     */
    public function testSetCcHandlesSingleAddressWithName()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setCc('address@example.com', 'my test address');
        $this->assertEquals(array('address_example_com@yopmail.com' => 'my test address'), $message->getCc());
    }

    /**
     * @return void
     */
    public function testSetBccHandlesArray()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $message = new Message($chapleanConfig);

        $message->setBcc(array('address@example.com'));
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
            array(
                'address_example_com@yopmail.com' => 'my test address',
                'staff@chaplean.com' => 'staff@chaplean.com'
            ), $message->getBcc()
        );
    }

    /**
     * @return void
     */
    public function testNoAmazonTags()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $this->assertArrayNotHasKey('amazon_tags', $chapleanConfig);
        $message = new Message($chapleanConfig);

        $message->setBcc('address@example.com', 'my test address');
        $this->assertNull($message->getHeaders()->get('X-SES-CONFIGURATION-SET'));
        $this->assertNull($message->getHeaders()->get('X-SES-MESSAGE-TAGS'));
    }

    /**
     * @return void
     */
    public function testWithAmazonTags()
    {
        $chapleanConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $chapleanConfig['amazon_tags'] = array(
            'configuration_set' => 'test',
            'project_name' => 'test_project',
            'env' => '[TEST]'
        );
        $message = new Message($chapleanConfig);

        $message->setBcc('address@example.com', 'my test address');
        $this->assertEquals('test', $message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getFieldBody());
        $this->assertEquals('project_name=test_project, environment=[TEST]', $message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getFieldBody());
    }

    /**
     * @return void
     */
    public function testWithoutBounce()
    {
        $noBounceConfig = $this->getContainer()->getParameter('chaplean_mailer');
        unset($noBounceConfig['bounce_address']);
        $noBounce = new Message($noBounceConfig);

        $this->assertNull($noBounce->getReturnPath());
        $this->assertNull($noBounce->getHeaders()->get('Return-Path'));
    }

    /**
     * @return void
     */
    public function testWithBounce()
    {
        $bounceConfig = $this->getContainer()->getParameter('chaplean_mailer');
        $bounceConfig['bounce_address'] = 'bounce@address.com';
        $bounce = new Message($bounceConfig);

        $this->assertEquals('bounce@address.com', $bounce->getReturnPath());
        $this->assertNotNull($bounce->getHeaders()->get('Return-Path'));
        $this->assertEquals('<bounce@address.com>', $bounce->getHeaders()->get('Return-Path')->getFieldBody());
    }
}
