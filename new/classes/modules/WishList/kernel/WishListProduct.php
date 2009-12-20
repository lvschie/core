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
* Wishlist Product class.
*
* @package Module_Wishlist
* @access public
* @version $Id$	
*/

class WishListProduct extends Base // {{{
{
	var $product 	= null;
	var $orderItem 	= null;
   	
	var $fields = array (
		"item_id"		=>  0,
		"wishlist_id"	=>	0,
		"product_id"	=> 	0,
		"amount"		=>  0,
		"purchased"		=> 	0,
		"options"		=> 	0,
		"order_by"		=>	0);
		
	var $alias 			= "wishlist_products";
	var $defaultOrder 	= "order_by";
	var $primaryKey 	= array("item_id","wishlist_id");

	function &getProduct() // {{{ 
	{
		if (is_null($this->product)) {
			$this->product = & func_new("Product",$this->get("product_id"));
		}	
		return $this->product;	
		
	} // }}}
	
	function &getOrderItem() // {{{
	{
        if (is_null($this->orderItem)) {
            $this->orderItem = & func_new("OrderItem");
            $this->orderItem->set("product",$this->get("product"));
		}										        
		
		return $this->orderItem;										
		
	} // }}}

    function changeOrderItem(&$orderItem) // {{{
    {
        $isChanged = false;
        if ($this->xlite->get("WholesaleTradingEnabled")) {
            $orderItem->set("amount", $this->get("amount"));
            $isChanged = true;
        }
        if ($this->hasOptions()) {
            $orderItem->set("options", serialize($this->getProductOptions()));
            $isChanged = true;
        }

        return $isChanged;
    } // }}}

	function &get($name) // {{{
	{
		switch($name) {
		    case "listPrice" :
			case "price" :
            case "weight":
				$orderItem = $this->get("orderItem");
                if ($this->changeOrderItem($orderItem)) {
					return $orderItem->get($name);	
				}	
			case "name" : 
			case "brief_description" :	
			case "sku" :
	        case "description" :	
				return $this->get("product.$name");
		}
		return parent::get($name);
	} // }}}

	function &getImageURL() // {{{
	{
		return $this->get("product.imageURL");
	} // }}}

	function getUrl() // {{{
	{
		return CART_SELF . "?target=product&product_id=" . $this->get("product_id");	
	} // }}}

	function &getTotal() // {{{
	{
		return $this->get("price")*$this->get("amount");
	} // }}}
	
	function &hasImage() // {{{
	{
		$product = $this->get("product");
		return $this->product->hasImage();
 	} // }}}
	
	function &hasOptions() // {{{ 
	{
		return $this->get("options");
	} // }}}
	
	/* returns two-dimensional array like this
		( 
			([Color]=>Red, [Size]=>Medium),
			([Color]=>Green, [Size]=>Large)
		)
	*/
	function & getOptionExceptionsAsArray() {
		$exceptions = array();
		$product = $this->getProduct();
		foreach ($product->get("optionExceptions") as $oneException) {
			$tempArray = array();
			foreach (explode(";", $oneException->get("exception")) as $exceptionElement) {
				list($class, $option) = explode("=", $exceptionElement);
				$tempArray[$class] = $option;
			}
			$exceptions[] = $tempArray;
		}
		return $exceptions;
	}
	
	function & getSelectedOptionsAsArray() {
		$selectedOptions = $this->getProductOptions();
		$result = array();
		foreach ($selectedOptions as $selectedOption) {
			$result[$selectedOption->class] = $selectedOption->option;
		}
		return $result;
	}
	
	// bool
	function isOptionsInvalid() {
		if (!$this->xlite->get("ProductOptionsEnabled")) {
			// if ProductOptions disabled - all options are valid
			return false;
		}
		$exceptions = $this->getOptionExceptionsAsArray();
		$selectedOptions = $this->getSelectedOptionsAsArray();
		foreach ($exceptions as $exception) {
			$stillInvalid = true;
			foreach ($exception as $class => $option) {
				if ((!isset($selectedOptions[$class])) || ($selectedOptions[$class] != $option)) {
					$stillInvalid = false;
				}
			}			
			if ($stillInvalid) {
				return true;
			}
		}
		return false;
	}
	
	function isOptionsExist() {
		if (!$this->xlite->get("ProductOptionsEnabled")) {
			// if ProductOptions disabled - all options are exists
			return true;
		}
		$product = & $this->getProduct();
		$selectedOptions = & $this->getSelectedOptionsAsArray();
		
		$result = true;
		foreach ($product->getProductOptions() as $productOptions) {
			if ($productOptions->get("opttype") == "Text" || $productOptions->get("opttype") == "Textarea") 
				continue;

			$class = $productOptions->get("optclass");
			if (isset($selectedOptions[$class])) {
				// check that pruduct options still have this option
				$option = $selectedOptions[$class];
				$options = $productOptions->get("options");
				// $options - string like this - "Green\n\Blue\nRed" or "Green\r\nBlue\r\nRed"
				if (!preg_match("/(\r|(\r\n)|\n)?$option(\r|(\r\n)|\n)?/", $options)) {
					$result = false;
					break;
				}
			}			
		}
		return $result;
	}
	
   	function &getProductOptions() // {{{
	{
		$options = $this->get("options");
		if(empty($options)) 
			return array();
		else
			return unserialize($options);

	} // }}}

    function setProductOptions(&$options) // {{{ 
    {
        $orderItem = $this->get("orderItem");
        $orderItem->setProductOptions($options);
        $this->set("options",$orderItem->get("options"));
    } // }}}

} // }}}

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
