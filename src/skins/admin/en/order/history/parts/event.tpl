{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Order history event
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="order.history.base.events", weight="20")
 *}
<li class="event" FOREACH="block,index,event">
  <ul class="event-details">
    <list name="order.history.base.events.details" event="{event}" />
  </ul>
  <list name="order.history.base.events.details.info" event="{event}" />
</li>