<?php

/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URL: http://www.litecommerce.com/license.php                |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
|                                                                              |
| The Initial Developer of the Original Code is Ruslan R. Fazliev              |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2003 Creative        |
| Development. All Rights Reserved.                                            |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* AntiFraud module base class.
*
* @package Module_AntiFraud
* @access public
* @version $Id$
*/

class XLite_Module_AntiFraud_Main extends Module // {{{
{

	var $minVer = "2.0";
	var $isFree = true;
	var $showSettingsForm = true;

	function init() // {{{ 
	{
		parent::init();	

		$this->addDecorator("Order", "Module_AntiFraud_Order");
        $this->addDecorator("Country", "Module_AntiFraud_Country");

		if ($this->xlite->is("adminZone")) {
			 $this->addDecorator("Admin_Dialog_Order", "Module_AntiFraud_Admin_Dialog_order");
			 $this->addDecorator("Admin_Dialog_order_list", "Module_AntiFraud_Admin_Dialog_order_list");
			 $this->addDecorator("Admin_Dialog_module", "Admin_Dialog_module_AntiFraud");
			 $this->addDecorator("Admin_Dialog_countries", "Module_AntiFraud_Admin_Dialog_countries");
		} else {
			 $this->addDecorator("Dialog_profile", "Dialog_profile_module_AntiFraud");
			 $this->addDecorator("Dialog_callback", "Dialog_callback_module_AntiFraud");
		}
		$this->xlite->set("AntiFraudEnabled",true);

	} // }}} 
	
	function uninstall() // {{{ 
	{
		func_cleanup_cache("classes");
		func_cleanup_cache("skins");
		parent::uninstall();

	} // }}}

} // }}} 

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.



?>
