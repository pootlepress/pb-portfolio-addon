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

        //Content blocks grid
        var $gPrev = $this.find('.pofo-grid-preview');
        var $gOpt = $this.find('.pofo-grid-options');
        $this.find('.content-block-portfolio-grid-across, .content-block-portfolio-grid-down').off( 'change' ).on( 'change', function () {
            var across = $this.find('.content-block-portfolio-grid-across').val(),
                down = $this.find('.content-block-portfolio-grid-down').val();
            //If both across and down values are numbers
            if ( ! isNaN(across) && ! isNaN(down) ) {
                //Clearing previous preview grid
                $gPrev.html('');
                $gOpt.html('');
                //Create grid preview
                for ( var ro = 0; ro < down; ro++ ) {
                    var $row = $('<div/>');
                    $row.addClass('pofo-grid-row');
                    for ( var c = 0; c < across; c++ ) {
                        var pofoItemRef = 'item-' + ro + '-' + c;

                        //Add pofo item to grid preview
                        $row.append(
                            $('<div/>')
                                .addClass('pofo-grid-item ' + pofoItemRef)
                                .data('ref', pofoItemRef)
                                .data('row', ro)
                                .data('col', c)
                                .css( 'width', ((101-across)/across) + '%' )
                        );

                        //Add pofo item options
                        $gOpt.append(
                            $('<div/>')
                            .addClass('pofo-item-options options-' + pofoItemRef)
                            .append(
                                $('<div/>')
                                .addClass('field field-portfolio-' + pofoItemRef + '-color field_type-color')
                                .append(
                                    $('<label/>')
                                    .html('Background Color')
                                )
                                .append(
                                    $('<span/>')
                                    .append(
                                        $('<input/>')
                                        .data('ref', pofoItemRef)
                                        .attr('dialog-field', 'portfolio-' + pofoItemRef + '-color')
                                        .attr('data-style-field-type', 'color')
                                        .addClass('content-block-options-' + ro + '-' + c + '-color')
                                    )
                                )
                            )
                            .append(
                                $('<div/>')
                                .addClass('field field-portfolio-' + pofoItemRef + '-image field_type-upload')
                                .append(
                                    $('<label/>')
                                        .html('Background Image')
                                )
                                .append(
                                    $('<span/>')
                                    .append(
                                        $('<input/>')
                                            .data('ref', pofoItemRef)
                                            .attr('dialog-field', 'portfolio-' + pofoItemRef + '-upload')
                                            .attr('data-style-field-type', 'upload')
                                            .addClass('content-block-options-' + ro + '-' + c + '-image')
                                    )
                                    .append(
                                        $('<button/>')
                                            .addClass('button upload-button')
                                            .html('Select Image')
                                    )
                                )
                            )
                        )
                    }
                    $gPrev.append($row);
                }
                ppbPofo.previewGridEvents( $gPrev, $gOpt );
                panels.addInputFieldEventHandlers($gOpt);
                panels.pootlePageGetWidgetStyles($gOpt);
            }
        });
    });

    /**
     * Sets preview grid events
     * @param $grid pofo-grid-preview element jquery
     */
    ppbPofo.previewGridEvents = function ( $grid, $gOpt ) {
        console.log($gOpt.find('.field_type-color input'));

        //Color field
        $gOpt.find('.field_type-color input').change(function(){
            var $t = $(this),
                itemRef = $t.data('ref');
            $grid.find('.pofo-grid-item.' + itemRef).css( 'background-color', $t.val() );
        });

        //Image field
        $gOpt.find('.field_type-upload input').change(function(){
            var $t = $(this),
                itemRef = $t.data('ref');
            $grid.find('.pofo-grid-item.' + itemRef).css( 'background-image', 'url(' + $t.val() + ')' );
        });

        //Preview grid items
        $grid.find('.pofo-grid-item').click(function(){
            var $t = $(this),
                itemRef = $t.data('ref'),
                $settings = $( '.pofo-item-options.options-' + itemRef);

            $('.pofo-grid-item').removeClass('active');

            //If already visible
            if ( $settings.hasClass('active') ) {
                //hide settings and return
                $settings.removeClass('active').hide();
                return;
            }

            //Make this active and get it's settings
            $t.addClass('active');
            $( '.pofo-item-options' ).removeClass('active').hide();
            $settings
                .show()
                .addClass('active')
                .css('top', 0)
                .css('top', Math.floor( -$settings.offset().top + $t.offset().top + 97 ) + 'px');
        })
    };

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