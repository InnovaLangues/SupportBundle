<?php

namespace Innova\SupportBundle\Controller;

use Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler;
use Innova\SupportBundle\Manager\SupportManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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
     * Form factory
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * Current session
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    /**
     * Security Token
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $securityToken
     */
    protected $securityToken;

    /**
     * Router
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

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
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $securityToken
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler $configHandler
     * @param \Innova\SupportBundle\Manager\SupportManager $supportManager
     */
    public function __construct(
        FormFactoryInterface         $formFactory,
        SessionInterface             $session,
        TokenStorageInterface        $securityToken,
        RouterInterface              $router,
        TranslatorInterface          $translator,
        PlatformConfigurationHandler $configHandler,
        SupportManager               $supportManager)
    {
        $this->formFactory    = $formFactory;
        $this->session        = $session;
        $this->securityToken  = $securityToken;
        $this->router         = $router;
        $this->translator     = $translator;
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

                try {
                    // Send support request
                    $this->supportManager->sendRequest($supportEmail, $userEmail, $subject, $content);

                    $this->session->getFlashBag()->add(
                        'success',
                        $this->translator->trans('support_send_success', array())
                    );
                }
                catch (\Exception $e) {
                    $this->session->getFlashBag()->add(
                        'error',
                        $this->translator->trans('support_send_error', array())
                    );
                }

                // Redirect to form
                $url = $this->router->generate('innova_support_new');

                return new RedirectResponse($url);
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
        // Retrieve user mail, if not anonymous
        $user = $this->securityToken->getToken()->getUser();
        $mail = method_exists($user, 'getMail') ? $user->getMail() : "";

        $data = array(
            'userEmail' => $mail,
        );
        $form = $this->formFactory->create('innova_support', $data);

        return $form;
    }
}