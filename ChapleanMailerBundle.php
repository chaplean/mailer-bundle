<?php

namespace Chaplean\Bundle\MailerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

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