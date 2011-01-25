{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *}
<select size=7 name=partner_id>
<option IF="allOption" value="" selected="partner_id=##">All</option>
<option FOREACH="partners,partner" value="{partner.profile_id}" selected="partner_id={partner.profile_id}">{partner.billing_firstname} {partner.billing_lastname} &lt;{partner.login}&gt;</option>
</select>
