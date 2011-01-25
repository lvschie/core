/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Wishlist controller
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 */
jQuery(document).ready(
  function() {

    // Add onchange amount-based form submit
    jQuery('#wish-list .item-actions input[name="wishlist_amount"]').change(
      function() {
        var result = true;
        var amount = parseInt(this.value);
        result = !isNaN(amount) && 0 < amount;

        if (result) {
          jQuery(this.form).submit();

        } else {
          this.value = this.initialValue;
        }
 
        return result; 
      }
    )
    .each(
      function() {
        this.initialValue = this.value;
      }
    );

    // Add hover listener for Move to cart button
    jQuery('#wish-list .add-to-cart')
      .each(
        function() {
          this.moveQuantityWidget = jQuery('.move-quantity', jQuery(this).parents('.item-buttons'));
        }
      )
      .hover(
        function() {
          if (this.moveQuantityWidget.get(0).hideTo) {
            clearTimeout(this.moveQuantityWidget.get(0).hideTo);
            this.moveQuantityWidget.get(0).hideTo = false;
          }

          this.moveQuantityWidget.show();
        },
        function() {
          var o = this;
          this.moveQuantityWidget.get(0).hideTo = setTimeout(
            function() {
              o.moveQuantityWidget.hide();
            },
            500
          );
        }
      );

      jQuery('#wish-list .move-quantity').hover(
        function() {
          if (this.hideTo) {
            clearTimeout(this.hideTo);
            this.hideTo = false;
          }

          jQuery(this).show();
        },
        function() {
          var o = this;
          this.hideTo = setTimeout(
            function() {
              jQuery(o).hide();
            },
            500
          );
        }
      );
  }
);
