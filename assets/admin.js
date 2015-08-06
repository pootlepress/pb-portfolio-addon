/**
 * Created by shramee on 9/7/15.
 */
jQuery(function ($) {
    ppbPofo = {};
    ppbPofo.frame = null;
    /**
     * Set the title of the panel
     * @since 0.1.0
     */
    var $html = $('html');
    $html.on('pootlepb_admin_content_block_title', function (e, $t, data) {

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
    };

    $html.on('pootlepb_admin_input_field_event_handlers', function ( e, $this ) {
        var $butts = $this.find('.pofo-select-image, .pofo-sort-image'),
            $slImg = $this.find('.pofo-select-image'),
            $srtImg = $this.find('.pofo-sort-image');
        $butts.off( 'click' );
        $slImg.click(ppbPofo.selectImg);
        $srtImg.click(function(){
            var $t = $(this);
            alert($t.siblings( 'input').val());

        })
    });

    ppbPofo.selectImg = function (e) {
        e.preventDefault();

        var $textField = $(this).siblings('input');
        // If the media frame already exists, reopen it.
        if (ppbPofo.frame) {
            ppbPofo.frame.open();
            return;
        }
        // Create the media frame.
        ppbPofo.frame = wp.media.frames.ppbPofoFrame = wp.media({
            title: 'Choose Background Images',
            button: {text: 'Set portfolio items background image'},
            multiple: true
        });
        // When an image is selected, run a callback.
        ppbPofo.frame.on('select', function() {
            var attachment = ppbPofo.frame.state().get('selection').toJSON();

            //Get all selected images url in an object
            var images = {};
            $.each(attachment, function( k, v ){
                images[k] = v.url;
            });

            ppbPofo.updateCBbgImg(images);

            //Put the selected images in $textField
            $textField
                .val(JSON.stringify( images ))
                .change();
        });
        // Finally, open the modal
        ppbPofo.frame.open();
    };
    /** Updates Content Block background images */
    ppbPofo.updateCBbgImg = function( images ) {
        var $row = $('#grid-styles-dialog').data('container'),
            $CBstyles = $row.find('[name*="][info][style]"]');
        $.each(images, function (k, v) {
            var $t = $CBstyles.eq(k);
            console.log( $t.val() )
            var styles = $.parseJSON( $t.val() );
            styles['portfolio-bg']= v;
            $t.val(JSON.stringify(styles));
            console.log( $t.val() )
            console.log( '' )
        });
    };
});