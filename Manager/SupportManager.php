<?php
namespace Innova\SupportBundle\Manager;

/**
 * Class SupportManager
 */
class SupportManager
{
    /**
     * Mailer service
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * Class constructor
     * @param \Swift_Mailer $mailer
     */
    public function __construct(
        \Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send a new message to support team (it will never happen because we don't code bugs!)
     * @param string $supportEmail
     * @param string $userEmail
     * @param string $subject
     * @param string $content
     */
    public function sendRequest($supportEmail, $userEmail, $subject, $content)
    {
        // Create message to send
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject)
                ->setFrom($userEmail)
                ->setTo($supportEmail)
                ->setBody($content)
        ;

        // Send mail
        $this->mailer->send($message);
    }
}