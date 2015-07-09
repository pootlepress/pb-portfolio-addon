/**
 * Plugin front end scripts
 *
 * @package PB_Portfolio_Add_on
 * @version 0.1.0
 */
jQuery(function ($) {

    //Put all jquery code in here
    $( '.panel-row-style.portfolio-layout-square').each( function(){
        var $t = $(this);

        $t.find( '.pb-addon-portfolio-item').each( function(){
            var $t = $(this);
            $t.css('height', $t.width());
        } );

        $t.css('opacity', '1');
    } );
    $('.panel').hover(
        function(){
            var $t = $(this).children('.pb-addon-portfolio-item');
            $t.addClass( $t.data( 'portfolio-animate' ) );
        },
        function(){
            var $t = $(this).children('.pb-addon-portfolio-item');
            $t.removeClass( $t.data( 'portfolio-animate' ) );
        }
    );

});