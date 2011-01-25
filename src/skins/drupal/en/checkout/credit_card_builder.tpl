{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Autogenerated credit card form
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<form action="{cart.paymentMethod.getFormURL(cart)}" method="{cart.paymentMethod.getFormMethod()}" name="cc_form" class="cc-auto-form">
  <input FOREACH="cart.paymentMethod.getFields(cart),name,value" type="hidden" name="{name}" value="{value:r}" />
</form>
