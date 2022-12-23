/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */
define([
    'jquery',
    'productGallery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/backend/tree-suggest',
    'mage/backend/validation',
    'newVideoDialog',
    'newUploadVideoDialog'
], function ($, productGallery) { 
    'use strict';

    $.widget('mage.productGallery', productGallery, {

        /**
         * Bind events
         * @private
         */
        _bind: function () {
            var events = {},
                itemId;

            this._super();

            /**
             * Add item_id value to opened modal
             * @param {Object} event
             */
            events['click ' + this.options.imageSelector] = function (event) {
                
                if (!$(event.currentTarget).is('.ui-sortable-helper')) {
                    itemId = $(event.currentTarget).find('input')[0].name.match(/\[([^\]]*)\]/g)[2];
                    //console.log(itemId);
                    this.videoDialog.find('#item_id').val(itemId);
                    
                    //this.uploadVideoDialog.find('#uploaded_item_id').val(itemId);
                }
            };
            this._on(events);
            this.element.prev().find('[data-role="add-video-button"]').on('click', this.showModal.bind(this));
            this.element.prev().find('[data-role="upload-video-button"]').on('click', this.showUploadModal.bind(this));
            this.element.on('openDialog', '.gallery.ui-sortable', $.proxy(this._onOpenDialog, this));
        },

        /**
         * @private
         */
        _create: function () {
            this._super();
            this.videoDialog = this.element.find('#new-video');
            this.videoDialog.mage('newVideoDialog', this.videoDialog.data('modalInfo'));
            
            this.uploadVideoDialog = this.element.find('#upload-video');
            this.uploadVideoDialog.mage('newUploadVideoDialog', this.uploadVideoDialog.data('modalInfo'));
            
        },

        /**
         * Open dialog for external video
         * @private
         */
        _onOpenDialog: function (e, imageData) {
             
            if (imageData['media_type'] !== 'external-video' && imageData['media_type'] !== 'external-uploaded-video') {
                this._superApply(arguments);
            } else {
                if (imageData['media_type'] == 'external-video') {
                    this.showModal();
                } else if(imageData['media_type'] == 'external-uploaded-video'){
                    //this.showUploadModal(imageData);
                }
            }
        },

        /**
         * Fired on trigger "openModal"
         */
        showModal: function () {
            this.videoDialog.modal('openModal');
        },
         /**
         * Fired on trigger "openModal"
         */
        showUploadModal: function (arga) {
            if(arga){
                var data = arga[1] || [];
                if(data.length>0){
                    var popelements = {
                        'uploaded_item_id' : 'value_id',
                        'uploaded_file_name' : 'video_url',
                        'uploaded_file_path' : 'video_url',
                        'uploaded_video_url' : 'video_url', 
                        'uploaded_video_title' : 'video_title',
                        'uploaded_video_description' : 'video_description',
                        'new_uploaded_video_screenshot_preview' : 'file'
                    };
                    for (var i in popelements){
                        var val = data[popelements[i]] || '';
                        this.uploadVideoDialog.find('#'+i, val);
                    } 
                }
            }
            this.uploadVideoDialog.modal('openModal');
        }
        
    });

    return $.mage.productGallery;
});

