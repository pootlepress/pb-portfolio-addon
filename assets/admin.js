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

        if (typeof data == 'undefined') {
            return;
        }
        if (typeof data.info != 'undefined') {
            if ( data.info.style['portfolio-grid-across'] && data.info.style['portfolio-grid-down'] ) {
                $t.find('h4').html('Portfolio');
            }
        } else if ($t.data('dialog')) {
            var $d = $t.data('dialog');
            if ($d.find('.content-block-portfolio-grid-across').val() && $d.find('.content-block-portfolio-grid-down').val()) {
                $t.find('h4').html('Portfolio');
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
                Add: function () {
                    var grid = [
                        $('#pofo-add-dialog-num-cols').val(),
                        $('#pofo-add-dialog-num-rows').val()
                    ];

                    var $gridContainer = window.panels.createGrid(grid[0], null, {
                        'portfolio-hover-color': '#cccccc',
                        'portfolio-hover-color-opacity': '0.5',
                        'portfolio-layout': 'square'
                    });
                    panels.ppbGridEvents($gridContainer);

                    for (var x = 0; x < grid[0]; x++) {
                        panels.removePaddingAnimated($(this).closest('.grid-container'));

                        for (var y = 0; y < grid[1]; y++) {
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

    $('.add-pofo').click(function (e) {
        e.preventDefault();
        ppbPofo.addPofoDialog.ppbDialog('open')
    });

    add_pofo_block = function ($t) {
        $('.cell').removeClass('cell-selected');
        $t.addClass('cell-selected');

        var panel = panelsCreatePanel('Pootle_PB_Content_Block', {
            text: '[Your text here]',
            info: {
                style: {
                    'portfolio-item': '1'
                }
            }
        });
        panels.addPanel(panel, null, null, false);
    };

    $html.on('pootlepb_admin_editor_panel_done', function (e, $this) {

        if (
            $this.find('.content-block-portfolio-grid-across').val() &&
            $this.find('.content-block-portfolio-grid-down').val()
        ) {
            $this.find('.ppb-tabs-anchors[href="#pootle-portfolio-tab"]').click();
        }
    });

    $html.on('pootlepb_admin_input_field_event_handlers', function (e, $this) {

            var $butts = $this.find('.pofo-select-image, .pofo-sort-image'),
            $slImg = $this.find('.pofo-select-image'),
            $srtImg = $this.find('.pofo-sort-image');
        $butts.off('click');
        $slImg.click(ppbPofo.selectImg);

        //Content blocks grid
        var $gPrev = $this.find('.pofo-grid-preview');
        var $gOpt = $this.find('.pofo-grid-options');
        $this.find('.content-block-portfolio-grid-across, .content-block-portfolio-grid-down').off('change').on('change', function () {
            var across = $this.find('.content-block-portfolio-grid-across').val(),
                down = $this.find('.content-block-portfolio-grid-down').val(),
                item = 0;
            //If both across and down values are numbers
            if (!isNaN(across) && !isNaN(down)) {
                //Clearing previous preview grid
                $gPrev.html('');
                $gOpt
                    .html('')
                    .data('cols', across)
                    .data('rows', down);
                //Create grid preview
                for (var ro = 0; ro < down; ro++) {
                    for (var c = 0; c < across; c++) {
                        var pofoItemRef = 'item-' + item,
                            styles = {
                                width: ((100 - across) / across) + '%',
                                paddingTop: ((100 - across) / across) + '%'
                            },
                            classes = 'pofo-grid-item ' + pofoItemRef;

                        //Add pofo item to grid preview
                        $gPrev.append(
                            $('<div/>')
                                .addClass(classes)
                                .data('ref', pofoItemRef)
                                .css(styles)
                        );

                        //Add pofo item options
                        $gOpt
                            .append(
                            $('<div/>')
                                .addClass('pofo-item-options options-' + pofoItemRef)
                                .append(
                                //CONTENT
                                $('<div/>')
                                    .addClass('field field-portfolio-' + pofoItemRef + '-color field_type-color')
                                    .append(
                                    $('<label/>')
                                        .html('Content')
                                )
                                    .append(
                                    $('<span/>')
                                        .append(
                                        $('<textarea/>')
                                            .attr('dialog-field', 'portfolio-' + pofoItemRef + '-content')
                                            .attr('data-style-field-type', 'textarea')
                                            .addClass('content-block-options-' + pofoItemRef + '-content')
                                    )
                                )
                            )
                                .append(
                                //HOVER COLOR
                                $('<div/>')
                                    .addClass('field field-portfolio-' + pofoItemRef + '-color field_type-color')
                                    .append(
                                    $('<label/>')
                                        .html('Hover Color')
                                )
                                    .append(
                                    $('<span/>')
                                        .append(
                                        $('<input/>')
                                            .attr('dialog-field', 'portfolio-' + pofoItemRef + '-hover-color')
                                            .attr('data-style-field-type', 'color')
                                            .addClass('content-block-options-' + pofoItemRef + '-hover-color')
                                    )
                                )
                            )
                                .append(
                                //BG COLOR
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
                                            .addClass('content-block-options-' + pofoItemRef + '-color')
                                    )
                                )
                            )
                                .append(
                                //BG IMAGE
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
                                            .attr('dialog-field', 'portfolio-' + pofoItemRef + '-image')
                                            .attr('data-style-field-type', 'upload')
                                            .addClass('pofo-item-image content-block-options-' + pofoItemRef + '-image')
                                    )
                                        .append(
                                        $('<button/>')
                                            .addClass('button upload-button')
                                            .html('Select Image')
                                    )
                                )
                            )
                        );
                        item++;
                    }
                }
                ppbPofo.previewGridEvents($gPrev, $gOpt);
                panels.addInputFieldEventHandlers($gOpt);
                panels.pootlePageGetWidgetStyles($gOpt);
                $gPrev.sortable({
                    items: '.pofo-grid-item',
                    sort: function ( e, ui ) {
                        $('.pofo-grid-item').each(function ( i ) {
                            var $t = $(this),
                                newRef = 'item-' + i,
                                classes = 'pofo-grid-item ' + newRef,
                                oldRef = $t.data('ref', pofoItemRef);
                            $t
                                .attr('class', classes)
                                .data('ref', newRef);
                            ppbPofo.updateSettingsIndex( newRef, oldRef );
                        });
                    }
                });
            }
        });
    });

    ppbPofo.updateSettingsIndex = function( ol, nu ) {
        var $sets = $('.pofo-item-options.options-' + ol).attr('class', 'pofo-item-options options-' + nu);

        $.each(['-content', '-hover-color', '-color', '-image'], function(k, v){
            $sets.find('.content-block-options-' + ol + k)
                .attr('dialog-field', 'portfolio-' + nu + k)
                .addClass('.content-block-options-' + nu + k)
                .removeClass('.content-block-options-' + ol + k)
                .change();
        })
    };

    /**
     * Sets preview grid events
     * @param $grid pofo-grid-preview element jquery
     */
    ppbPofo.previewGridEvents = function ($grid, $gOpt) {
        //Color field
        $gOpt.find('.field_type-color input').change(function () {
            var $t = $(this),
                itemRef = $t.data('ref');
            $grid.find('.pofo-grid-item.' + itemRef).css('background-color', $t.val());
        });

        //Image field
        $gOpt.find('.field_type-upload input').change(function () {
            var $t = $(this),
                itemRef = $t.data('ref');
            $grid.find('.pofo-grid-item.' + itemRef).css('background-image', 'url(' + $t.val() + ')');
        });

        //Preview grid items
        $grid.find('.pofo-grid-item').click(function () {
            var $t = $(this),
                itemRef = $t.data('ref'),
                $settings = $('.pofo-item-options.options-' + itemRef),
                $tab = $('#pootle-portfolio-tab');

            $('.pofo-grid-item').removeClass('active');

            //If already visible
            if ($settings.hasClass('active')) {
                //hide settings and return
                $settings.removeClass('active').hide();
                return;
            }

            //Make this active and get it's settings
            $t.addClass('active');
            $('.pofo-item-options').removeClass('active').hide();
            $settings
                .show()
                .addClass('active')
                .css('top', 0)
                .css('top', Math.floor(-$settings.offset().top + $t.offset().top + $t.outerWidth() + 3) + 'px');

            $tab.animate({
                scrollTop: $t.offset().top - $tab.offset().top + $tab.scrollTop() - 3
            }, 500);
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
        ppbPofo.frame.on('select', function () {
            var attachment = ppbPofo.frame.state().get('selection').toJSON();

            //Get all selected images url in an object
            ppbPofo.imgSelected = {};
            ppbPofo.imgSelected.length = 0;
            $.each(attachment, function (k, v) {
                ppbPofo.imgSelected.length++;
                ppbPofo.imgSelected[k] = v.url;
            });

            ppbPofo.updateCBbgImg();

            //Put the selected images in $textField
            $textField
                .val(JSON.stringify(ppbPofo.imgSelected))
                .change();
        });
        // Finally, open the modal
        ppbPofo.frame.open();
    };
    /** Updates Content Block background images */
    ppbPofo.updateCBbgImg = function () {
        var $imgFields = $('.pofo-item-image'),
            num = ppbPofo.imgSelected.length,
            i = 0;

        debug = [];

        $imgFields.each(function (k) {
            var $t = $(this);
            if ( ! $t.val().length && i < num ) {
                $t.val( ppbPofo.imgSelected[i] ).change();
                console.log( {
                    index : i,
                    img : ppbPofo.imgSelected[i],
                    val : $t.val(),
                    num : num
                } );
                i++;
            }
        });
        console.log(debug);
    };
});