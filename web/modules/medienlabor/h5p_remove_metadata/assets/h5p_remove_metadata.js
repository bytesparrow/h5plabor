/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function ($) {

    /**
     * Utility to kill the metadata-button
     */
    Drupal.behaviors.h5p_remove_metadata = {
        /*adds an inline-css to the iframe containing the h5p-editor. 
         * The CSS hides the metadata-button*/
        register_metadata_remover: function ()
        {

            setTimeout(function () {
                window[0].H5PEditor.$("head").append('<style>.h5p-metadata-button-wrapper{display:none;}</style>');
            }, 1300);
            return;
        }
    }

})(jQuery);


jQuery(document).ready(function () {
    Drupal.behaviors.h5p_remove_metadata.register_metadata_remover();
});
