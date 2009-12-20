Use this page to configure your store to communicate with your Payment Processing Gateway. Complete the required fields below and press the "Update" button.<hr>

<p>
<span class="SuccessMessage" IF="dialog.updated">SkipJack parameters were successfully changed. Please make sure that the SkipJack payment method is enabled on the <a href="admin.php?target=payment_methods"><u>Payment methods</u></a> page before you can start using it.</span>
<form action="admin.php" method="POST">
<input type="hidden" name="target" value="payment_method">
<input type="hidden" name="action" value="update">
<input type="hidden" name="payment_method" value="{dialog.pm.get(#payment_method#)}">
<table border=0 cellspacing=10>
<tr>
<td>HTML Serial Number:</td>
<td><input type=text name=params[param01] size=24 value="{dialog.pm.params.param01}"></td>
</tr>

<tr>
<td>Test/Live mode:</td>
<td>
<select name=params[testmode]>
<option value=Y selected="{IsSelected(dialog.pm.params.testmode,#Y#)}">test
<option value=N selected="{IsSelected(dialog.pm.params.testmode,#N#)}">live
</select>
</td>
</tr>

<tr>
<td colspan=2>
<input type=submit value=" Update ">
</td>
</tr>
</table>
</form>
