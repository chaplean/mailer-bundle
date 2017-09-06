<?php

namespace Chaplean\Bundle\MailerBundle\Utility;

/**
 * Class EmailConfigurationUtility.
 *
 * @package   Chaplean\Bundle\MailerBundle\Utility
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     X.Y.Z
 */
class EmailConfigurationUtility
{
    /**
     * @var array
     */
    private $config;

    /**
     * EmailConfigurationUtility constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $addresses
     *
     * @return array
     */
    public function removeEmailDisabled(array $addresses)
    {
        $newAddresses = [];
        foreach ($addresses as $email => $name) {
            $domain = substr(strrchr($email, '@'), 1);

            if (!in_array($domain, $this->config['disabled_email_extensions'], true)) {
                $newAddresses[$email] = $name;
            }
        }

        return $newAddresses;
    }
}
