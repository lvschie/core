<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
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
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* Implements product option base class
*
* @package Module_ProductOptions
* @access public
* @version $Id$
*/
class XLite_Module_ProductOptions_Model_ProductOption extends XLite_Model_Abstract
{	
    public $fields = array(
            "option_id"  		=> 0,
            "product_id" 		=> 0,
            "optclass"   		=> "",
            "opttext"    		=> "",
            "options"    		=> "",
            "opttype"    		=> "Text",
            "cols"       		=> 25,
            "rows"       		=> 4,
            "orderby"    		=> 0,
            "parent_option_id"	=> 0,
            "categories"		=> ""
            );	

    public $autoIncrement = "option_id";	
    public $alias = "product_options";	
    public $defaultOrder = "orderby";	

    public $importFields = array
    (
    	"sku"  => 0,
        "name" => 1,
        "optclass" => 2,
        "opttext" => 3,
        "options" => 4,
        "opttype" => 5,
        "cols" => 6,
        "rows" => 7,
        "orderby" => 8,
		"categories" => 9,
		"NULL" => -1,
	);

    function hasOptions($product_id) // {{{
    {
        $sql = "SELECT COUNT(*) FROM %s WHERE product_id=%d";
        $sql = sprintf($sql, $this->getTable(), $product_id);
        return (bool) $this->db->getOne($sql);
    } // }}}

    function isEmpty() // {{{
    {
        $options = $this->get("options");
        return (strlen(trim($options)) == 0) ? true : false;
    } // }}}

    function getProductOptions() // {{{
    {
        require_once LC_MODULES_DIR . 'ProductOptions' . LC_DS . 'encoded.php';
        if (is_null($this->productOptions)) {
            $this->productOptions = func_get_product_options($this);
        }    
        return $this->productOptions;
    } // }}}

    function _import(array $options) // {{{
    {
        static $line;
        if (!isset($line)) $line = 1; else $line++;

        $properties = $options["properties"];
        // prepare product options "options" value
        if (!empty($properties["options"])) {
            $properties["options"] = str_replace("|", "\r\n", $properties["options"]); 
        }
        $product = new XLite_Model_Product();
		$found = $product->findImportedProduct($properties["sku"],"",$properties["name"],false);

        print "<b>line# $line:</b> ";

		if (!is_null($found)) {
	        $product_option = new XLite_Module_ProductOptions_Model_ProductOption();
			$option_found = $product_option->find("product_id = " .$found->get("product_id"). " AND optclass='".addslashes($properties["optclass"])."'");
            $product_option->set("properties",$properties);
			$product_option->set("categories",null);
			if ($option_found) {	
				print "Updating product option for '<a href=\"admin.php?target=product&product_id=".$found->get("product_id"). "\">" .$found->get("name"). "</a>' product";	
				$product_option->update();	
			} else {
				print "Creating product option for '<a href=\"admin.php?target=product&product_id=".$found->get("product_id"). "\">" .$found->get("name"). "</a>' product";     
	            $product_option->set("product_id", $found->get("product_id"));
	            $product_option->create();
			}
		} else if (empty($properties["sku"]) && empty($properties["name"])) {
			$global_option = new XLite_Module_ProductOptions_Model_ProductOption();
			$global_found = $global_option->find("optclass='".addslashes($properties['optclass'])."' AND product_id=0");		
			if (!empty($properties["categories"])) {
				$cat = new XLite_Model_Category();
				foreach($cat->parseCategoryField($properties["categories"],true) as $path)
				{
					$category = $cat->findCategory($path);
					$categories[] = $category->get("category_id");
				}
				
			}

			$properties['categories'] = implode("|",!empty($categories) ? $categories : array());
			$global_option->set("properties",$properties);
			
			if ($global_found) {
				print "Updating '".$properties['optclass']."' global product option";
				$global_option->update();
			} else {
				print "Creating '".$properties['optclass']."' global product option";	
				$global_option->create();
			}	
            $product_id = array();
			if (!empty($categories)) {
				foreach($categories as $category_id) {
					$category = new XLite_Model_Category($category_id);
					$products = $category->get("products");
				}
			} else {
				$product = new XLite_Model_Product();
				$products = $product->findAll();
			}		
            foreach($products as $product) {
				$product_option = new XLite_Module_ProductOptions_Model_ProductOption();
	            $option_found = $product_option->find("product_id = " .$product->get("product_id"). " AND optclass='".addslashes($properties["optclass"])."'");

	            $product_option->set("properties",$properties);
				$product_option->set("product_id",$product->get("product_id"));
		        $product_option->set("categories",null);
				$product_option->set("parent_option_id",$global_option->get("option_id"));
				$option_found ? $product_option->update() : $product_option->create();
			}	
		} else {
			print "<font color=red>Product not found:</font>".(!empty($properties["sku"]) ? " SKU: ".$properties["sku"] : "") . (!empty($properties["name"]) ? " NAME: ".$properties["name"] : "");
		}	
		print "<br>";
    } // }}}

    function _export($layout, $delimiter) // {{{
    {
        $data = array();
        
        $product = new XLite_Model_Product($this->get("product_id"));
        $values = $this->get("properties");

        foreach ($layout as $field) {
            if ($field == "NULL") {
                $data[] = "";
            } elseif ($field == "sku" || $field == "name") {
                $data[] = $this->_stripSpecials($product->get($field));
            } elseif ($field == "options") {
                if (isset($values[$field])) {
                    $str = strpos($values[$field], "\n") !== false ? preg_replace("/(\\r\\n)|(\\n\\r)|(\\n)/", "|", $values[$field]) : $values[$field];
                    $data[] = $this->_stripSpecials($str);
                } else {
                    $data[] = "";
                }
            } elseif ($field == "categories") { 
				$this->getCategories();
				if (!empty($this->categories)) {
					foreach($this->categories as $category_id) {
						$category = new XLite_Model_Category($category_id);
						$categories[] = $category->get("stringPath");
					}	
					$data[] = implode("|",$categories);
				} else $data[] = "";
			} elseif (isset($values[$field])) {
                $data[] =  $this->_stripSpecials($values[$field]);
            }
        }
        return $data;
    } // }}} 

    function _modifiedPrice($opt, $ignoreProductPrice=false, $newProductPrice = null) // {{{ 
    {
        $product = new XLite_Model_Product($this->get("product_id"));
        if (!$ignoreProductPrice) {
			if (!is_null($newProductPrice)) {
				if ($product->get("price") != $newProductPrice) { // get() is required for reading the product from DB
					$product->set("price", $newProductPrice);
				}
			}
            if (!$this->config->getComplex('Taxes.prices_include_tax')) {
            	$productPrice = $product->get("listPrice");
            } else {
            	$productPrice = $product->get("price");
            }
        } else {
        	$productPrice = 0;
        }

    	$price = $opt->surcharge;

		if ($opt->percent) {
			$price = ($productPrice * $price) / 100;
		}

		$price = $productPrice + $price;

        if ($this->config->getComplex('Taxes.prices_include_tax')) {
        	$product->set("price", $this->formatCurrency($price));
        	$price = $product->get("listPrice");
        }

        if ($ignoreProductPrice) {
        	$price = abs($price);
        }

		return max(0, $price);
    } // }}}

    function modifiedPrice($opt, $ignoreProductPrice=false) // {{{
    {
		return abs($opt->surcharge);
    } // }}}

    function modifiedWeight($opt) // {{{
    {
      	return $opt->weight_modifier;
    } // }}} 

    function formatCurrency($price)
    {   
    	$isNewFC = $this->xlite->get("ProductOptionsNewFC");
    	if (!isset($isNewFC)) {
			$classMethods = array_map("strtolower", get_class_methods(get_parent_class(get_class($this))));
			$isNewFC = in_array("formatcurrency", $classMethods);
			$this->xlite->set("ProductOptionsNewFC", $isNewFC);
		}

		if ($isNewFC) {
			return parent::formatCurrency($price);
		} else {
        	return round($price, 2);
        }
    }               

	function update() // {{{ 
	{
		if ($this->xlite->get("InventoryTrackingEnabled")) {
            $product = new XLite_Model_Product();
            $product->updateInventory($this->get("properties"));
        }
		parent::update();

	} // }}} 

	function delete() // {{{ 
	{
    	if ($this->xlite->get("InventoryTrackingEnabled")) {
			$product = new XLite_Model_Product();
			$product->deleteInventory($this->get("properties"));
		}
		parent::delete();
		
		
	} // }}} 

	function setCategoriesList($categories) // {{{
	{
		$this->categories = null;
		$oldCategories = $this->getCategories();
        $deleteOnly = array_diff($oldCategories, (is_array($categories)) ? $categories : array());
        $addOnly = array_diff((is_array($categories)) ? $categories : array(), $oldCategories);

		if (is_array($categories)) {
			$categories = array_values($categories);
            $categories = implode("|", $categories);
		} else {
			$categories = "";
		}

		$this->set("categories", $categories);

		// for ALL --> for SELECTED
		if (count($oldCategories) == 0 && $categories != "" && count($addOnly) > 0) {
			$allProducts = $this->getProductsList(array());
			$selectedProducts = $this->getProductsList($addOnly);
        	$deletedProducts = array_diff($allProducts, (is_array($selectedProducts)) ? $selectedProducts : array());
        	$this->deleteProductsList($deletedProducts, $categories);
		}
		if (count($oldCategories) > 0 && count($deleteOnly) > 0) {
			$deletedProducts = $this->getProductsList($deleteOnly);
        	$this->deleteProductsList($deletedProducts, $categories);
		}

		if (($categories != "" && count($addOnly) > 0) || $categories == "") {
			foreach ($this->getProductsList($addOnly) as $product_id) {
    			$po = new XLite_Module_ProductOptions_Model_ProductOption();
    			$child_po = $po->count("parent_option_id='".$this->get("option_id")."' AND product_id='".$product_id."'");
    			if ($child_po == 0) {
        			$po->set("properties", $this->get("properties"));
        			$po->set("option_id", null);
        			$po->set("product_id", $product_id);
        			$po->set("parent_option_id", $this->get("option_id"));
        			$po->create();
        		}
    		}
		}
	} // }}} 

	function getCategories() // {{{ 
	{
		if (!isset($this->categories)) {
			$categories = $this->get("categories");
			$this->categories = !empty($categories) ? explode("|",$categories) : array();
			if (!is_array($this->categories)) {
				$this->categories = array($this->categories);
			}
		}
		return $this->categories;
	} // }}}

	function isCategorySelected($category_id)// {{{
	{
		$this->getCategories();
		if (count($this->categories) == 0)
			return false;
		return in_array($category_id, $this->categories);
	} // }}}
	
	function isGlobal() // {{{ 
	{
		return ($this->get("product_id") == 0 && !$this->get("categories")) ? true : false;
	} // }}} 

	function getGlobalOptions() // {{{
	{
		$po = new XLite_Module_ProductOptions_Model_ProductOption();
		return $po->findAll("product_id = 0");
	} // }}}

	function getProductsList($categories=null)
	{
        $ids = array();
        if (!isset($categories)) {
        	$categories = $this->getCategories();
        }
        if (count($categories) > 0) {
            foreach ($categories as $category_id) {
                $category = new XLite_Model_Category($category_id);
                $products = $category->get("products");
                foreach ($products as $product) {
                	$ids[] = $product->get("product_id");
                }
            }
        } else {
            $product = new XLite_Model_Product();
            $result = $product->iterate();
            while ($product->next($result)) {
				$ids[] = $product->get("product_id");
            }
        }
		return $ids;
	}

	function deleteProductsList(array &$products, $categories)
	{
		if (!is_array($categories)) {
			$categories = explode("|", $categories);
		}

		$po = new XLite_Module_ProductOptions_Model_ProductOption();
		foreach ($products as $product_id) {
			$child_po = $po->findAll("parent_option_id='".$this->get("option_id")."' AND product_id='".$product_id."'");
			if ($child_po) {
        		$product = new XLite_Model_Product($product_id);
                $productCategories = array();
                $product_categories = $product->get("categories");
                if (is_array($product_categories)) {
                	foreach($product_categories as $cat) {
                		$productCategories[] = $cat->get("category_id");
                	}
                }
				foreach($child_po as $option_) {
        			if (count(array_intersect($categories, $productCategories)) == 0) {
						$option_->delete();
					}
				}
			}
		}
	}
} 

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
