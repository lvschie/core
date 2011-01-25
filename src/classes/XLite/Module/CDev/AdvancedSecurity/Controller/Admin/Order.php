<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\AdvancedSecurity\Controller\Admin;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    function action_submit_password()
    {
        $gpg = new \XLite\Module\CDev\AdvancedSecurity\Model\GPG();
        if ($gpg->isPasswordValid($this->get('master_password'))) {
            $this->session->set('masterPassword', $this->get('master_password'));
        } else {
            $this->set('valid', false);
            $this->set('invalidMasterPassword', true);
        }
    }

    function action_clear_password()
    {
        $this->session->set('masterPassword', null);
    }

    function getGPG()
    {
        if (is_null($this->gpg)) {
            $this->gpg = new \XLite\Module\CDev\AdvancedSecurity\Model\GPG();
        }
        return $this->gpg;
    }
}
