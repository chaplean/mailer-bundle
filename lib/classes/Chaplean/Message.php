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
     * @var float
     */
    private $time;

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

        $headers = $this->getHeaders();

        if (array_key_exists('amazon_tags', $chapleanMailerConfig)) {
            $amazonTags = $chapleanMailerConfig['amazon_tags'];
            $headers->addTextHeader('X-SES-CONFIGURATION-SET', $amazonTags['configuration_set']);
            $headers->addTextHeader(
                'X-SES-MESSAGE-TAGS',
                sprintf(
                    'project_name=%s, environment=%s',
                    $amazonTags['project_name'],
                    $amazonTags['env']
                )
            );
        }


        if (isset($chapleanMailerConfig['bounce_address'])) {
            $this->setReturnPath($chapleanMailerConfig['bounce_address']);
        }

        $this->setFrom($chapleanMailerConfig['sender_address'], $chapleanMailerConfig['sender_name']);

        $this->chapleanMailerConfig = $chapleanMailerConfig;
        $this->time = microtime(true);

        $this->setBcc(array());
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
        $subject = $prefix . ' ' . $subject;

        if (!$this->_setHeaderFieldModel('Subject', $subject)) {
            $this->getHeaders()->addTextHeader('Subject', $subject);
        }

        return $this;
    }


    /**
     * Add a To: address to this message.
     *
     * If $name is passed this name will be associated with the address.
     *
     * @param string $address
     * @param string $name    optional
     *
     * @return \Swift_Mime_SimpleMessage
     */
    public function addTo($address, $name = null)
    {
        $current = $this->getTo();
        $current[$address] = $name;

        return $this->setTo($current);
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
        if (!is_array($addresses)) {
            $addresses = array($addresses => $name);
        }

        $addresses = $this->transformMail($addresses);

        if (!$this->_setHeaderFieldModel('To', (array) $addresses)) {
            $this->getHeaders()->addMailboxHeader('To', (array) $addresses);
        }

        return $this;
    }

    /**
     * Add a Cc: address to this message.
     *
     * If $name is passed this name will be associated with the address.
     *
     * @param string $address
     * @param string $name    optional
     *
     * @return \Swift_Mime_SimpleMessage
     */
    public function addCc($address, $name = null)
    {
        $current = $this->getCc();
        $current[$address] = $name;

        return $this->setCc($current);
    }

    /**
     * Set the Cc addresses of this message.
     *
     * If $name is passed and the first parameter is a string, this name will be
     * associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return \Swift_Mime_SimpleMessage
     */
    public function setCc($addresses, $name = null)
    {
        if (!is_array($addresses)) {
            $addresses = array($addresses => $name);
        }

        $addresses = $this->transformMail($addresses);

        if (!$this->_setHeaderFieldModel('Cc', (array) $addresses)) {
            $this->getHeaders()->addMailboxHeader('Cc', (array) $addresses);
        }

        return $this;
    }

    /**
     * Add a Bcc: address to this message.
     *
     * If $name is passed this name will be associated with the address.
     *
     * @param string $address
     * @param string $name    optional
     *
     * @return \Swift_Mime_SimpleMessage
     */
    public function addBcc($address, $name = null)
    {
        $current = $this->getBcc();
        $current[$address] = $name;

        return $this->setBcc($current);
    }

    /**
     * Set the Bcc addresses of this message.
     *
     * If $name is passed and the first parameter is a string, this name will be
     * associated with the address.
     *
     * @param mixed  $addresses
     * @param string $name      optional
     *
     * @return \Swift_Mime_SimpleMessage
     */
    public function setBcc($addresses, $name = null)
    {
        if (!is_array($addresses)) {
            $addresses = array($addresses => $name);
        }

        $extraAddress = $this->getExtraBccAddress();
        $addresses = $this->transformMail($addresses);
        $addresses = array_merge($addresses, $extraAddress);

        if (!$this->_setHeaderFieldModel('Bcc', (array) $addresses)) {
            $this->getHeaders()->addMailboxHeader('Bcc', (array) $addresses);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time * 10000;
    }

    /**
     * Yopmailization of mail addresses when in dev env
     *
     * @param array $addresses addresses to transform
     *
     * @return array Addresses transformed if necessary
     */
    public function transformMail(array $addresses)
    {
        $test = $this->chapleanMailerConfig['test'];

        if ($test) {
            $finalAddresses = array();

            foreach ($addresses as $recipient => $recipientName) {
                $addressToUpdate = is_string($recipient) ? $recipient : $recipientName;
                $newRecipient = $addressToUpdate;

                // If not already transformed
                if (!in_array($addressToUpdate, $this->getExtraBccAddress(), true) && substr($addressToUpdate, -strlen('@yopmail.com')) !== '@yopmail.com'){
                    $newRecipient = str_replace(array('.', '@'), '_', $addressToUpdate);
                    $newRecipient =  substr($newRecipient, 0, 25);
                    $newRecipient .=  '@yopmail.com';
                }

                $finalAddresses[$newRecipient] = $recipientName;
            }

            $addresses = $finalAddresses;
        }

        return $addresses;
    }

    /**
     * Return automatically added bcc address
     *
     * @return array
     */
    public function getExtraBccAddress()
    {
        $extraAddress = array();
        if (isset($this->chapleanMailerConfig['bcc_address'])) {
            $extraAddress[$this->chapleanMailerConfig['bcc_address']] = $this->chapleanMailerConfig['bcc_address'];
        }

        return $extraAddress;
    }
}
