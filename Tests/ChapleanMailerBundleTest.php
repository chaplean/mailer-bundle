<?php

namespace Tests\Chaplean\Bundle\MailerBundle;

use Chaplean\Bundle\MailerBundle\ChapleanMailerBundle;
use PHPUnit\Framework\TestCase;

/**
 * Class ChapleanMailerBundleTest.
 *
 * @package   Tests\Chaplean\Bundle\MailerBundle
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     4.0.0
 */
class ChapleanMailerBundleTest extends TestCase
{
    /**
     * @covers \Chaplean\Bundle\MailerBundle\ChapleanMailerBundle::getParent()
     *
     * @return void
     */
    public function testGetParent()
    {
        $chapleanMailerBundle = new ChapleanMailerBundle();

        $this->assertEquals('SwiftmailerBundle', $chapleanMailerBundle->getParent());
    }
}
