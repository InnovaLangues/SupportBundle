<?php
namespace Innova\SupportBundle\Manager;

use Symfony\Component\Security\Core\SecurityContextInterface;

class SupportManager
{
    /**
     * Current security context
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $security;

    /**
     * @var
     */
    protected $mailer;

    /**
     * Class constructor
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        \Swift_Mailer $mailer)
    {
        $this->security = $securityContext;
        $this->mailer = $mailer;
    }

    /**
     * Send a new message to support team (it will never happen because we don't code bugs!)
     * @param $message string
     */
    public function sendRequest($message)
    {
        /*$message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody($this->renderView('HelloBundle:Hello:email.txt.twig', array('name' => $name)))
        ;
        $this->mailer->send($message);*/
    }
}