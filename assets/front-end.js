/**
 * Plugin front end scripts
 *
 * @package Pootle_PB_Portfolios
 * @version 0.1.0
 */
jQuery(function ($) {

    $('.pofo-item').hover(
        function(){
            var $t = $(this).find('.ppb-portfolio-item');
            $t.addClass( $t.data( 'portfolio-animate' ) );
        },
        function(){
            var $t = $(this).find('.ppb-portfolio-item');
            $t.removeClass( $t.data( 'portfolio-animate' ) );
        }
    );

});