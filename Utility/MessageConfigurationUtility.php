<?php

namespace Chaplean\Bundle\MailerBundle\Utility;

/**
 * Class MessageConfigurationUtility.
 *
 * @package   Tests\Chaplean\Bundle\MailerBundle\Utility
 * @author    Valentin - Chaplean <valentin@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     4.0.0
 */
class MessageConfigurationUtility
{
    const EXTENSION_YOPMAIL = '@yopmail.com';

    /**
     * @var array
     */
    private $config;

    /**
     * MessageConfigurationUtility constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    public function applyAmazonTags(\Swift_Message $message)
    {
        if (!array_key_exists('amazon_tags', $this->config)) {
            return $message;
        }

        $amazonTags = $this->config['amazon_tags'];
        $headers = $message->getHeaders();
        if ($headers->get('X-SES-CONFIGURATION-SET') === null) {
            $headers->addTextHeader('X-SES-CONFIGURATION-SET', $amazonTags['configuration_set']);
        }
        if ($headers->get('X-SES-MESSAGE-TAGS') === null) {
            $headers->addTextHeader(
                'X-SES-MESSAGE-TAGS',
                sprintf(
                    'project_name=%s, environment=%s',
                    $amazonTags['project_name'],
                    $amazonTags['env']
                )
            );
        }

        return $message;
    }

    /**
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    public function applyBccAddress(\Swift_Message $message)
    {
        if ($this->config['bcc_address'] === null) {
            return $message;
        }

        return $message->addBcc($this->config['bcc_address']);
    }

    /**
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    public function applyBounceAddress(\Swift_Message $message)
    {
        return $message->setReturnPath($this->config['bounce_address']);
    }

    /**
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    public function applyFrom(\Swift_Message $message)
    {
        return $message->setFrom($this->config['sender_address'], $this->config['sender_name']);
    }

    /**
     * Prefix subject with prefix in configuration
     *
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    public function applySubjectPrefix(\Swift_Message $message)
    {
        $subject = $message->getSubject();
        $prefix = $this->config['subject']['prefix'];
        if (strpos($subject, $prefix) === 0) {
            return $message;
        }

        $subject = $prefix . ' ' . $message->getSubject();

        return $message->setSubject($subject);
    }

    /**
     * Yopmailization of mail addresses when in dev env
     *
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    public function applyYopmail(\Swift_Message $message)
    {
        if (!((bool) $this->config['test'])) {
            return $message;
        }

        $message = $this->applyYopmailOn('To', $message);
        $message = $this->applyYopmailOn('Cc', $message);

        return $message;
    }

    /**
     * Yopmailize addresses by type (ex: To, Cc, Bcc, From)
     *
     * @param string         $type
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    private function applyYopmailOn($type, \Swift_Message $message)
    {
        $getter = 'get' . $type;
        $addresses = $message->$getter();

        if (!is_array($addresses)) {
            return $message;
        }

        $finalAddresses = [];
        foreach ($addresses as $recipient => $recipientName) {
            $addressToUpdate = is_string($recipient) ? $recipient : $recipientName;
            $newRecipient = $addressToUpdate;

            // If not already transformed
            if (substr($addressToUpdate, -strlen(self::EXTENSION_YOPMAIL)) !== self::EXTENSION_YOPMAIL) {
                $newRecipient = str_replace(['.', '@'], '_', $addressToUpdate);
                $newRecipient = substr($newRecipient, 0, 25);
                $newRecipient .= '@yopmail.com';
            }

            $finalAddresses[$newRecipient] = $recipientName;
        }

        $setter = 'set' . $type;

        return $message->$setter($finalAddresses);
    }

    /**
     * Warning can be called multiple times per message!
     * see https://github.com/swiftmailer/swiftmailer/issues/139
     *
     * @param \Swift_Message $message
     *
     * @return \Swift_Message
     */
    public function applyAll(\Swift_Message $message)
    {
        $message = $this->applyAmazonTags($message);
        $message = $this->applyBccAddress($message);
        $message = $this->applyBounceAddress($message);
        $message = $this->applyFrom($message);
        $message = $this->applySubjectPrefix($message);
        $message = $this->applyYopmail($message);

        return $message;
    }
}
