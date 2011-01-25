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
 * @copyright  Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Module\CDev\Affiliate\Controller\Customer;

/**
 * ____description____
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class PartnerPayments extends \XLite\Module\CDev\Affiliate\Controller\Partner
{
    public $totalPaid = 0.00;


    /**
     * Common method to determine current location 
     * 
     * @return array
     * @access protected 
     * @since  3.0.0 
     */ 
    protected function getLocation()
    {
        return 'Payments history';
    }


    function getPayments()
    {
        if (!$this->auth->isAuthorized($this)) {
        	return null;
        }

        if (is_null($this->payments)) {
            $this->payments = array();
            $pp = new \XLite\Module\CDev\Affiliate\Model\PartnerPayment();
            $table = $pp->db->getTableByAlias($pp->alias);
            $partnerID = $this->getComplex('auth.profile.profile_id');
            if ($this->get('period') == "period") {
                $startDate = $this->get('startDate');
                $endDate = $this->get('endDate') + 24 * 3600;
                $date = " AND paid_date>=$startDate AND paid_date<=$endDate ";
            }
            $sql = "SELECT sum(commissions) AS amount, paid_date   ".
                   "FROM $table ".
                   "WHERE partner_id=$partnerID AND paid=1 ".
                   $date .
                   "GROUP BY paid_date";
            $this->payments = $pp->db->getAll($sql);
            $sql = "SELECT sum(commissions) AS total ".
                   "FROM $table ".
                   "WHERE partner_id=$partnerID AND paid=1 ".
                   $date;
            $total = $pp->db->getAll($sql);
            $this->totalPaid = $total[0]['total'];
        }
        return $this->payments;
    }
}
