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

func_define("SUBSCRIBER_EXISTS", 1);
func_define("SUBSCRIBE_SUCCESS", 0);
func_define("SUBSCRIBE_FAILED", -1);

/**
* New list subscriber description.
*
* @package Module_Newsletters
* @access public
* @version $Id$
*/
class NewsSubscriber extends Base
{
    var $fields = array(
            "list_id"  => 0,
            "email" => "",
            "since_date"  => 0,
            );

    var $primaryKey = array("list_id", "email");
    var $alias = "news_subscribers";
    var $defaultOrder = "since_date,email";
    
    function subscribe($email, $list_id, $verbose = true)
    {
        require_once "modules/Newsletters/encoded.php";
        if ($this->find("list_id=$list_id AND email='".addslashes(strtolower($email))."'")) {
            return SUBSCRIBER_EXISTS;
        }
        $this->set("email", strtolower($email));
        $this->set("list_id", $list_id);
        $this->set("since_date", time());
        $this->create();
        if ($verbose) {
            $params = array
            (
            	"email" => $this->get("email"),
                "list" => $this->getList(true),
                "code" => func_newsletters_gen_code($this->get("email"))
            );
            if (!is_null($this->get("profile"))) {
            	$params["profile"] = $this->get("profile");
            }
            $this->sendMail(
                    $this->get("email"),
                    "modules/Newsletters/subscribed",
                    $params
                    );
        }
        return SUBSCRIBE_SUCCESS;
    }

    function unsubscribe($email = null, $list_id = null, $verbose = true)
    {
        if (!is_null($email) && !is_null($list_id)) {
            if (!$this->find("list_id=$list_id AND email='".addslashes(strtolower($email))."'")) {
                return;
            }
        }
        $this->delete();
        if ($verbose) {
        	$this->set("list_id", $list_id);
            $params = array
            (
                "email" => $this->get("email"),
                "list" => $this->getList(true)
            );
            if (!is_null($this->get("profile"))) {
            	$params["profile"] = $this->get("profile");
            }
            $this->sendMail(
                    $this->get("email"),
                    "modules/Newsletters/unsubscribed",
                    $params
                    );
        }                
    }
    
    function request($email, $list_id = null)
    {
        $this->sendMail(
                $email,
                "modules/Newsletters/confirm_subscription",
                array("email" => $email,
                    "code" => func_newsletters_gen_code($email))
                );
    }

    function sendMail($email, $template, $params = array())
    {
        $mailer =& func_new("Mailer");
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $mailer->set($param, $value);
            }
        }
        $mailer->compose($this->get("config.Company.site_administrator"),
                         $email,
                         $template);
        $mailer->send();                         
    }
    
    function import($list_id, $fname)
    {
        if (($subscribers = file($fname)) === false) {
            echo "failed to open CSV file $fname";
            echo '<br /><br><a href="admin.php?target=news_subscribers&list_id=' . $list_id . '"><u>Click here to return to admin interface</u></a>';
            die;
        }
        
        // create and setup validator
        $tempFieldName = md5(time());        
        $emailValidator = & func_new("CEmailValidator");
        $emailValidator->set("field", $tempFieldName);

        foreach ($subscribers as $num => $email) {
            trim($email);
            $email = preg_replace("/[\n\r\t]/", "", $email);
            echo "<b>Importing CSV line #$num...</b><br>";
            $num++;
            $_POST[$tempFieldName] = $email;        
            if ((strlen($email) > 0) && $emailValidator->isValid()) {
                $ns =& func_new("NewsSubscriber");
                $res = $ns->subscribe($email, $list_id);
                if ($res == SUBSCRIBER_EXISTS) {
                    echo "<font color=blue>Subscriber $email already exists!</font><br>";
                }
            } else {
				echo "<font color=red>Subscriber e-mail is wrong!</font><br>";
            }
        }
    }

    function &getList($as_new=false)
    {
    	if ($as_new) {
    		$this->list = null;
    	}
        if (is_null($this->list)) {
            $this->list =& func_new("NewsList", $this->get("list_id"));
        }
        return $this->list;
    }
} 

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
