<?php

namespace Chaplean\Bundle\MailerBundle\lib\classes\Chaplean;

/**
 * Class Chaplean_Message.
 *
 * @author    Benoit - Chaplean <benoit@chaplean.com>
 * @copyright 2014 - 2015 Chaplean (http://www.chaplean.com)
 * @since     0.1.0
 */
class Message extends \Swift_Message
{
    /**
     * @var
     */
    protected $chapleanMailerConfig;

    /**
     * Constructor.
     *
     * @param array $chapleanMailerConfig
     */
    public function __construct(array $chapleanMailerConfig)
    {
        parent::__construct();

        $this->setFrom($chapleanMailerConfig['sender_address'], $chapleanMailerConfig['sender_name']);
        if (isset($chapleanMailerConfig['bcc_address'])) {
            $this->setBcc($chapleanMailerConfig['bcc_address']);
        }

        $this->chapleanMailerConfig = $chapleanMailerConfig;
    }

    /**
     * Set the subject of this message.
     *
     * @param string $subject
     *
     * @return Message
     */
    public function setSubject($subject)
    {
        $prefix = $this->chapleanMailerConfig['subject']['prefix'];
        $subject = $prefix . $subject;

        if (!$this->_setHeaderFieldModel('Subject', $subject)) {
            $this->getHeaders()->addTextHeader('Subject', $subject);
        }

        return $this;
    }

    /**
     * Set the to addresses of this message.
     *
     * If multiple recipients will receive the message an array should be used.
     * Example: array('receiver@domain.org', 'other@domain.org' => 'A name')
     *
     * If $name is passed and the first parameter is a string, this name will be
     * associated with the address.
     *
     * @param mixed       $addresses
     * @param string|null $name      optional
     *
     * @return Message
     */
    public function setTo($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }

        $test = $this->chapleanMailerConfig['test'];

        if ($test) {
            if (is_array($addresses)) {
                $finalAddresses = array();

                foreach ($addresses as $recipient => $recipientName) {
                    if (is_string($recipient)) {
                        $newRecipient = str_replace(array('.', '@'), '_', $recipient) . '@yopmail.com';
                        $finalAddresses[$newRecipient] = $newRecipient;
                    }
                }

                $addresses = $finalAddresses;
            } else {
                $addresses = str_replace(array('.', '@'), '_', $addresses) . '@yopmail.com';
            }
        }

        if (!$this->_setHeaderFieldModel('To', (array) $addresses)) {
            $this->getHeaders()->addMailboxHeader('To', (array) $addresses);
        }

        return $this;
    }
}
