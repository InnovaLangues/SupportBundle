<?php

namespace Innova\SupportBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
    public function newAction()
    {

    }
}