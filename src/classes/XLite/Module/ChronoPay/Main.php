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
 * @subpackage Core
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\ChronoPay;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Main extends \XLite\Module\AModule
{
    /**
     * Module type
     *
     * @var    int
     * @access protected
     * @since  3.0
     */
    public function getModuleType()
    {
        return self::MODULE_PAYMENT;
    }

    /**
     * Module version
     *
     * @var    string
     * @access protected
     * @since  3.0
     */
    public function getVersion()
    {
        return '3.0';
    }

    /**
     * Module description
     *
     * @var    string
     * @access protected
     * @since  3.0
     */
    public function getDescription()
    {
        return 'ChronoPay WEB payment gateway';
    }

    /**
     * Determines if we need to show settings form link
     *
     * @return bool
     * @access public
     * @since  3.0
     */
    public function showSettingsForm()
    {
        return true;
    }

    /**
     * Return link to settings form
     *
     * @return string
     * @access public
     * @since  3.0
     */
    public function getSettingsForm()
    {
       return 'admin.php?target=payment_method&payment_method=chronopay';
    }

    /**
     * Perform some actions at startup
     *
     * @return void
     * @access public
     * @since  3.0
     */
    public function init() 
    {
        parent::init();

        
        $this->registerPaymentMethod('chronopay');
    }

    function uninstall() 
    {
        func_cleanup_cache('skins');
        func_cleanup_cache('classes');

        parent::uninstall();
    }
    
}
