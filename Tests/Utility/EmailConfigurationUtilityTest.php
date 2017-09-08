<?php

namespace Tests\Chaplean\Bundle\MailerBundle\Utility;

use Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility;
use PHPUnit\Framework\TestCase;

/**
 * Class EmailConfigurationUtilityTest.
 *
 * @package   Tests\Chaplean\Bundle\MailerBundle\Utility
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     4.0.0
 */
class EmailConfigurationUtilityTest extends TestCase
{
    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility::removeEmailDisabled()
     *
     * @return void
     */
    public function testRemoveEmailDisabled()
    {
        $emailConfigurationUtility = new EmailConfigurationUtility([
            'disabled_email_extensions' => ['bar.com']
        ]);

        $this->assertEquals([], $emailConfigurationUtility->removeEmailDisabled(['foo@bar.com' => null]));
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility::removeEmailDisabled
     *
     * @return void
     */
    public function testRemoveEmailDisabledNotExcluded()
    {
        $emailConfigurationUtility = new EmailConfigurationUtility([
            'disabled_email_extensions' => ['foo.com']
        ]);

        $this->assertEquals(['foo@bar.com' => null], $emailConfigurationUtility->removeEmailDisabled(['foo@bar.com' => null]));
    }

    /**
     * @covers \Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility::__construct()
     * @covers \Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility::extractDomain
     *
     * @return void
     */
    public function testExtractDomain()
    {
        $emailConfigurationUtility = new EmailConfigurationUtility([]);

        $this->assertEquals('bar.com', $emailConfigurationUtility->extractDomain('foo@bar.com'));
        $this->assertEquals('', $emailConfigurationUtility->extractDomain('bar.com'));
        $this->assertEquals('der', $emailConfigurationUtility->extractDomain('foo@tr-dz@der'));
    }
}
