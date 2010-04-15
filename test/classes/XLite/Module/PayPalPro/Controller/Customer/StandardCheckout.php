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
class XLite_Module_PayPalPro_Controller_Customer_StandardCheckout extends XLite_Controller_Customer_Checkout implements XLite_Base_IDecorator
{	
	public $registerForm = null;

	public function __construct(array $params)
	{
		parent::__construct($params);
		if (($_REQUEST["target"] == "checkout") && (isset($_REQUEST["paypal_result"]) && $_REQUEST["paypal_result"] == "cancel")) {
			$this->cart = XLite_Model_Cart::getInstance();
			if ($this->cart->get("status") == "Q") {
				// revert back to I if the payment is cancelled at the PayPal side
				$this->cart->set("status", "I");
				$this->updateCart();
			}
		}
	}

	function init() // {{{
	{
    	if ($_REQUEST["target"] == "standard_checkout") {
			$this->registerForm = new XLite_Base();
		}
		parent::init();									  
	} // }}}
	
	function action_redirect() // {{{ 
	{
    	$itemsBeforeUpdate = $this->cart->get("itemsFingerprint");
		$this->cart->set("status", "Q");
        $this->updateCart();
    	$itemsAfterUpdate = $this->cart->get("itemsFingerprint");
		if ($this->get("absence_of_product") || $this->cart->isEmpty() || $itemsAfterUpdate != $itemsBeforeUpdate) {
			$this->cart->set("status", "I");
			$this->updateCart();
			$this->session->set("order_id", $this->cart->get("order_id"));
			$this->set("absence_of_product", true);
			$this->set("returnUrl", "cart.php?target=cart");
			$this->redirect();
			return;
		}
		$pm = $this->cart->get("PaymentMethod");
		$profile = $this->cart->get("profile");
?>
<HTML>
<BODY onload="javascript: document.paypal_form.submit();">
<FORM name="paypal_form" action="<?php echo $pm->get("standardUrl"); ?>" method="POST">
<INPUT type=hidden name=cmd value="_ext-enter">
<INPUT type=hidden name=invoice value="<?php echo $pm->getComplex('params.standard.prefix'); ?><?php echo $this->cart->get("order_id"); ?>">
<INPUT type=hidden name=redirect_cmd value="_xclick">
<INPUT type=hidden name=mrb value="R-2JR83330TB370181P">
<INPUT type=hidden name=pal value="RDGQCFJTT6Y6A">
<INPUT type=hidden name=rm value="2">
<INPUT type=hidden name="email" value="<?php echo $profile->get("login"); ?>">
<INPUT type=hidden name=first_name value="<?php echo $profile->get("billing_firstname"); ?>">
<INPUT type=hidden name=last_name value="<?php echo $profile->get("billing_lastname"); ?>">
<INPUT type=hidden name=address1 value="<?php echo $profile->get("billing_address"); ?>">
<INPUT type=hidden name=city value="<?php echo $profile->get("billing_city"); ?>">
<INPUT type=hidden name=state value="<?php echo $pm->getBillingState($this->cart); ?>">
<INPUT type=hidden name=country value="<?php echo $profile->get("billing_country"); ?>">
<INPUT type=hidden name=zip value="<?php echo $profile->get("billing_zipcode"); ?>">
<INPUT type=hidden name=day_phone_a value="<?php echo $pm->getPhone($this->cart,"a"); ?>">
<INPUT type=hidden name=day_phone_b value="<?php echo $pm->getPhone($this->cart,"b"); ?>">
<INPUT type=hidden name=day_phone_c value="<?php echo $pm->getPhone($this->cart,"c"); ?>">
<INPUT type=hidden name=night_phone_a value="<?php echo $pm->getPhone($this->cart,"a"); ?>"> 
<INPUT type=hidden name=night_phone_b value="<?php echo $pm->getPhone($this->cart,"b"); ?>">
<INPUT type=hidden name=night_phone_c value="<?php echo $pm->getPhone($this->cart,"c"); ?>">
<INPUT type=hidden name=business value="<?php echo $pm->getComplex('params.standard.login'); ?>">
<INPUT type=hidden name=item_name value="<?php echo $pm->getItemName($this->cart); ?>">
<INPUT type=hidden name=amount value="<?php echo $this->cart->get("total"); ?>">
<INPUT type=hidden name=currency_code value="<?php echo $pm->getComplex('params.standard.currency'); ?>">
<INPUT type="hidden" name="bn" value="x-cart">
<INPUT type=hidden name=return value="<?php echo $this->getShopUrl("cart.php?target=checkout", $this->getComplex('config.Security.customer_security')); ?>&action=return&order_id=<?php echo $this->cart->get("order_id"); ?>">
<INPUT type=hidden name=cancel_return value="<?php echo $this->getShopUrl("cart.php?target=checkout", $this->getComplex('config.Security.customer_security')); ?>&paypal_result=cancel">
<INPUT type=hidden name=notify_url value="<?php echo $this->getShopUrl("cart.php?target=callback"); ?>&action=callback&order_id=<?php echo $this->cart->get("order_id"); ?>">
<INPUT type=hidden name=image_url value="<?php echo $pm->getComplex('params.standard.logo'); ?>">
<CENTER>Please wait while connecting to <b>PayPal</b> payment gateway...</CENTER>
</FORM>
</BODY>
</HTML>
<?php
		exit;		
	} // }}} 

    function isSecure() // {{{ 
    {
		return $this->getComplex('config.Security.customer_security');
	} // }}} 
						
} // }}}
