/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
humhub.module('linkpreview', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var string = require('util').string;
    var loader = require('ui.loader');
    var client = require('client');

    /**
     * Helper class used for extracting metadata from the given fetch server response.
     *
     * @param {type} url
     * @param {type} fetchUrl
     * @returns {humhub.linkpreviewL#6.PageInfo}
     */
    var PageInfo = function (url, fetchUrl) {
        this.url = url;
        this.fetchUrl = fetchUrl;
    };

    /**
     * Fetches the metadata for the given url.
     * @returns Promise
     */
    PageInfo.prototype.fetch = function () {
        var that = this;
        return client.post(this.fetchUrl, {data: {url: this.url}}).then(function (response) {
            that.meta = response.output;
            return that;
        });
    };

    /**
     * Extracts the errors from fetching of a link preview.
     * @returns string
     */
    PageInfo.prototype.getErrors = function () {
        if (!this.errors) {
            this.errors = this.meta['errors'];
        }
        return this.errors;
    };

    /**
     * Extracts the title metadata either from 'og:title' meta tag or page 'title'.
     * @returns string
     */
    PageInfo.prototype.getTitle = function () {
        if (!this.title) {
            this.title = this.meta['og:title'];
        }
        return this.title;
    };

    /**
     * Extracts the description metadata either from meta tag 'og:description' or meta tag with name="Description"
     * @returns string
     */
    PageInfo.prototype.getDescription = function () {
        if (!this.description) {
            this.description = this.meta['og:description'];
        }
        return this.description;
    };

    /**
     * Extracts the image url from meta tags 'og:image'.
     * @returns {Array}
     */
    PageInfo.prototype.getImages = function () {
        if (!this.images) {
            var that = this;
            this.images = [];
            $.each(this.meta['og:image'], function (index, img) {
                that.images.push(img['og:image:url']);
            });
        }
        return this.images;
    };

    /**
     * Returns the image count.
     * @returns {humhub.linkpreviewL#6.PageInfo.prototype.getImages.length}
     */
    PageInfo.prototype.getImageCount = function () {
        return this.getImages().length;
    };

    /**
     * Checks if the pageInfo could extract an title.
     * @returns {Boolean}
     */
    PageInfo.prototype.hasTitle = function () {
        return this.getTitle() && this.getTitle().length;
    };

    /**
     * LinkPreviewEditor widget.
     *
     * @param {type} node
     * @param {type} options
     * @returns {humhub.linkpreviewL#6.LinkPreviewEditor}
     */
    var LinkPreviewEditor = LinkPreview = Widget.extend();

    LinkPreviewEditor.prototype.init = function () {
        var that = this;
        this.richtext = Widget.instance(this.$.data('richtext-selector'));

        this.richtext.editor.context.event.on('linkified', function (evt, result) {
            var url = result[0];

            if (!url || !/^https?:\/\//i.test(url)) {
                return;
            }

            that.fetchUrl(result[0])
        }).on('clear', function () {
            that.reset();
        });


        this.$.on('click', '.preview-title-text', function () {
            $(this).hide();
            that.$.find('.title-input').show().focus();
        });

        this.$.on('focusout', '.title-input', function () {
            var $this = $(this).hide();
            that.$.find('.preview-title-text').show().text($this.val());
        });

        this.$.on('click', '.preview-description-text', function () {
            $(this).hide();
            that.$.find('.description-input').show().focus();
        });

        this.$.on('focusout', '.description-input', function () {
            var $this = $(this).hide();
            that.$.find('.preview-description-text').show().text($this.val());
        });

        this.$.on('click', '.btn-remove', function () {
            that.reset();
        });

        this.$.closest('form').on('submit', function () {
            alert('asdf');
        });
    };

    LinkPreviewEditor.prototype.reset = function () {
        this.hide();
        this.$.data('fetched', false);
        this.setPreviewErrors();
        this.setPreviewTitle();
        this.setPreviewUrl();
        this.setPreviewImage();
        this.setPreviewDescription();
    };

    /**
     * Searches an url within the richtext and fetches the metadata.
     * And rerenders the link preview and set the additional linkpreview form field values.
     * @returns {undefined}
     */
    LinkPreviewEditor.prototype.fetchUrl = function (url) {
        if (this.$.data('fetched')) {
            return;
        }

        if (url) {
            this.setInputUrl(url);
            this.loader();

            var that = this;
            new PageInfo(url, this.options.fetchUrl).fetch().then(function (info) {
                that.loader(false);

                if (!info.meta) {
                    that.reset();
                    return;
                }

                that.pageInfo = info;

                if (!that.updatePreview()) {
                    that.reset();
                }
            }).catch(function (e) {
                module.log.error(e);
                that.loader(false);
                that.reset();
            });
        } else {
            this.fadeOut('fast');
        }
    };

    LinkPreviewEditor.prototype.updatePreview = function () {
        if (!this.pageInfo.hasTitle()) {
            return false;
        }

        this.setPreviewErrors(this.pageInfo.getErrors());
        this.setPreviewTitle(this.pageInfo.getTitle());
        this.setPreviewDescription(this.pageInfo.getDescription());
        this.setPreviewImage(this.pageInfo.getImages());
        this.setPreviewUrl(this.pageInfo.url);
        this.$.data('fetched', true);

        return true;
    };

    LinkPreviewEditor.prototype.setPreviewErrors = function (errors) {
        this.$.find('.preview-errors ul').html(errors && errors.length ? '<li>' + errors.join('</li><li>') + '</li>' : '');
        if (errors && errors.length) {
            this.$.find('.preview-errors').show();
        } else {
            this.$.find('.preview-errors').hide();
        }
    };

    LinkPreviewEditor.prototype.setPreviewTitle = function (title) {
        this.$.find('.title-input').val(title || '');
        this.$.find('.preview-title-text').text(title || '');
    };

    LinkPreviewEditor.prototype.setPreviewDescription = function (description) {
        this.$.find('.description-input').val(description || '');
        this.$.find('.preview-description-text').text(description || '');
    };

    LinkPreviewEditor.prototype.setPreviewImage = function (images) {
        // Set first image value
        this.$.find('.image-input')
            .val((images && images.length) ? images[0] : '');

        if (!images) {
            return;
        }

        var that = this;
        var $imageRoot = that.$.find('.media-image').remove('img');
        $.each(images, function (i, image) {
            var $image = $(string.template(module.template.image, {
                index: i,
                url: image
            }));

            $imageRoot.prepend($image);

            if (i !== 0) {
                $image.hide();
            }
        });

        if (images.length > 1) {
            this.$.find('.image-controls').show().find('.total').text(images.length);
        }

    };

    LinkPreviewEditor.prototype.actionNext = function (evt) {
        this.$.find('.btn-prev-thumbnail').removeClass('disabled');
        var $current = this.$.find('.media-object:visible').hide();

        var nextIndex = $current.data('index') + 1;
        var $next = this.$.find('[data-index="' + nextIndex + '"]').show();
        this.$.find('.image-input').val($next.attr('src'));
        this.$.find('.current').text(nextIndex);

        // disable button if no more images are available
        if (nextIndex === this.pageInfo.getImageCount() - 1) {
            evt.$trigger.addClass('disabled');
        }
    };

    LinkPreviewEditor.prototype.actionPrevious = function (evt) {
        this.$.find('.btn-next-thumbnail').removeClass('disabled');
        var $current = this.$.find('.media-object:visible').hide();

        var prevIndex = $current.data('index') - 1;
        var $prev = this.$.find('[data-index="' + prevIndex + '"]').show();
        this.$.find('.image-input').val($prev.attr('src'));
        this.$.find('.current').text(prevIndex);

        // disable button if no more images are available
        if (prevIndex === 0) {
            evt.$trigger.addClass('disabled');
        }
    };

    LinkPreviewEditor.prototype.setPreviewUrl = function (url) {
        this.$.find('.preview-url-text').text(url);
    };

    module.template = {
        image: '<img class="media-object" data-index="{thumbnail}" alt="80x80" rendered="true" src="{url}" style="width: 80px;">'
    };

    LinkPreviewEditor.prototype.loader = function ($show) {
        this.fadeIn('fast');
        if ($show === false) {
            loader.reset(this.$);
        } else {
            this.$.find('.media-image img').remove();
            loader.set(this.$);
        }
    };

    LinkPreviewEditor.prototype.setInputUrl = function (url) {
        this.$.find('.url-input').val(url);
    };

    var init = function () {
        // Init LinkPreview on richtext focus
        $(document).on('focus', '.humhub-ui-richtext', function () {
            var richtext = Widget.closest($(this));
            Widget.instance(richtext.$.siblings('.preview-editor'));
        });
    };

    /**
     * LinkPreview widget.
     *
     * @param {type} node
     * @param {type} options
     * @returns {humhub.linkpreviewL#6.LinkPreview}
     */
    var LinkPreview = Widget.extend();

    LinkPreview.prototype.init = function () {
        var $postContent = this.$.closest('.content');
        if ($postContent.length) {
            $postContent.append(this.$.show());
        }
    };

    module.export({
        init: init,
        LinkPreviewEditor: LinkPreviewEditor,
        LinkPreview: LinkPreview
    });

});

