<?php

namespace Chaplean\Bundle\MailerBundle\Tests\lib\classes\Chaplean;

use Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageTest.
 *
 * @package   Chaplean\Bundle\MailerBundle\Tests\lib\classes\Chaplean
 * @author    Benoit - Chaplean <benoit@chaplean.coop>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.coop)
 * @since     1.0.0
 */
class MessageTest extends TestCase
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
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     *
     * @return void
     */
    public function testMessageIsSwiftMessageInstzance()
    {
        $message = new Message($this->chapleanMailerConfig);

        $this->assertInstanceOf(\Swift_Message::class, $message);
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getSubject()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setSubject()
     *
     * @return void
     */
    public function testSetSubjectAddsConfiguredPrefix()
    {
        $message = new Message($this->chapleanMailerConfig);
        $message->setSubject('My subject');

        $this->assertEquals('[TEST] My subject', $message->getSubject());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::addTo()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getTo()
     *
     * @return void
     */
    public function testKeepPreviousAddressesOnAddTo()
    {
        $message = new Message($this->chapleanMailerConfig);
        $message->addTo('test1@test.com');
        $this->assertCount(1, $message->getTo());

        $message->addTo('test2@test.com');
        $this->assertCount(2, $message->getTo());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::addCc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getCc()
     *
     * @return void
     */
    public function testKeepPreviousAddressesOnAddCc()
    {
        $message = new Message($this->chapleanMailerConfig);
        $message->addCc('test1@test.com');
        $this->assertCount(1, $message->getCc());

        $message->addCc('test2@test.com');
        $this->assertCount(2, $message->getCc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::addBcc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getBcc()
     *
     * @return void
     */
    public function testKeepPreviousAddressesOnAddBcc()
    {
        $message = new Message($this->chapleanMailerConfig);
        $message->addBcc('test1@test.com');
        $this->assertCount(2, $message->getBcc());

        $message->addBcc('test2@test.com');
        $this->assertCount(3, $message->getBcc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getBcc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setBcc()
     *
     * @return void
     */
    public function testKeepAutomaticConfiguredBccEvenOnSetBcc()
    {
        $message = new Message($this->chapleanMailerConfig);
        $this->assertCount(1, $message->getBcc());

        $message->setBcc('test2@test.com');
        $this->assertCount(2, $message->getBcc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getTime()
     *
     * @return void
     */
    public function testGetTime()
    {
        $message = new Message($this->chapleanMailerConfig);

        $this->assertInternalType('float', $message->getTime());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::transformMail()
     *
     * @return void
     */
    public function testTransformMail()
    {
        $message = new Message($this->chapleanMailerConfig);
        $addresses = [
            'already_transformed@yopmail.com' => null,
            'to_transform@example.com' => null,
            'other_to_transform@example.com',
            'named_to_transform@example.com' => 'named address'
        ];

        $this->assertEquals(
            [
                'already_transformed@yopmail.com' => null,
                'to_transform_example_com@yopmail.com' => null,
                'other_to_transform_exampl@yopmail.com' => 'other_to_transform@example.com',
                'named_to_transform_exampl@yopmail.com' => 'named address'
            ],
            $message->transformMail($addresses)
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getTo()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setTo()
     *
     * @return void
     */
    public function testSetToHandlesArray()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setTo(array('address@example.com'));
        $this->assertEquals(array('address_example_com@yopmail.com' => 'address@example.com'), $message->getTo());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getTo()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setTo()
     *
     * @return void
     */
    public function testSetToHandlesSingleAddress()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setTo('address@example.com');
        $this->assertEquals(array('address_example_com@yopmail.com' => null), $message->getTo());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getTo()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setTo()
     *
     * @return void
     */
    public function testSetToHandlesSingleAddressWithName()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setTo('address@example.com', 'my test address');
        $this->assertEquals(array('address_example_com@yopmail.com' => 'my test address'), $message->getTo());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setCc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getCc()
     *
     * @return void
     */
    public function testSetCcHandlesArray()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setCc(array('address@example.com'));
        $this->assertEquals(array('address_example_com@yopmail.com' => 'address@example.com'), $message->getCc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setCc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getCc()
     *
     * @return void
     */
    public function testSetCcHandlesSingleAddress()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setCc('address@example.com');
        $this->assertEquals(array('address_example_com@yopmail.com' => null), $message->getCc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setCc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getCc()
     *
     * @return void
     */
    public function testSetCcHandlesSingleAddressWithName()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setCc('address@example.com', 'my test address');
        $this->assertEquals(array('address_example_com@yopmail.com' => 'my test address'), $message->getCc());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setBcc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getBcc()
     *
     * @return void
     */
    public function testSetBccHandlesArray()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setBcc(array('address@example.com'));
        $this->assertEquals(
            array(
                'address_example_com@yopmail.com' => 'address@example.com',
                'staff@chaplean.coop' => 'staff@chaplean.coop'
            ), $message->getBcc()
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setBcc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getBcc()
     *
     * @return void
     */
    public function testSetBccHandlesSingleAddress()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setBcc('address@example.com');
        $this->assertEquals(
            array(
                'address_example_com@yopmail.com' => null,
                'staff@chaplean.coop' => 'staff@chaplean.coop'
            ), $message->getBcc()
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getBcc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setBcc()
     *
     * @return void
     */
    public function testSetBccHandlesSingleAddressWithName()
    {
        $message = new Message($this->chapleanMailerConfig);

        $message->setBcc('address@example.com', 'my test address');
        $this->assertEquals(
            array(
                'address_example_com@yopmail.com' => 'my test address',
                'staff@chaplean.coop' => 'staff@chaplean.coop'
            ), $message->getBcc()
        );
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setBcc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getHeaders()
     *
     * @return void
     */
    public function testNoAmazonTags()
    {
        $this->assertArrayNotHasKey('amazon_tags', $this->chapleanMailerConfig);
        $message = new Message($this->chapleanMailerConfig);

        $message->setBcc('address@example.com', 'my test address');
        $this->assertNull($message->getHeaders()->get('X-SES-CONFIGURATION-SET'));
        $this->assertNull($message->getHeaders()->get('X-SES-MESSAGE-TAGS'));
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::setBcc()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getHeaders()
     *
     * @return void
     */
    public function testWithAmazonTags()
    {
        $this->chapleanMailerConfig['amazon_tags'] = array(
            'configuration_set' => 'test',
            'project_name' => 'test_project',
            'env' => '[TEST]'
        );
        $message = new Message($this->chapleanMailerConfig);

        $message->setBcc('address@example.com', 'my test address');
        $this->assertEquals('test', $message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getFieldBody());
        $this->assertEquals('project_name=test_project, environment=[TEST]', $message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getFieldBody());
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getReturnPath()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getHeaders()
     *
     * @return void
     */
    public function testWithoutBounce()
    {
        unset($this->chapleanMailerConfig['bounce_address']);
        $noBounce = new Message($this->chapleanMailerConfig);

        $this->assertNull($noBounce->getReturnPath());
        $this->assertNull($noBounce->getHeaders()->get('Return-Path'));
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getReturnPath()
     * @covers \Chaplean\Bundle\MailerBundle\lib\classes\Chaplean\Message::getHeaders()
     *
     * @return void
     */
    public function testWithBounce()
    {
        $this->chapleanMailerConfig['bounce_address'] = 'bounce@address.com';
        $bounce = new Message($this->chapleanMailerConfig);

        $this->assertEquals('bounce@address.com', $bounce->getReturnPath());
        $this->assertNotNull($bounce->getHeaders()->get('Return-Path'));
        $this->assertEquals('<bounce@address.com>', $bounce->getHeaders()->get('Return-Path')->getFieldBody());
    }
}
