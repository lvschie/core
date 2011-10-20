{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Move pointer
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.11
 *
 * @ListChild (list="product.attachments.row", weight="100", zone="admin")
 *}
<a href="#" class="move" title="{t(#Move#)}"><img src="images/spacer.gif" alt="" /></a>
<input type="hidden" class="orderby" name="data[{attachment.getId()}][orderby]" value="{attachment.getOrderby()}" />
