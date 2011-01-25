{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Shopping cart totals row
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (list="cart.totals", weight="10")
 *}
<li IF="!cart.global_discount=#0#"><em>Global discount:</em>
  {price_format(invertSign(cart.global_discount)):h}
</li>
