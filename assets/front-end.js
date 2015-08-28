/**
 * Plugin front end scripts
 *
 * @package Pootle_PB_Portfolios
 * @version 0.1.0
 */
jQuery(function ($) {
    resize_pofo_items = function() {
        $('.ppb-block.ppb-portfolio').each(function() {
            var $row = $(this);
            $('.ppb-portfolio-item').each(function () {
                var $t = $(this),
                    wid = $t.width(),
                    pofo = $t.find('.hv-center'),
                    pofoHi = pofo.height();

                //Creating square elements
                if ($row.hasClass('portfolio-layout-square') || $row.hasClass('portfolio-layout-circle')) {
                    pofo.css({
                        paddingTop: (wid - pofoHi - 2) / 2 + 'px',
                        paddingBottom: (wid - pofoHi - 2) / 2 + 'px'
                    });
                }

            });
            $row.css('opacity', 1)
        })
    };
    resize_pofo_items();
    $(window).resize(resize_pofo_items);

    $('.pofo-item').hover(
        function(){
            var $t = $(this).find('.ppb-portfolio-item');
            var $$ = $(this).find('.hv-center');
            $$.addClass( $t.data( 'portfolio-animate' ) );
        },
        function(){
            var $t = $(this).find('.ppb-portfolio-item');
            var $$ = $(this).find('.hv-center');
            $$.removeClass( $t.data( 'portfolio-animate' ) );
        }
    );

});