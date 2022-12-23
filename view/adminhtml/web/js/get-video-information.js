/**
 * @category  SixtySeven
 * @package   SixtySeven_ProductVideo
 * @author    67Digital http://67digital.com/
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
    'mage/translate'
], function ($, alert) {
        'use strict';

        var videoRegister = {
            _register: {},

            /**
             * Checks, if api is already registered
             *
             * @param {String} api
             * @returns {bool}
             */
            isRegistered: function (api) {
                return this._register[api] !== undefined;
            },

            /**
             * Checks, if api is loaded
             *
             * @param {String} api
             * @returns {bool}
             */
            isLoaded: function (api) {
                return this._register[api] !== undefined && this._register[api] === true;
            },

            /**
             * Register new video api
             * @param {String} api
             * @param {bool} loaded
             */
            register: function (api, loaded) {
                loaded = loaded || false;
                this._register[api] = loaded;
            }
        };

        $.widget('mage.uploadedProductVideoLoader', {

            /**
             * @private
             */
            _create: function () {
                //console.log('which element');
               // console.log(this.element);
                this.element.videoExternal();
                this._player = this.element.data('magevideoExternal');
                        
            },

            /**
             * Initializes variables
             * @private
             */
            _initialize: function () {
                this._params = this.element.data('params') || {};
                this._code = this.element.data('code');
                this._width = this.element.data('width');
                this._height = this.element.data('height');
                this._autoplay = !!this.element.data('autoplay');
                this._mimetype = this.element.data('mimetype');
                this._video_url = this.element.data('video_url');
                this._playing = this._autoplay || false;

                this._responsive = this.element.data('responsive') !== false;

                if (this._responsive === true) {
                    this.element.addClass('responsive');
                }

                this._calculateRatio();
            },

            /**
             * Abstract play command
             */
            play: function () {
                this._player.play();
            },

            /**
             * Abstract pause command
             */
            pause: function () {
                this._player.pause();
            },

            /**
             * Abstract stop command
             */
            stop: function () {
                this._player.stop();
            },

            /**
             * Abstract playing command
             */
            playing: function () {
                return this._player.playing();
            },

            /**
             * Abstract destroying command
             */
            destroy: function () {
                this._player.destroy();
            },

            /**
             * Calculates ratio for responsive videos
             * @private
             */
            _calculateRatio: function () {
                if (!this._responsive) {
                    return;
                }
                this.element.css('paddingBottom', this._height / this._width * 100 + '%');
            }
        });

        $.widget('mage.videoExternal', $.mage.uploadedProductVideoLoader, {

            /**
             * Initialize the Vimeo widget
             * @private
             */
            _create: function () {
                var timestamp,
                    src,
                    additionalParams,
                    autoplay,
                    videohtml;

                this._initialize();
                timestamp = new Date().getTime();
                autoplay= '';
                if (this._autoplay) {
                    autoplay += 'autoplay';
                }

                src = this._video_url + '?__t='+timestamp;
                videohtml = '<video width="'+this._width+'" height="'+this._height+'" '+autoplay+' controls><source src="'+src+'" type="'+this._mimetype+'">Your browser does not support the video tag.</video>';
                this.element.append(
                      videohtml
                );
             
            }
        });

        $.widget('mage.toUploadVideoData', {
            options: {
                youtubeKey: '',
                eventSource: '', //where is data going from - focus out or click on button
                otherOptions: '' // options for video dialog to get save urls 
            },

            _REQUEST_VIDEO_INFORMATION_TRIGGER: 'request_video_information',

            _UPDATE_VIDEO_INFORMATION_TRIGGER: 'updated_video_information',

            _START_UPDATE_INFORMATION_TRIGGER: 'update_video_information',

            _ERROR_UPDATE_INFORMATION_TRIGGER: 'error_updated_information',

            _FINISH_UPDATE_INFORMATION_TRIGGER: 'finish_update_information',
            
            _VIDEO_UPLOADED_FINISHED: 'finish_video_upload',

            _VIDEO_URL_VALIDATE_TRIGGER: 'validate_video_url',

            _videoInformation: null,

            _currentVideoUrl: null,
            
            _videoFormSelector: '#new_uploaded_video_form',
            
            _videoDisableinputSelector: '#new_uploaded_video_disabled',
            /**
             * @private
             */
            _init: function () {
                this.element.on(this._START_UPDATE_INFORMATION_TRIGGER, $.proxy(this._onRequestHandler, this));
                this.element.on(this._ERROR_UPDATE_INFORMATION_TRIGGER, $.proxy(this._onVideoInvalid, this));
                this.element.on(this._FINISH_UPDATE_INFORMATION_TRIGGER, $.proxy(
                    function () {
                        this._currentVideoUrl = null;
                    }, this
                ));
                this.element.on(this._VIDEO_URL_VALIDATE_TRIGGER, $.proxy(this._onUrlValidateHandler, this));
            },

            /**
             * @private
             */
            _onUrlValidateHandler: function (event, callback, forceVideo) {
                var url = this.element.val(),
                    videoInfo;

                videoInfo = this._validateURL(url, forceVideo);

                if (videoInfo) {
                    callback();
                } else {
                    this._onRequestError($.mage.__('Invalid video format'));
                }
            },

            /**
             * @private
             */
            _onRequestHandler: function () {
                var url = this.element.val(),
                    self = this,
                    videoInfo,
                    type,
                    id,
                    googleapisUrl, inputFile;

                if (this._currentVideoUrl === url) {
                    return;
                }

                this._currentVideoUrl = url;

                this.element.trigger(this._REQUEST_VIDEO_INFORMATION_TRIGGER, {
                    url: url
                });

                if (!url) {
                    return;
                }

                videoInfo = this._validateURL(url);

                if (!videoInfo) {
                    this._onRequestError($.mage.__('Invalid video format'));

                    return;
                }
                 
                var fileName = this.element[0].files;
                //console.log(fileName);
                if (!fileName || !fileName.length) {
                    fileName = null;
                }
                 
                _uploadVideo(fileName, function (data){
                    var oldFile  = data.oldFile;
                    delete data['oldFile'];
                    var respData = {
                        duration: '',
                        channel: '',
                        channelId: '',
                        uploaded: new Date().toUTCString(),
                        title: data['name'],
                        description: '',
                        thumbnail: '',
                        videoId: '',
                        videoProvider: 'custom_upload',
                        video_url: data['url'],
                        mimetype: data['type'],
                        up_result: JSON.stringify(data),
                        up_oldFile: oldFile
                    };
                    self._videoInformation = respData;
                    self.element.trigger(self._UPDATE_VIDEO_INFORMATION_TRIGGER, respData);
                    self.element.trigger(self._FINISH_UPDATE_INFORMATION_TRIGGER, true);
                    self.element.trigger(self._VIDEO_UPLOADED_FINISHED, respData);
                });
                
                /**
                 *
                 * Wrap _uploadVideo
                 * @param {String} file
                 * @param {String} oldFile
                 * @param {Function} callback
                 * @private
                 */
                function _uploadVideo (file, oldFile, callback) {
                    var url = ""+self.options.otherOptions.uploadVideoUrl;
                    
                    if(url.indexOf('?')>0) {
                        var url = url+"&form_key="+window.FORM_KEY;
                    }else{
                        var url = url+"?form_key="+window.FORM_KEY;
                    }
                    
                    var data = {
                        files: file[0],
                        url: url
                    };
                    //console.log(data);
                    //console.log('start upload')
                    jQuery('body').loader('show');
                    _uploadVideoFile(data, $.proxy(function (result) {
                        this._onVideoUploaded(result, file, oldFile, callback);
                         
                    }, self));

                }
                
                /**
                 * File uploader
                 * @private
                 */
                function _uploadVideoFile (data, callback) {
                    var fu = self.element.find('[name="uploaded_video_url"]'),
                        tmpInput = document.createElement('input'),
                        
                        fileUploader = null;

                    $(tmpInput).attr({
                        'name': 'uploaded_video_url_tmp',
                        'value': fu.val(),
                        'type': 'file',
                        'data-ui-ud': fu.attr('data-ui-ud')
                    }).css('display', 'none');
                    
                     
                    
                    fu.parent().append(tmpInput);
                    
                    
                    console.log('before upload');
                    fileUploader = $(tmpInput).fileupload();
                
                    console.log('trigger upload');
                    fileUploader.fileupload('send', data).success(function (result, textStatus, jqXHR) {
                        
                        callback.call(null, result, textStatus, jqXHR);
                    });
                }
                 

                
            },
            /**
             * @param {String} result
             * @param {String} file
             * @param {String} oldFile
             * @param {Function} callback
             * @private
             */
            _onVideoUploaded : function (result, oldFile, callback) {
                var data = JSON.parse(result);
                var that = this;
                if (that.element.parent().find('.image-upload-error').length > 0) {
                    that.element.parent().find('.image-upload-error').remove();
                }

                if (data.errorcode || data.error) {
                    that.element.parent().append('<div class="image-upload-error">' +
                    '<div class="image-upload-error-cross"></div><span>' + data.error + '</span></div>');

                    return;
                }
                
                var uploadedpath = data.uploaded_path;
                
                $.each(that.element.find(that._videoFormSelector).serializeArray(), function (i, field) {
                    data[field.name] = field.value;
                });
                data.disabled = that.element.find(that._videoDisableinputSelector).attr('checked') ? 1 : 0;
                data['media_type'] = 'external-uploaded-video';
                data['uploaded_file_path'] = uploadedpath;
                //console.log(that.element.find(that._videoFormSelector));
                var celement = that.element.closest(that._videoFormSelector).find('#uploaded_file_path');
                console.log($(celement))
                $(celement).val(uploadedpath);
                data.oldFile = oldFile;
                 
                callback.call(0, data);
            },
            /**
             * @private
             */
            _onVideoInvalid: function (event, data) {
                this._videoInformation = null;
                this.element.val('');
                alert({
                    content: 'Error: "' + data + '"'
                });
            },

            /**
             * @private
             */
            _onRequestError: function (error) {
                this.element.trigger(this._ERROR_UPDATE_INFORMATION_TRIGGER, error);
                this.element.trigger(this._FINISH_UPDATE_INFORMATION_TRIGGER, false);
                this._currentVideoUrl = null;
            },

            /**
             * @private
             */
            _formatYoutubeDuration: function (duration) {
                var match = duration.match(/PT(\d+H)?(\d+M)?(\d+S)?/),
                    hours = parseInt(match[1], 10) || 0,
                    minutes = parseInt(match[2], 10) || 0,
                    seconds = parseInt(match[3], 10) || 0;

                return this._formatVimeoDuration(hours * 3600 + minutes * 60 + seconds);
            },

            /**
             * @private
             */
            _formatVimeoDuration: function (seconds) {
                return (new Date(seconds * 1000)).toUTCString().match(/(\d\d:\d\d:\d\d)/)[0];
            },

            /**
             * @private
             */
            _parseHref: function (href) {
                var a = document.createElement('a');

                a.href = href;

                return a;
            },

            /**
             * @private
             */
            _validateURL: function (href, forceVideo) {
                
                var id,
                    type, extension;
                
                extension = href.substr( (href.lastIndexOf('.') +1) );
                var allowedExtension = ['mp4','ogg', 'ogv', 'webm']; //'flv','avi','wmv' may be added later
                
                if(allowedExtension.indexOf(extension)>=0){
                    id = href;
                    type = 'custom';
                } 
                return id ? {
                    id: id, type: type, s: href.replace(/^\?/, '')
                } : false;
            }
        });
    });

