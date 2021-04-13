(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.h5plabor = {
        attach: function (context, settings) {
            $("a.nav-link--user").html(drupalSettings.h5plabor.user_name);
        }
    };

})(jQuery, Drupal, drupalSettings);