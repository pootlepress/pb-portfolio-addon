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

        $t.find( '.panel.portfolio-item').each( function(){
            var $t = $(this);
            $t.css('height', $t.width());
        } );

        $t.css('opacity', '1');
    } );
});