{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Item position
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (list="itemsList.product.table.admin.search.columns", weight="50")
 *}

<td><input class="pos" size="5" name="{getNamePostedData(#pos#,product.getProductId())}" value="{product.getOrderBy():r}" /></td>
