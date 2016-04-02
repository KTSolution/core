"use strict";

app.controller('LanguageController',
    function($scope, $rootScope, $route, apiService, $timeout, $location, $compile, $sce) {
        console.log('language controller');

        $scope.fetched = false;
        $scope.saving = false;
        $scope.searching = false;
        $scope.reset = false;
        $scope.start_edit = false;

        var link = {
            list: '/website/language',
            edit: '/website/language@edit',
            save: '/website/language@save',
            change: '/website/language@change',
            delete: '/website/language@delete'
        };

        // init Tinymce
        $scope.tiny_options = $tinymceOptions.tiny;

        $scope.init = function (data) {
            $scope.link     = link;
            if ( typeof data != isInvalid ) {
                $scope.list     = data.list || {};
                $scope.global   = data.global || {};
                $scope.header   = data.header || {};
                $scope.filter   = data.filter || {};
                $scope.paging   = data.paging || {};
                $scope.gender   = data.gender || {};
            }
        };

        $scope.fetchPage = function (pageNum) {
            var params = {};
            if ( $scope.searching ) {
                if ( typeof $scope.filter.columns != isInvalid ) {
                    var filter = $scope.filter.columns;
                    for (var key in filter) {
                        var elm = '#filter' + key;
                        var value = jQuery(elm).val();
                        if ( value != isUndefined && value != isEmpty) {
                            if ( jQuery(elm).parent().hasClass("input-calendar") ) {
                                value = $scope.revertToString(value);
                            }
                            params[key] = value;
                        }
                    }
                }
            }

            if (  $scope.paging == isDefined ) {
                if (pageNum == 'n') pageNum = $scope.paging.PageNext;
                else if (pageNum == 'p') pageNum = $scope.paging.PagePrev;

                if ( (pageNum > 0 && pageNum <= $scope.paging.TotalPage) || ($scope.paging.TotalPage == 0) ) {
                    jQuery.extend(params, {pageNum: pageNum});
                    $scope.fetched = false;
                }
            }

            if ( !$scope.fetched ) {
                return apiService.list($scope.link.list, params).then(function (response) {
                    $scope.init(response.data);
                    $scope.fetched = true;
                    $scope.searching = false;
                    $timeout(function(){
                        $scope.loaded = true;
                    }, 1000);
                });
            }
        };

        $scope.edit = function(id){
            $scope.start_edit = true;
            $scope.submitted = false;

            apiService.get($scope.link.edit, id).then(function (response) {
                var data = response.data;
                $scope.member = {};
                if ( typeof data != isInvalid ) {

                    $scope.typeList = data.typeList;
                    $scope.statusList = data.statusList;

                    var member = data.member;
                    $scope.member.ID = id;
                    $scope.member.Type = __render(member.type);
                    $scope.member.Active = __render(member.enable);
                    $scope.member.FirstName = member.first_name;
                    $scope.member.LastName = member.last_name;
                    $scope.member.Email = member.email;
                    $scope.member.UserName = member.login_name;
                }
            });
        };

        $scope.change = function(id){
            
        };

        $scope.save = function(form){
            if ( $scope.saving ) {
                return;
            }
            $scope.submitted = true;

            if( !form.$invalid ){
                $scope.dataHasSaved();
                $scope.saving = true;
                apiService.save($scope.link.save, form).then(function (response) {
                    $timeout(function () {
                        $scope.saving = false;
                        jQuery('#modelEdit').modal('toggle');
                        $scope.init(response.data);
                    }, 1000);
                });
            }else{
                var $elm = jQuery('#modalEdit input.ng-invalid:first');
                $elm.focus();
                doc.scrollElementToCenter($elm);
            }
        };

        $scope.delete = function (id, name) {
            name = name ? name : '';
            $scope.IDDelete = id;

            var messageDelete = jQuery('#messageDelete').text();
            jQuery('#modalDelContent').html(messageDelete + ' ' + name + '?');
        };
        $scope.submitDel = function (id) {
            apiService.delete($scope.link.delete, id).then(function (response) {
                $timeout(function () {
                    jQuery('#modelDelete').modal('toggle');
                    $scope.init(response.data);
                }, 1000);
            });
        };

        $scope.find = function () {
            $scope.fetched = false;
            $scope.searching = true;
            $scope.fetchPage();
        };

        // Sort on header
        $scope.sort = function (field, enable) {
            if(enable=="true" || enable=="1" || enable==1 || enable==true){
                if ($scope.sorter.sortBy == field)
                    $scope.sorter.sortDir = $scope.sorter.sortDir == 'asc' ? 'desc' : 'asc';
                else {
                    $scope.sorter.sortBy = field;
                    $scope.sorter.sortDir = 'asc';
                }
                jQuery.extend(filterParams, {sortDir: $scope.sorter.sortDir, sortBy: $scope.sorter.sortBy});
                $scope.fetchPage($scope.paging.PageNum);
            }
        };

        $scope.sortList = function(list) {
            list.sort(function(a, b) {
                return (a.sort - b.sort);
            });
        };

        $scope.getLang = function(content, show) {
            try {
                var obj = JSON.parse(content);
                if (typeof obj == 'object')
                {
                    if (show == true)
                        return obj['en'];
                    else
                    if (typeof obj[$scope.languageCur] !== 'undefined')
                        return obj[$scope.languageCur];
                    else
                        return '';
                }
            } catch (e) {
                if (show == true)
                    return content;
                else
                    return '';
            }
        };

        //run
        if( $scope.tiny_options !== isInvalid ) tinyMCE.init($scope.tiny_options);
        $scope.init();
        $scope.fetchPage();

        $scope.ngDirtyInvalid = function(form, elementName) {
            return (form[elementName].$dirty
            && form[elementName].$invalid && $scope.submitted);
        };
        $scope.ngInvalid = function(form, elementName) {
            return (form[elementName].$invalid && $scope.submitted);
        };
        $scope.ngInvalidEmail = function(form, elementName) {
            return (form[elementName].$error.multipleEmails && $scope.submitted);
        };
        $scope.ngDirtyErrorRequired = function(form, elementName) {
            return (form[elementName].$dirty
            && form.$error.required && $scope.submitted);
        };
        $scope.ngErrorRequired = function(form, elementName) {
            return (form[elementName].$error.required && $scope.submitted);
        };

        jQuery('#modalEdit').on('hidden.bs.modal', function() { $timeout(function() { $scope.start_edit = false; }, 500); });
        jQuery('#modalEdit').on('show.bs.modal', function() { $scope.start_edit = true; });
    });