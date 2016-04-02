app.directive('tinymce', ['$timeout', function ($timeout) {
    'use strict';
    var generatedId = 0;

    var updateTinymceContent = function (id) {
        try {
            var flag = false;
            if(tinyMCE) {
                var value = jQuery('#' + id).val();

                var instant = tinyMCE.get(id);
                if(instant) {
                    instant.setContent(value);
                    flag = true;
                }
            }
            if(!flag) {
                $timeout(function () {
                    updateTinymceContent(id);
                }, 400);
            }
        } catch (e) {
            $timeout(function () {
                updateTinymceContent(id);
            }, 400);
        }
    };

    return function (scope, element, attrs) {
        if (!attrs.id) {
            attrs.$set('id', 'rte' + generatedId);
        }

        var uiTinymceConfig = $tinymceOptions.default;
        // var uiTinymceConfig = {
        //     //theme_advanced_fonts: Drupal.settings.tinyMCEFonts
        // };

        if (attrs.tinymce) {
            var expr = scope.$eval(attrs.tinymce);
            angular.extend(uiTinymceConfig, expr);
        }

        uiTinymceConfig.force_br_newlines = false;
        uiTinymceConfig.force_p_newlines = true;
        uiTinymceConfig.forced_root_block = 'p';
        //uiTinymceConfig.theme_advanced_fonts = Drupal.settings.tinyMCEFonts;
        uiTinymceConfig.convert_urls = false;
        uiTinymceConfig.paste_data_images = false;
        uiTinymceConfig.paste_block_drop = true;
        uiTinymceConfig.paste_auto_cleanup_on_paste = true;

        $timeout(function () {
            uiTinymceConfig.mode = 'exact';
            uiTinymceConfig.elements = attrs.id;

            if ( uiTinymceConfig.version !=='undefined' && uiTinymceConfig.version == "4" ) {
                uiTinymceConfig.setup = function(ed) {
                    ed.on('change', function(ed, l) {
                        ed.save();

                        scope.dataHasChanged();
                        if(scope.tinyMceChange) {
                            scope.tinyMceChange();
                        }
                    });
                    ed.on('SetContent', function(ed, o) {
                        tinymce.triggerSave();
                    });

                    ed.on('Init', function(ed) {
                        if (tinymce.isIE) {
                            tinymce.dom.Event.add(ed.getBody(), "dragenter", function(e) {
                                return tinymce.dom.Event.cancel(e);
                            });
                        }
                        else {
                            tinymce.dom.Event.add(ed.getBody().parentNode, "drop", function(e) {
                                tinymce.dom.Event.cancel(e);
                                tinymce.dom.Event.stop(e);
                            });

                        }
                    });
                };
            }
            else {
                uiTinymceConfig.setup = function(ed) {
                    ed.onChange.add(function (ed, l) {
                        ed.save();

                        scope.dataHasChanged();
                        if (scope.tinyMceChange) {
                            scope.tinyMceChange();
                        }
                    });
                    ed.onSetContent.add(function (ed, o) {
                        ed.save();
                    });

                    ed.onInit.add(function (ed) {
                        if (tinymce.isIE) {
                            tinymce.dom.Event.add(ed.getBody(), "dragenter", function (e) {
                                return tinymce.dom.Event.cancel(e);
                            });
                        }
                        else {
                            tinymce.dom.Event.add(ed.getBody().parentNode, "drop", function (e) {
                                tinymce.dom.Event.cancel(e);
                                tinymce.dom.Event.stop(e);
                            });

                        }
                    });
                }
            }


            uiTinymceConfig.paste_preprocess = function(pl, o) {
                // Content string containing the HTML from the clipboard
                if ( /<img.*\ssrc\s*=\s*"data:/i.test(o.content) ) {
                    o.content = '';
                    alert("Pasting images is prohibited! Upload/attach the image instead.");
                }

            };
            uiTinymceConfig.paste_postprocess = function(pl, o) {

            };

            tinyMCE.init(uiTinymceConfig);

            updateTinymceContent(attrs.id);
        }, 400);

        generatedId++;
    };
}]);

app.directive('multipleEmail', ['$timeout', function () {
    var EMAIL_REGEXP = /^[a-z0-9!#$%&'*+/=?^_`{|}~.-]+@[a-z0-9-]+(\.[a-z0-9-]+)*$/i;

    function validateAll(ctrl, validatorName, value) {
        var validity = ctrl.$isEmpty(value) || value.split(',').every(
                function (email) {
                    return EMAIL_REGEXP.test(email.trim());
                }
            );

        ctrl.$setValidity(validatorName, validity);
        return validity ? value : undefined;
    }

    return {
        restrict: 'A',
        require: 'ngModel',
        link: function postLink(scope, elem, attrs, modelCtrl) {
            function multipleEmailsValidator(value) {
                return validateAll(modelCtrl, 'multipleEmails', value);
            }

            modelCtrl.$formatters.push(multipleEmailsValidator);
            modelCtrl.$parsers.push(multipleEmailsValidator);
        }
    };
}]);

app.directive('preventDefault', ['$timeout', function() {
    return function(scope, element, attrs) {
        element.bind('click', function(event) {
            event.preventDefault();
        });
    };
}]);

app.directive('autoUrl', ['$timeout', function($timeout) {
    /*var url = this.replace(/[^0-9a-zA-Z]/g, " ");
     url = url.replace(/\s\s+/g, " ").trim();
     url = url.replace(/\s/g, "-");*/
    //return url.toLowerCase();

    return {
        restrict: 'A',
        link: function(scope, elm, attrs) {
            $timeout(function() {
                if ( elm.hasClass('ng-pristine') && !elm.val() ) {
                    var title = attrs.autoUrl;
                    if ( title ) {
                        scope.$watch(title, function() {
                            var url = scope.$eval(title);
                            if ( url ) {
                                url = url.replace(/[^0-9a-zA-Z]/g, " ");
                                url = url.replace(/\s\s+/g, " ").trim();
                                url = url.replace(/\s/g, "-");
                                url = url.toLowerCase();
                                elm.val(url);

                                if ( scope.autoUrlChange ) {
                                    scope.autoUrlChange();
                                }
                            }
                        }, true);
                    }
                }
            }, 300);
        }
    };
}]);

app.directive('minicolors', ['$timeout', function($timeout) {
    return function(scope, element, attrs) {
        var options = {
            letterCase: 'uppercase'
        };
        if(attrs.minicolors) {
            var expr = scope.$eval(attrs.tinymce);
            angular.extend(options, expr);
        }
        $timeout(function() {
            element.minicolors(options);
        }, 500);
    };
}]);

app.directive('defaultcolor', ['$timeout', function() {
    return function(scope, element, attrs) {
        element.click(function() {
            var id = element.attr('data-origin');
            if ( id ) {
                var $color = jQuery('#' + id);
                $color.minicolors('value', attrs.defaultcolor);
            }
        });
    };
}]);

app.directive('fileupload', ['$timeout', function($timeout) {
    return function (scope, element, attrs) {
        if (attrs.id) {
            var id = '#' + attrs.id;
            attrs.$set('tabindex', -1);
            attrs.$set('style', 'position: absolute; left: -9999px;');

            $timeout(function () {
                var $text = jQuery(id + 'Text');
                var $button = jQuery(id + 'Button');
                var $message = jQuery(id + 'Message');
                var $error_message = jQuery(id + 'ErrorMessage');
                var $delete = jQuery(id + 'Delete');
                var old_file = null;

                $button.attr('tabindex', -1);
                $button.click(function () {
                    old_file = $text.val();
                    element.focus().trigger('click');
                });

                var options = null;
                if (attrs.fileupload ) {
                    options = scope.$eval(attrs.fileupload);
                }

                //Process delete temporate file
                $delete.click(function (e) {
                    e.preventDefault();
                    if (options && options.auto_upload) {
                        if ($text.hasClass('alert-border')) {
                            $text.removeClass('alert-border').removeClass('invalid-image');
                            $button.removeClass('alert-border');
                            $error_message.hide();
                            $text.val('');
                            $delete.hide();

                            return;
                        }

                        var data = new FormData();
                        data.append('form_build_id', options.form_build_id);
                        data.append('form_token', options.form_token);
                        data.append('form_id', options.form_id);
                        data.append('file_name', $text.val());
                        data.append('data_type', element.attr('data-type'));

                        jQuery.ajax({
                            url: options.delete_path,
                            type: 'POST',
                            data: data,
                            cache: false,
                            dataType: 'json',
                            processData: false, // Don't process the files
                            contentType: false, // Set content type to false as
                            //jQuery will tell the server its a query string request
                            beforeSend: function () {
                                $message.text($message.attr('data-deleting')).show();
                                $text.addClass('processing');
                                jQuery('#accm-submit-button-upload').show();
                                jQuery('#accm-submit-button').hide();
                            },
                            success: function (response) {
                                $text.removeClass('processing');
                                //scope.file...
                                $message.hide();
                                $error_message.hide();
                                element.replaceWith(element = element.clone(true));
                                element.trigger('change');
                                element.val('');
                                $text.val('').removeAttr('title');
                                jQuery('#show-icon-download').hide();
                                $delete.hide();
                                //scope.$apply();
                                if (scope.fileUploadChange) {
                                    scope.fileUploadChange();
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                $message.text($message.attr('data-error')).show();
                                console.log('disable');
                            }
                        });
                    }
                    else {
                        element.replaceWith(element = element.clone(true));
                        element.trigger('change');
                        element.val('');
                        $text.val('').removeAttr('title');
                        $delete.hide();
                    }
                });


                element.change(function (evt) {
                    var filename = element.val().split('\\').pop();

                    if (options && options.validate_size) {
                        var validator_size = 2048 * 1024 * 5;
                        var fileInput = jQuery('#uxLogo');
                        if (fileInput.get(0).files.length) {
                            if (validator_size < this.files[0].size) {
                                //console.log('File size:' + this.files[0].size);
                                jQuery("#uxLogoFileSize").show();
                                $text.val(filename) // Set the value
                                    .attr('title', filename) // Show filename in title tootlip
                                    .focus(); // Regain focus
                                return;
                            } else {
                                jQuery("#uxLogoFileSize").hide();
                            }
                        }
                    }
                   
                    $text.val(filename) // Set the value
                        .attr('title', filename) // Show filename in title tootlip
                        .focus(); // Regain focus
                    
                    if (!options) {
                        if (scope.fileUploadChange) {
                            scope.fileUploadChange();
                        }
                        return;
                    }

                    //Process auto upload when user choose file
                    if (options.auto_upload) {
                        var files = (evt.srcElement || evt.target).files;
                        if (files.length > 0) {
                            scope.$apply(function () {
                                /*var filesUpload = new Array();
                                 for ( var i = 0; i < files.length; i++ ) {
                                 filesUpload.push(files[i]);
                                 }*/

                                var data = new FormData();
                                data.append('form_build_id', options.form_build_id);
                                data.append('form_token', options.form_token);
                                data.append('form_id', options.form_id);
                                data.append('data_type', element.attr('data-type'));

                                if (old_file) {
                                    data.append('old_file_name', old_file);
                                }

                                jQuery.each(files, function (key, value) {
                                    data.append(key, value);
                                });

                                jQuery.ajax({
                                    url: options.upload_path,
                                    type: 'POST',
                                    data: data,
                                    cache: false,
                                    dataType: 'json',
                                    processData: false, // Don't process the files
                                    contentType: false, // Set content type to false as
                                    //jQuery will tell the server its a query string request
                                    beforeSend: function () {
                                        $error_message.hide();
                                        $text.removeClass('alert-border').removeClass('invalid-image');
                                        $text.addClass('processing');
                                        $button.removeClass('alert-border');
                                        $message.text($message.attr('data-loading')).show();
                                        jQuery('#accm-submit-button-upload').show();
                                        jQuery('#accm-submit-button').hide();
                                    },
                                    success: function (data) {
                                        $text.removeClass('processing');
                                        jQuery('#accm-submit-button-upload').hide();
                                        jQuery('#accm-submit-button').show();
                                        //scope.file...
                                        jQuery('#show-icon-download').show();
                                        $message.text('').hide();
                                        if (!data.status) {
                                            $error_message.text(data.error).show();
                                            //$text.val('');
                                            element.val('');
                                            $delete.show();
                                            $text.addClass('alert-border invalid-image');
                                            $button.addClass('alert-border');
                                            jQuery('#accm-submit-button-upload').show();
                                            jQuery('#accm-submit-button').hide();
                                        }
                                        else {
                                            //$error_message.text($error_message.attr('data-empty')).show();
                                            $text.val(data.name);
                                            element.val('');
                                            $delete.show();
                                            jQuery('#accm-submit-button-upload').hide();
                                            jQuery('#accm-submit-button').show();
                                        }

                                        if (scope.fileUploadChange) {
                                            scope.fileUploadChange();
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        $message.text($message.attr('data-error')).show();

                                    }
                                });
                            });
                        }
                    } //-----end auto upload
                    else {
                        if (scope.fileUploadChange) {
                            scope.fileUploadChange();
                        }
                    }

                });

                $text.on({
                    blur: function () {
                        element.trigger('blur');
                    },
                    keydown: function (e) {
                        if (e.which === 13) { // Enter
                            e.preventDefault();
                            var isIE = /msie/i.test(navigator.userAgent); // simple but not super secure...
                            if (!isIE) {
                                //element.trigger('click');
                                element.focus().click();
                            }
                        }
                        else if (e.which === 8 || e.which === 46) { // Backspace & Del
                            if (!element.val() && !$text.val()) {
                                return;
                            }
                            if (options && options.auto_upload) {
                                $delete.trigger('click');
                                return;
                            }
                            // On some browsers the value is read-only
                            // with this trick we remove the old input and add
                            // a clean clone with all the original events attached
                            element.replaceWith(element = element.clone(true));
                            element.trigger('change');
                            element.val('');
                            $text.val('').removeAttr('title');
                            //alert(element.val());
                        }
                        else if (e.which === 9) { // TAB
                            return;
                        }
                        else { // All other keys
                            return false;
                        }
                    }
                });
            }, 400);
        }
    };
}]);
// angular.module('appDirectives', []).directive(directive);