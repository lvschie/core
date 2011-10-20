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

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\Country
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class Country extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget param names
     */
    const PARAM_ALL               = 'all';
    const PARAM_STATE_SELECTOR_ID = 'stateSelectorId';
    const PARAM_STATE_INPUT_ID    = 'stateInputId';

    /**
     * Display only enabled countries
     *
     * @var   boolean
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $onlyEnabled = true;

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __construct(array $params = array())
    {
        if (!empty($params[self::PARAM_ALL])) {
            $this->onlyEnabled = false;
        }

        parent::__construct($params);
    }

    /**
     * Register JS files
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/select_country.js';

        return $list;
    }

    /**
     * Pass the DOM Id fo the "States" selectbox
     * NOTE: this function is public since it's called from the View_Model_Profile_AProfile class
     *
     * @param string $selectorId DOM Id of the "States" selectbox
     * @param string $inputId    DOM Id of the "States" inputbox
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setStateSelectorIds($selectorId, $inputId)
    {
        $this->getWidgetParams(self::PARAM_STATE_SELECTOR_ID)->setValue($selectorId);
        $this->getWidgetParams(self::PARAM_STATE_INPUT_ID)->setValue($inputId);
    }


    /**
     * Return field template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getFieldTemplate()
    {
        return 'select_country.tpl';
    }

    /**
     * Define widget parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ALL               => new \XLite\Model\WidgetParam\Bool('All', false),
            self::PARAM_STATE_SELECTOR_ID => new \XLite\Model\WidgetParam\String('State select ID', null),
            self::PARAM_STATE_INPUT_ID    => new \XLite\Model\WidgetParam\String('State input ID', null),
        );
    }

    /**
     * Get selector default options list
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultOptions()
    {
        return $this->onlyEnabled
            ? \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabled()
            : \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllCountries();
    }

    /**
     * getDefaultValue
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultValue()
    {
        return \XLite\Core\Config::getInstance()->General->default_country;
    }

    /**
     * Some JavaScript code to insert
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getInlineJSCode()
    {
        return 'jQuery(document).ready(function() { '
            . 'stateSelectors[\'' . $this->getFieldId() . '\'] = new StateSelector('
            . '\'' . $this->getFieldId() . '\', '
            . '\'' . $this->getParam(self::PARAM_STATE_SELECTOR_ID) . '\', '
            . '\'' . $this->getParam(self::PARAM_STATE_INPUT_ID) . '\'); });' . PHP_EOL
            . $this->getWidget(array(), '\XLite\View\JS\StatesList')->getContent();
    }
}
