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
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class XLite_Controller_Admin_Categories extends XLite_Controller_Admin_Abstract
{
    public $params = array('target', 'category_id');
    
    protected function getCategories()
    {
		return self::getCategory()->getSubcategories();
    }

    public function action_update()
    {
        $order_by = isset(XLite_Core_Request::getInstance()->category_order) 
			? XLite_Core_Request::getInstance()->category_order 
			: array();

        foreach ($order_by as $category_id => $category_order) {
            $category = new XLite_Model_Category($category_id);
            $category->set("order_by", $category_order);
            $category->update();
        }
    }

    public function action_delete()
    {
        foreach ($this->getCategories() as $category) {
            $category->delete();
        }
    }
}
