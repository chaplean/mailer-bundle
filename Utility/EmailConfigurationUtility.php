<?php

namespace Chaplean\Bundle\MailerBundle\Utility;

/**
 * Class EmailConfigurationUtility.
 *
 * @package   Chaplean\Bundle\MailerBundle\Utility
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     4.0.0
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
     * Remove addresses which the domain is in 'chaplean_mailer.disabled_email_extensions'
     *
     * @param array $addresses
     *
     * @return array
     */
    public function removeEmailDisabled(array $addresses)
    {
        $newAddresses = [];
        foreach ($addresses as $email => $name) {
            $domain = $this->extractDomain($email);

            if (!in_array($domain, $this->config['disabled_email_extensions'], true)) {
                $newAddresses[$email] = $name;
            }
        }

        return $newAddresses;
    }

    /**
     * @param string $email
     *
     * @return boolean
     */
    public function isDisabledEmail($email)
    {
        return empty($this->removeEmailDisabled([$email => $email]));
    }

    /**
     * Extract domain of email (very simply!)
     *
     * @param string $email
     *
     * @return string
     */
    public function extractDomain($email)
    {
        return substr(strrchr($email, '@'), 1);
    }
}
