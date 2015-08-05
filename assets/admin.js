/**
 * Created by shramee on 9/7/15.
 */
jQuery(function ($) {
    /**
     * Set the title of the panel
     * @since 0.1.0
     */

    $('html').on('pootlepb_admin_content_block_title', function (e, $t, data) {

        if( typeof data == 'undefined' ) {
            return;
        }
        if( typeof data.info != 'undefined' ) {
            if ( data.info.style['portfolio-item'] ) {
                $t.find('h4').html('Portfolio Item');
            }
        } else if( $t.data('dialog') ) {
            var $d = $t.data('dialog');
            if ( $d.find('.content-block-portfolio-item').is(':checked') ) {
                $t.find('h4').html('Portfolio Item');
            }
        }
    });
    $('#pofo-add-dialog').ppbDialog({
        dialogClass: 'panels-admin-dialog',
        autoOpen: false,
        title: $('#pofo-add-dialog').attr('data-title'),
        open: function () {
            $('#pofo-add-dialog-num-cols').val(4);
            $('#pofo-add-dialog-num-rows').val(4);
        },
        width: 430,
        buttons: {
            Add: function(){
                var grid = [
                    $('#pofo-add-dialog-num-cols').val(),
                    $('#pofo-add-dialog-num-rows').val()
                ];

                console.log( grid );

                var $gridContainer = window.panels.createGrid(grid[0], null, {'portfolio-hover-color': '#cccccc', 'portfolio-hover-color-opacity': '0.5', 'portfolio-layout':'square'});
                panels.ppbGridEvents($gridContainer);

                for( var x = 0; x < grid[0]; x++ ){
                    panels.removePaddingAnimated( $(this).closest('.grid-container') );

                    for( var y = 0; y < grid[1]; y++ ) {
                        var $t = $gridContainer.find('.cell').eq(x);
                        add_pofo_block($t);
                    }
                }
                $gridContainer.hide();
                $gridContainer.slideDown();
                $('#pofo-add-dialog').ppbDialog('close')
            }
        }
    });

    $('.add-pofo').click(function(e){
        e.preventDefault();
        $('#pofo-add-dialog').ppbDialog('open')
    });

    add_pofo_block = function($t) {
        $('.cell').removeClass('cell-selected');
        $t.addClass('cell-selected');

        var panel = panelsCreatePanel('Pootle_PB_Content_Block', {
            text: '[Your text here]',
            info:{
                style: {
                    'portfolio-item': '1'
                }
            }
        });
        panels.addPanel(panel, null, null, false);
    }

});