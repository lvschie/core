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
<FORM name="search_form" action="admin.php" method="GET">
<span IF="extraParams=##"><input FOREACH="allParams,_name,_val" type="hidden" name="{_name}" value="{_val:r}"/></span>
<span IF="!extraParams=##"><input FOREACH="extraParams,_name,_val" type="hidden" name="{_name}" value="{_val:r}"/></span>
<input type="hidden" name="mode" value="search">

{extra_parameters:h}

<TABLE border=0>
<TBODY>
	<TR>
		<TD class="FormButton" noWrap height=10>Product SKU</TD>
		<TD width=10 height=10></TD>
		<TD height=10><INPUT size=6 name="search_productsku" value="{search_productsku}"></TD>
	</TR>
	<TR>
		<TD class="FormButton" noWrap height=10>Product Title</TD>
		<TD width=10 height=10></TD>
		<TD height=10><INPUT size=30 name="substring" value="{substring}"></TD>
	</TR>
	<TR>
		<TD class="FormButton" noWrap height=10>In category</TD>
		<TD width=10 height=10><FONT class="ErrorMessage">*</FONT></TD>
		<TD height=10>
            <widget class="\XLite\View\CategorySelect" fieldName="search_category" allOption>
        </TD>
	</TR>
	<TR>
		<TD class="FormButton" noWrap height=10 colspan="3">
			Search in subcategories
			<input type="checkbox" name="subcategory_search" checked="{subcategory_search}">
		</TD>
	</TR>
	<widget module="CDev\ProductAdviser" template="modules/CDev/ProductAdviser/product_search.tpl">
    <TR><TD colspan=3>&nbsp;</TD></TR>
	<TR>
		<TD colspan=3>
		    <INPUT type="submit" value=" Search ">
        </TD>
	</TR>
</TBODY>
</TABLE>
</FORM>

<br>
<b>Note:</b> You can also <a href="javascript:void(0);" onclick="javascript:window.open('admin.php?target=add_product','ADD_PRODUCT','width=800,height=500,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"><u>add a new product</u></a>.
