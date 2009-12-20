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
* 
*
* @package AntiFraud
* @access public
* @version $Id$
*/

class Module_AntiFraud_Order extends Order 
{
    function getAntiFraudData()
    {
		$this->xlite->logger->log("->AntiFraud_Order::getAntiFraudData");
    	if (is_null($this->get("details.af_result.total_trust_score")))
    	{
			if ($this->xlite->get("AFcallbackProcessing")) {
				$this->xlite->logger->log("->AntiFraud_Order::AFcallbackProcessing");
			} else {
    			$proxy_ip = $this->getProxyIP();
    			if (!empty($proxy_ip)) {
    				$customer_ip = $proxy_ip;
    				$proxy_ip = $this->getCustomerIP();
    			}
    			else
    				$customer_ip = $this->getCustomerIP();

    			$this->set("details.customer_ip","<".$customer_ip.">");
    			$this->set("details.proxy_ip",$proxy_ip);
                
                $this->checkFraud();
    			$this->xlite->logger->log("->AntiFraud_Order::checkFraud");
    		}
    	}
		$this->xlite->logger->log("<-AntiFraud_Order::getAntiFraudData");
    }

    function isAntiFraudForceQueued()
    {
		if ($this->get("details.af_result.total_trust_score") >= $this->config->get("AntiFraud.antifraud_risk_factor") && $this->config->get("AntiFraud.antifraud_force_queued") == 'Y') {
			$this->xlite->logger->log("->AntiFraud_Order::isAntiFraudForceQueued(1)");
			return true;
		} else {
			$this->xlite->logger->log("->AntiFraud_Order::isAntiFraudForceQueued(0)");
			return false;
		}
    }

	function isStatusChanged2Processed($oldStatus, $newStatus)
	{
		if ($oldStatus != 'P' && $oldStatus != 'C' && ($newStatus =='P' || $newStatus == 'C')) {
			return true;
		} else {
			return false;
		}
	}
	
	function statusChanged($oldStatus, $newStatus) // {{{
	{
		$this->xlite->logger->log("->AntiFraud_Order::statusChanged[".$oldStatus."][".$newStatus."]");
		if ($this->xlite->is("adminZone")) {
			$this->xlite->logger->log("->AntiFraud_Order::adminZone");
		} else {
			$this->xlite->logger->log("->AntiFraud_Order::!adminZone");

		    $this->getAntiFraudData();

    		if ($this->isStatusChanged2Processed($oldStatus, $newStatus)) {
				// switching to PROCESSED
    			if ($this->isAntiFraudForceQueued()) {
    				$this->xlite->logger->log("->AntiFraud_Order::status=Q");
    				$newStatus = "Q";
    				$this->set("status", $newStatus);
				}
    		}
		}

		parent::statusChanged($oldStatus, $newStatus);
	} // }}}

	function getAddress()
	{
		$address = $this->get("details.customer_ip");
		preg_match('/^<(.*)>$/',$address,$address);
		return $address[1];
	}	

	function checkFraud()
	{
		$profile = $this->get('profile');

		$post = array();
		$post["ip"] 		= $this->get('address');
		$post["proxy_ip"] 	= $this->get('details.proxy_ip');
		$post["email"] 		= preg_replace("/^[^@]+@/Ss","",$profile->get('login'));
		$post["country"] 	= $profile->get('billing_country');
		$post["state"] 		= $profile->get('billingState.code');
		$post["city"] 		= $profile->get('billing_city');
		$post["zipcode"] 	= $profile->get('billing_zipcode');
		$post["phone"] 		= $profile->get('billing_phone');
		$post["service_key"] = $this->config->get('AntiFraud.antifraud_license');
		$post["safe_distance"] = $this->config->get('AntiFraud.antifraud_safe_distance');

		$request = & func_new("HTTPS");
		$request->url = $this->config->get('AntiFraud.antifraud_url')."/antifraud_service.php";
		$request->data = $post;
		$request->request();
		
		if ($request->error) {
			$this->set("details.error",$request->error);
			$this->set("detailLabels.error","HTTPS error");
			return null;
		} else {

    		list($result,$data) = explode("\n",$request->response);
    		$result = unserialize($result);
    		$data 	= unserialize($data);

    		$risk_factor_multiplier = 1; 
    		$found =& func_new("Order",$this->get("order_id"));

    		if ($this->config->get("AntiFraud.antifraud_order_total") > 0 && $this->get("total") > 0 &&  $this->get("total") > $this->config->get("AntiFraud.antifraud_order_total"))	{
    			$risk_factor_multiplier *= $this->config->get("AntiFraud.order_total_multiplier");
    		}
    		$processed_orders = $found->count("(status='P' OR status='C') AND orig_profile_id='" . $this->get("origProfile.profile_id") . "' AND order_id<>'" . $this->get("order_id") . "'");

    		if ($processed_orders > 0) {
    			$this->set("details.processed_orders", $processed_orders);
    			$risk_factor_multiplier /= $this->config->get("AntiFraud.processed_orders_multiplier");
    		}
    		
    		$declined_orders = $found->count("(status='D' OR status='F') AND orig_profile_id='" . $this->get("origProfile.profile_id") . "' AND order_id<>'" . $this->get("order_id") . "'");

    		if ($declined_orders > 0) {
    			$this->set("details.declined_orders", $declined_orders);
    			$risk_factor_multiplier *= $this->config->get("AntiFraud.declined_orders_multiplier");
    		}
    		$duplicate_ip = $found->count("orig_profile_id <> ".$this->get("origProfile.profile_id")." AND details LIKE '%".$this->get("details.customer_ip")."%'");

    		if ($duplicate_ip) {
    			$risk_factor_multiplier *= $this->config->get("AntiFraud.duplicate_ip_multiplier");
    		}
    		
    		$result["total_trust_score"] = $result["total_trust_score"] * $risk_factor_multiplier;

            $country = & func_new("Country",$profile->get('billing_country'));
            if ($country->get("riskCountry")) {
                $result["total_trust_score"] +=  $this->config->get("AntiFraud.risk_country_multiplier");
            }
    		
            if ($result['available_request'] == $result['used_request']) {
                $result['error'] = 'LICENSE_KEY_EXPIRED';
    			$mailer =& func_new("Mailer");
    			$mailer->compose($this->get("config.Company.orders_department"),
    							 $this->get("config.Company.site_administrator"), 
    							 "modules/AntiFraud/license_expired");
                $mailer->send();
    		}

    		if ($result["total_trust_score"] > 10) {
    			$result["total_trust_score"] = 10;
    		}
    	
			$this->set("details.af_result",$result);	
			$this->set("details.af_data",$data); 
		}

		return $result;
	}
	
	function getProxyIP() // {{{
	{
		
		if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		    return $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (!empty($_SERVER["HTTP_X_FORWARDED"])) {
		    return $_SERVER["HTTP_X_FORWARDED"];
		} elseif (!empty($_SERVER["HTTP_FORWARDED_FOR"])) {
		    return $_SERVER["HTTP_FORWARDED_FOR"];
		} elseif (!empty($_SERVER["HTTP_FORWARDED"])){
		    return $_SERVER["HTTP_FORWARDED"];
		} elseif (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		    return $_SERVER["HTTP_CLIENT_IP"];
		} elseif (!empty($_SERVER["HTTP_X_COMING_FROM"])) {
		    return $_SERVER["HTTP_X_COMING_FROM"];
		} elseif (!empty($_SERVER["HTTP_COMING_FROM"])) {
		    return $_SERVER["HTTP_COMING_FROM"];
		} else 
			return '';	
	} // }}}
	
	function getCustomerIP() // {{{
	{
		return $_SERVER["REMOTE_ADDR"];	
	} // }}}

	function isAFServiceValue($value)
	{
		switch ($value) {
			case "IP_NOT_FOUND":
			case "POSTAL_CODE_NOT_FOUND":
			case "COUNTRY_NOT_FOUND":
			case "CITY_NOT_FOUND":
			case "IP_REQUIRED":
			case "DOMAIN_REQUIRED":
			case "EMPTY_SERVICE_KEY":
			case "NOT_AVAILABLE_SERVICE":
			case "NO_ACTIVE_LICENSES":
			case "NOT_ALLOWED_SHOP_IP":
			return true;
			default:
			return false;
		}
	}

    function getTotalTrustScore()
    {
		$score = $this->get("details.af_result.total_trust_score");
        return round($score, 1);
    }

    function update()
    {
		if ($this->xlite->is("adminZone") && $this->config->get("AntiFraud.always_keep_info")) {
        	$afFields = array("customer_ip", "proxy_ip", "af_result", "processed_orders", "declined_orders", "af_data");

			$oldOrder = func_new("Order", $this->get("order_id"));
        	$oldDetails = $oldOrder->get("details");
        	$details = $this->get("details");
        	$detailsUpdated = false;
        	foreach ($afFields as $fieldName) {
        		if (isset($oldDetails[$fieldName]) && !isset($details[$fieldName])) {
        			$details[$fieldName] = $oldDetails[$fieldName];
        			$detailsUpdated = true;
        		}
        	}
        	if ($detailsUpdated) {
        		$this->set("details", $details);
        	}
		}
        parent::update();
    }
}


// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
