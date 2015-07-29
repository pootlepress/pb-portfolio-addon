/**
 * Plugin front end scripts
 *
 * @package Pootle_PB_Portfolios
 * @version 0.1.0
 */
jQuery(function ($) {

    //Put all jquery code in here
    resize_pofo_items = function() {
        $('.panel-row-style.portfolio-layout-square, .panel-row-style.portfolio-layout-circle').each(function () {
            var $t = $(this);

            $t.find('.ppb-portfolio-block').each(function () {
                var $t = $(this),
                    wid = $t.width(),
                    pofo = $t.find('.ppb-portfolio-item'),
                    pofoHi = pofo.height();
                $t.css('height', wid);

                pofo.css({
                    padding: (wid - pofoHi)/2 + 'px ' + (wid*0.16) + 'px'
                });
            });

            $t.css('opacity', '1');
        });
    };
    resize_pofo_items();

    $('[data-ppb-pofo-item-link]').click(function () {

    });

    $(window).resize(resize_pofo_items);
    $('.ppb-block').hover(
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