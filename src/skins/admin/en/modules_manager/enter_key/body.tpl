{**
 * Modules enter key form
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}

<div class="enter-addon-key-form">

  <form action="admin.php" method="post" name="getAddonForm" >
    <input type="hidden" name="target" value="module_installation" />
    <input type="hidden" name="action" value="register_key" />

    <div class="enter-key-hint">
      {t(#If you have a license key for commercial module you may enter them here to register a purchase of the appropriate module.#)}
    </div>

    <div class="addon-key">
      <input type="text" name="key" value="" />
    </div>

    <widget class="\XLite\View\Button\Submit" label="{t(#Validate key#)}" />

    <div class="clear"></div>

  </form>

</div>
