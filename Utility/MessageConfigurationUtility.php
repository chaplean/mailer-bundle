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
        $headers->addTextHeader('X-SES-CONFIGURATION-SET', $amazonTags['configuration_set']);
        $headers->addTextHeader(
            'X-SES-MESSAGE-TAGS',
            sprintf(
                'project_name=%s, environment=%s',
                $amazonTags['project_name'],
                $amazonTags['env']
            )
        );

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
        $prefix = $this->config['subject']['prefix'];
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

        $finalAddresses = array();

        foreach (['To', 'Cc'] as $type) {
            $getter = 'get' . $type;
            $setter = 'set' . $type;
            $addresses = $message->$getter();

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

            $message->$setter($finalAddresses);
        }

        return $message;
    }

    /**
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
