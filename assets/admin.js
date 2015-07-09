/**
 * Created by shramee on 9/7/15.
 */
jQuery(function ($) {
    /**
     * Set the title of the panel
     * @since 0.1.0
     */

    $('html').on('pootlepb_admin_content_block_title', function (e, $t, data) {

        if( typeof data.info == 'undefined' ) {
            var $d = $t.data('dialog');
            if ( $d.find('.content-block-portfolio-item').is(':checked') ) {
                $t.find('h4').html('Portfolio Item');
            }
        } else {
            if ( data.info.style['portfolio-item'] ) {
                $t.find('h4').html('Portfolio Item');
            }
        }
    });

});