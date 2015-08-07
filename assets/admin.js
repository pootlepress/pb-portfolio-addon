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
    ppbPofo.addPofoDialog = $('#pofo-add-dialog');
    ppbPofo.addPofoDialog
        .ppbDialog({
            dialogClass: 'panels-admin-dialog',
            autoOpen: false,
            title: ppbPofo.addPofoDialog.attr('data-title'),
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

    ppbPofo.editBgDialog = $('#pofo-edit-bgs-dialog');
    ppbPofo.editBgDialog
        .ppbDialog({
            dialogClass: 'panels-admin-dialog',
            autoOpen: false,
            title: ppbPofo.editBgDialog.attr('data-title'),
            open: function () {
                ppbPofo.editBgDialog.ppbDialog( "option", "width", $(window).width() - 50 );
                ppbPofo.editBgDialog.ppbDialog( "option", "height", $(window).height() - 50 );
                var imgs = ppbPofo.imgSelected,
                    imgWrap = $('#pofo-edit-bgs-dialog .images'),
                    $row = $('#grid-styles-dialog').data('container');
                imgWrap.attr( 'class', 'images num-cols-' + $row.find('.cell').length );

                $.each( imgs, function( k, v ){
                    imgWrap.append( '<img src="' + v + '">' );
                });
                imgWrap.sortable({
                    items : 'img'
                })
            },
            width: $(window).width() - 50,
            height: $(window).height() - 50,
            buttons: {
                Done: function(){
                    var images = [],
                        imgWrap = $('#pofo-edit-bgs-dialog .images');
                    imgWrap.children('img').each(function(i){
                        images[i] = $(this).attr('src');
                    });
                    ppbPofo.editBgDialog.data('BGimgInput').val(JSON.stringify(images));
                    imgWrap.html('');
                    ppbPofo.imgSelected = images;
                    ppbPofo.updateCBbgImg();
                    ppbPofo.editBgDialog.ppbDialog('close')
                }
            }
        });

    $('.add-pofo').click(function(e){
        e.preventDefault();
        ppbPofo.addPofoDialog.ppbDialog('open')
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
            if ( ! $(this).siblings( 'input').val() ) {
                $slImg.click();
                return;
            }
            ppbPofo.editBgDialog.data('BGimgInput', $(this).siblings( 'input'))
            ppbPofo.imgSelected = $.parseJSON( $(this).siblings( 'input').val() );
            ppbPofo.editBgDialog.ppbDialog('open');
        })
    });

    ppbPofo.selectImg = function (e) {
        e.preventDefault();

        var $textField = $(this).siblings('input');
        // If the media frame already exists, reopen it.

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
            ppbPofo.imgSelected = {};
            $.each(attachment, function( k, v ){
                ppbPofo.imgSelected[k] = v.url;
            });

            ppbPofo.updateCBbgImg();

            //Put the selected images in $textField
            $textField
                .val(JSON.stringify( ppbPofo.imgSelected ))
                .change();
        });
        // Finally, open the modal
        ppbPofo.frame.open();
    };
    /** Updates Content Block background images */
    ppbPofo.updateCBbgImg = function() {
        var $row = $('#grid-styles-dialog').data('container'),
            $CBstyles = $row.find('[name*="][info][style]"]');
        $.each( ppbPofo.imgSelected, function (k, v) {
            if ( k == $CBstyles.length ){
                return false;
            }
            var $t = $CBstyles.eq(k);
            var styles = $.parseJSON( $t.val() );
            styles['portfolio-bg']= v;
            $t.val(JSON.stringify(styles));
        } );
    };
});