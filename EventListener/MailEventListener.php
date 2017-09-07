<?php

namespace Chaplean\Bundle\MailerBundle\EventListener;

use Chaplean\Bundle\MailerBundle\Utility\EmailConfigurationUtility;
use Chaplean\Bundle\MailerBundle\Utility\MessageConfigurationUtility;
use Psr\Log\LoggerInterface;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;

/**
 * Class MailEventListener.
 *
 * @author    Matthias - Chaplean <matthias@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     3.0.3
 */
class MailEventListener implements Swift_Events_SendListener
{
    /**
     * @var EmailConfigurationUtility
     */
    protected $emailConfigurationUtility;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var MessageConfigurationUtility
     */
    protected $messageConfigurationUtility;

    /**
     * MailLoggingEventListener constructor.
     *
     * @param LoggerInterface             $logger
     * @param MessageConfigurationUtility $messageConfigurationUtility
     * @param EmailConfigurationUtility   $emailConfigurationUtility
     */
    public function __construct(LoggerInterface $logger, MessageConfigurationUtility $messageConfigurationUtility, EmailConfigurationUtility $emailConfigurationUtility)
    {
        $this->emailConfigurationUtility = $emailConfigurationUtility;
        $this->logger = $logger;
        $this->messageConfigurationUtility = $messageConfigurationUtility;
    }

    /**
     * @param \Swift_Events_SendEvent $event
     *
     * @return void
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $event)
    {
        /** @var \Swift_Message $message */
        $message = $event->getMessage();

        foreach (['To', 'Bcc', 'Cc'] as $type) {
            $getter = 'get' . $type;
            $setter = 'set' . $type;

            $addresses = $message->$getter();
            if (!is_array($addresses)) {
                break;
            }

            $message->$setter($this->emailConfigurationUtility->removeEmailDisabled($addresses));
        }

        $this->messageConfigurationUtility->applyAll($message);
    }

    /**
     * @param \Swift_Events_SendEvent $event
     *
     * @return void
     */
    public function sendPerformed(Swift_Events_SendEvent $event)
    {
        $transport = 'Mail unknown transport';

        if ($event->getTransport() instanceof \Swift_Transport_SpoolTransport) {
            $transport = 'Mail queued';
        } elseif ($event->getTransport() instanceof \Swift_Transport_AbstractSmtpTransport) {
            $transport = 'Mail sent';
        }

        $this->logger->info($transport . ': ' . $event->getMessage()->toString());
    }
}
