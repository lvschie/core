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
<tr IF="item.productOptions">
    <td valign="top" nowrap>Selected options&nbsp;&nbsp;&nbsp;</td>
    <td>
	<table cellpadding="0" cellspacing="0" border="0">
	<tr FOREACH="item.productOptions,poption">
    		<td nowrap>{poption.class:h}: {poption.option:h}<br></td>
	</tr>
	</table>
    </td>
</tr>
