<?php

namespace Innova\SupportBundle\Controller;

use Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler;
use Innova\SupportBundle\Manager\SupportManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class SupportController
 *
 * @category   Controller
 * @package    Innova
 * @subpackage SupportBundle
 * @author     Innovalangues <contact@innovalangues.net>
 * @copyright  2013 Innovalangues
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @version    0.1
 * @link       http://innovalangues.net
 *
 * @Route(
 *      "support",
 *      name    = "innova_support",
 *      service = "innova_support.controller.support"
 * )
 */
class SupportController
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * Current security context
     * @var SecurityContextInterface|\Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * Translator service
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * Configuration of the platform
     * @var \Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler
     */
    private $configHandler;

    /**
     * @var \Innova\SupportBundle\Manager\SupportManager
     */
    private $supportManager;

    /**
     * Current request
     * @var
     */
    private $request;

    /**
     * Class constructor
     * @param \Symfony\Component\Form\FormFactoryInterface                             $formFactory
     * @param \Symfony\Component\Security\Core\SecurityContextInterface                $securityContext
     * @param \Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler $configHandler
     * @param \Innova\SupportBundle\Manager\SupportManager                             $supportManager
     */
    public function __construct(
        FormFactoryInterface         $formFactory,
        SecurityContextInterface     $securityContext,
        TranslatorInterface          $translator,
        PlatformConfigurationHandler $configHandler,
        SupportManager               $supportManager)
    {
        $this->formFactory    = $formFactory;
        $this->security       = $securityContext;
        $this->translator = $translator;
        $this->configHandler  = $configHandler;
        $this->supportManager = $supportManager;
    }

    /**
     * Inject current request
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Innova\SupportBundle\Controller\SupportController
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @Route(
     *      "/new",
     *      name = "innova_support_new"
     * )
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        // Create form
        $form = $this->createFrom();

        return array (
            'form' => $form->createView(),
        );
    }

    /**
     * @Route(
     *      "",
     *      name = "innova_support_send"
     * )
     * @Method("POST")
     * @Template("InnovaSupportBundle:Support:new.html.twig")
     */
    public function sendAction()
    {
        // Create form
        $form = $this->createFrom();

        // Handle request
        $form->handleRequest($this->request);
        if ($form->isValid()) {
            // Check if support email is defined
            $platformConfig = $this->configHandler->getPlatformConfig();

            $supportEmail = $platformConfig->getSupportEmail();
            if (!empty($supportEmail)) {
                $userEmail = $form->get('userEmail')->getData();
                $subject = $form->get('subject')->getData();
                $content = $form->get('content')->getData();

                // Send support request
                $this->supportManager->sendRequest($supportEmail, $userEmail, $subject, $content);
            }
            else {
                // Display error to user
                $form->addError(new FormError($this->translator->trans('no_support_email')));
            }
        }

        return array (
            'form' => $form->createView(),
        );
    }

    private function createFrom()
    {
        // Retrieve user
        $user = $this->security->getToken()->getUser();

        $data = array (
            'userEmail' => $user->getMail(),
        );
        $form = $this->formFactory->create('innova_support', $data);

        return $form;
    }
}