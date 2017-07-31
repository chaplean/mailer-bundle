<?php

namespace Chaplean\Bundle\MailerBundle\EventListener;

use Psr\Log\LoggerInterface;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;

/**
 * Class MailLoggingEventListener.
 *
 * @author    Matthias - Chaplean <matthias@chaplean.coop>
 * @copyright 2014 - 2017 Chaplean (http://www.chaplean.coop)
 * @since     3.0.3
 */
class MailLoggingEventListener implements Swift_Events_SendListener
{
    /** @var LoggerInterface  */
    protected $logger;

    /**
     * MailLoggingEventListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Swift_Events_SendEvent $evt
     *
     * @return void
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
    }

    /**
     * @param \Swift_Events_SendEvent $evt
     *
     * @return void
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $transport = 'Mail unknown transport';

        if ($evt->getTransport() instanceof \Swift_Transport_SpoolTransport) {
            $transport = 'Mail queued';
        } else if ($evt->getTransport() instanceof \Swift_Transport_AbstractSmtpTransport) {
            $transport = 'Mail sent';
        }

        $this->logger->info($transport . ': ' . $evt->getMessage()->toString());
    }
}
