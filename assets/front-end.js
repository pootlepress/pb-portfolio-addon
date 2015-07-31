/**
 * Plugin front end scripts
 *
 * @package Pootle_PB_Portfolios
 * @version 0.1.0
 */
jQuery(function ($) {

    //Put all jquery code in here
    resize_pofo_items = function() {
        $('.panel-row-style.ppb-portfolio').each(function () {
            var $row = $(this),
                gutter = parseFloat( $row.find('.ppb-col').eq(1).css('padding-right') );

            $row.find('.ppb-block').not(':last-child').css('margin-bottom', gutter*2);

            $row.find('.ppb-portfolio-block').each(function () {
                var $t = $(this),
                    wid = $t.width(),
                    pofo = $t.find('.ppb-portfolio-item'),
                    pofoHi = pofo.height();

                //Creating square elements
                if ( $row.hasClass('portfolio-layout-square') || $row.hasClass('portfolio-layout-circle') ){
                    pofo.css({
                        padding: (wid - pofoHi - 2)/2 + 'px ' + (wid*0.16) + 'px'
                    });
                }
            });

            $row.css('opacity', '1');
        });
    };
    resize_pofo_items();

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