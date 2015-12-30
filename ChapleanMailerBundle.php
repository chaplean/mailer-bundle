<?php

namespace Chaplean\Bundle\MailerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ChapleanMailerBundle.
 *
 * @package   Chaplean\Bundle\MailerBundle
 * @author    Benoit - Chaplean <benoit@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     1.0.0
 */
class ChapleanMailerBundle extends Bundle
{
    /**
     * Returns SwiftmailerBundle;
     *
     * @return string
     */
    public function getParent()
    {
        return 'SwiftmailerBundle';
    }
}
