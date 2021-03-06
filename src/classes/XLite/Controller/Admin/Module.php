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
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Controller\Admin;

/**
 * Module settings
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Module extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Module object
     *
     * @var   mixed
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $module;

    /**
     * handleRequest
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function handleRequest()
    {
        if (!$this->getModuleID()) {
            $this->setReturnURL($this->buildURL('addons_list_installed'));
        }

        parent::handleRequest();
    }

    /**
     * Return current module options
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Config')
            ->getByCategory($this->getModule()->getActualName(), true, true);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getTitle()
    {
        return static::t(
            'X module settings',
            array(
                'name'   => $this->getModule()->getName(),
                'author' => $this->getModule()->getAuthor(),
            )
        );
    }

    /**
     * Return current module object
     *
     * @return \XLite\Model\Module
     * @throws \Exception
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getModule()
    {
        if (!isset($this->module)) {
            $this->module = \XLite\Core\Database::getRepo('\XLite\Model\Module')->find($this->getModuleID());

            if (!$this->module) {
                throw new \Exception('Add-on does not exist (ID#' . $this->getModuleID() . ')');
            }
        }

        return $this->module;
    }

    /**
     * Get current module ID
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getModuleID()
    {
        return \XLite\Core\Request::getInstance()->moduleId;
    }

    /**
     * Update module settings
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');

        $this->setReturnURL(\XLite\Core\Request::getInstance()->return ?: $this->buildURL('addons_list_installed'));
    }

    /**
     * getModelFormClass
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Settings';
    }
}
