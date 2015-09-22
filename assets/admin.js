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

    //Switch to pofo tab
    $html.on('pootlepb_admin_editor_panel_done', function (e, $this) {
        if (
            $this.find('.content-block-portfolio-grid-across').val() &&
            $this.find('.content-block-portfolio-grid-down').val()
        ) {
            $this.find('.ppb-tabs-anchors[href="#pootle-portfolio-tab"]').click();
        }
    });

    $html.on('pootlepb_admin_input_field_event_handlers', function (e, $this) {
        $this.find('.field-portfolio-edit-background').hide();
            var $butts = $this.find('.pofo-select-image, .pofo-sort-image'),
            $slImg = $this.find('.pofo-select-image'),
            $srtImg = $this.find('.pofo-sort-image');
        $butts.off('click');
        $slImg.click(ppbPofo.selectImg);

        $this.find('.pofo-link-to select').change(function () {
            var $t = $(this),
                $field = $t.closest('.field'),
                $linkFields = $t.closest('.field').siblings('.pofo-url, .pofo-new-page');
            if ( 'link' == $t.val() ){
                $linkFields.show();
            } else {
                $linkFields.hide();
            }
        });

        //Content blocks grid
        var $gPrev = $this.find('.pofo-grid-preview');
        var $gOpt = $this.find('.pofo-grid-options');
        $this.find('.content-block-portfolio-grid-across, .content-block-portfolio-grid-down')
          .off('change')
          .on('change', function () {
            $('.field-portfolio-edit-background').hide();
            var across = $this.find('.content-block-portfolio-grid-across').val(),
                down = $this.find('.content-block-portfolio-grid-down').val(),
                item = 0;
            $gPrev.html('');
            $gOpt.html('');
            //If both across and down values are numbers
            if (!isNaN(across) && !isNaN(down) && across * down > 0 ) {
                $('.field-portfolio-edit-background').show();
                //Clearing previous preview grid
                $gOpt
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
                        ppbPofo.pofoItemSettings($gOpt, pofoItemRef);
                        item++;
                    }
                }
                ppbPofo.previewGridEvents($gPrev, $gOpt);
                panels.addInputFieldEventHandlers($gOpt);
                var json = window.$currentPanel.find('input[name$="[style]"]').val(),
                    styleData = JSON.parse(json);
                panels.setStylesToFields($gOpt, styleData);
                $gPrev.sortable({
                    items: '.pofo-grid-item',
                    update: function ( e, ui ) {
                        $('.pofo-grid-item').each(function ( i ) {
                            var $t = $(this),
                                newRef = 'item-' + i,
                                classes = 'pofo-grid-item ' + newRef,
                                oldRef = $t.data('ref');
                            $t  .addClass(newRef)
                                .removeClass(oldRef)
                                .data('ref', newRef);
                            ppbPofo.updateSettingsIndex( oldRef, newRef );
                        });
                        ppbPofo.previewGridEvents($(), $('.pofo-item-options.new'));
                        $('.pofo-item-options.new').removeClass('new').find('input').change();
                    }
                });
            }
        });
    });

    ppbPofo.updateSettingsIndex = function( ol, nu ) {
        if(nu == ol) {
            return;
        }
        var $sets = $('.pofo-item-options.options-' + ol).not('.new');
        $sets
            .removeClass('options-' + ol)
            .addClass('options-' + nu + ' new');
        $.each(['-content', '-hover-color', '-color', '-image'], function(k, v){
            var $field = $sets.find('.content-block-options-' + ol + v);
            $field
               .attr('dialog-field', 'portfolio-' + nu + v)
               .addClass('content-block-options-' + nu + v)
               .removeClass('content-block-options-' + ol + v)
               .data('ref', nu);
        })
    };

    /**
     * Sets preview grid events
     * @param $grid pofo-grid-preview element jquery
     */
    ppbPofo.previewGridEvents = function ($grid, $gOpt) {
        //Color field
        $gOpt.find('.field_type-color input').off( 'change').on( 'change', function () {
            var $t = $(this),
                itemRef = $t.data('ref');
            $grid.find('.pofo-grid-item.' + itemRef).css('background-color', $t.val());
        });

        //Image field
        $gOpt.find('.field_type-upload input').off( 'change').on( 'change', function () {
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

        $imgFields.each(function (k) {
            var $t = $(this);
            if ( ! $t.val().length && i < num ) {
                $t.val( ppbPofo.imgSelected[i] ).change();
                i++;
            }
        });
    };

    /** Adds pofo item options */
    ppbPofo.pofoItemSettings = function ( $this, pofoItemRef ) {
        $this.append(
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
                    $('<span/>').append(
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
                .append(
                //LINK TO
                $('<div/>')
                    .addClass('field field-portfolio-' + pofoItemRef + '-link pofo-link-to field_type-select')
                    .append(
                    $('<label/>')
                        .html('Link to')
                )
                    .append(
                    $('<span/>').append(
                        $('<select/>')
                            .data('ref', pofoItemRef)
                            .attr('dialog-field', 'portfolio-' + pofoItemRef + '-link')
                            .attr('data-style-field-type', 'select')
                            .addClass('content-block-options-' + pofoItemRef + '-link')
                            .append(
                            $('<option value="">None</option>' +
                            '<option value="link">Webpage</option>' +
                            '<option value="libox">Lightbox</option>')
                        )
                    )
                )
            )
                .append(
                //WEB PAGE URL
                $('<div/>')
                    .addClass('field field-portfolio-' + pofoItemRef + '-url pofo-url field_type-text')
                    .append(
                    $('<label/>')
                        .html('Webpage URL')
                )
                    .append(
                    $('<span/>').append(
                        $('<input/>')
                            .data('ref', pofoItemRef)
                            .attr('dialog-field', 'portfolio-' + pofoItemRef + '-url')
                            .attr('data-style-field-type', 'text')
                            .addClass('content-block-options-' + pofoItemRef + '-url')
                    )
                )
            )
                .append(
                //NEW PAGE
                $('<div/>')
                    .addClass('field field-portfolio-' + pofoItemRef + '-new-page pofo-new-page field_type-checkbox')
                    .append(
                    $('<label/>')
                        .html('New page')
                )
                    .append(
                    $('<span/>').append(
                        $('<input/>')
                            .data('ref', pofoItemRef)
                            .attr('type', 'checkbox')
                            .val('1')
                            .attr('dialog-field', 'portfolio-' + pofoItemRef + '-new-page')
                            .attr('data-style-field-type', 'text')
                            .addClass('content-block-options-' + pofoItemRef + '-new-page')
                    )
                )
            )
        );
    }
});