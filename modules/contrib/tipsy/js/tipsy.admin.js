(function($) {
  Drupal.behaviors.tipsyAdmin = {
    attach: function(context, settings) {

      ($('input#edit-drupal-forms-forms:checked').length > 0) ? $('#tipsy-drupal-forms-wrapper').show() : $('#tipsy-drupal-forms-wrapper').css('display', 'none');
      $('input#edit-drupal-forms-forms').click(function() {
        ($(this).is(':checked')) ? $('#tipsy-drupal-forms-wrapper').slideDown() : $('#tipsy-drupal-forms-wrapper').slideUp();
      });

    }
  };
})(jQuery);
