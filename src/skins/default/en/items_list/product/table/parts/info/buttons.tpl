{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Item buttons
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (list="itemsList.product.table.customer.info", weight="40")
 *}
<td IF="isShowAdd2Cart(product)" class="product-button-column">
  {displayListPart(#buttons#,_ARRAY_(#product#^product))}
</td>
