{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU General Pubic License (GPL 2.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="top_links", weight="20")
 *}
<li IF="isDrupalStorefrontLinkVisible()">
  <a href="#">{t(#Drupal#)}</a>
  <div>
    <ul>
      <list name="top_links.drupal_storefront" />
    </ul>
  </div>
</li>
