"use strict";

app.controller('WebsiteSearchController',
    function($scope, $rootScope, $route, apiService, $timeout, $location, $compile, $sce, $interval) {
        console.log('search controller');

        $scope.fetched = false;
        $scope.saving = false;
        $scope.searching = false;
        $scope.reset = false;
        $scope.start_edit = false;

        var link = {
            list: '/website/website-search',
            edit: '/website/website-search@edit',
            save: '/website/website-search@save',
            save_map: '/website/website-search@location_save_map',
            delete: '/website/website-search@delete'
        };

        // init Tinymce
        $scope.tiny_options = $tinymceOptions.normal;

        $scope.init = function (data) {
            $scope.link     = link;
            if ( typeof data != isInvalid ) {
                $scope.sidebar = data.sidebar || {};
                $scope.list = data.list || {};
                $scope.global = data.global || {};
                $scope.setup = data.setup || {};
                $scope.setting = data.setting || {};
                $scope.setting.file_manage ="";
            }
        };

        $scope.autoUrlChange = function() {
            $scope.setup.wsUrl = jQuery('#wsUrl').val();
        };
        $scope.keyupCountDown = function(elm, print, count) {
            var limit = count - jQuery('#' + elm).val().length;
            jQuery('#' + print).html(limit);
            if ( limit >= 0 ) {
                jQuery('#' + print).removeClass('red');
            }
            else {
                jQuery('#' + print).addClass('red');
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
                $scope.obj = {};
                if ( typeof data != isInvalid ) {

                }
            });
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
                        if ( response != isNull) {
                            $route.reload();
                        }
                    }, 1000);
                });
            }else{
                var $elm = jQuery('input.ng-invalid:first');
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

        $scope.map_type = 2;
        // $scope.map_layout = $scope.setting.markerData.mapLayout;
        $scope.zoom_level = -1;
        $scope.map_layout = 1;
        // $scope.markerData = $scope.setting.markerData;
        // $scope.markerLength = $scope.markerData;
        $scope.gmap = null;
        $scope.markerIcons = [];
        $scope.initMap = function () {
            if( typeof $scope.setting.map_layout == isInvalid ) {
                $scope.setting.map_layout = $scope.setting.markerData.mapLayout;
            }
            if (jQuery('#save-status').val() == 'false') {
                var latlng = new google.maps.LatLng(
                    $scope.setting.markerData.list[0].mLat,
                    $scope.setting.markerData.list[0].mLng
                );

                if ($scope.zoom_level == -1) {
                    $scope.zoom_level = $scope.setting.markerData.zoomLevel;
                } else {
                    $scope.zoom_level = $scope.gmap.getZoom();
                }

                var map_layout = null;
                if ($scope.setting.map_layout == 2) {
                    map_layout = google.maps.MapTypeId.SATELLITE;
                } else if ($scope.setting.map_layout == 3) {
                    map_layout = google.maps.MapTypeId.TERRAIN;
                } else {
                    map_layout = google.maps.MapTypeId.ROADMAP;
                }


                var myOptions = {
                    zoom: $scope.zoom_level,
                    center: latlng,
                    disableDefaultUI: true,
                    panControl: true,
                    scaleControl: true,
                    zoomControl: true,
                    mapTypeControl: true,
                    mapTypeId: map_layout
                };

                $scope.gmap = new google.maps.Map(document.getElementById('gmap'), myOptions);

                $interval(function () {
                    var center = $scope.gmap.getCenter();
                    google.maps.event.trigger($scope.gmap, 'resize');
                    $scope.gmap.setCenter(center);
                }, 3000);

                $scope.initMarkerIcons();
                $scope.addMarkers($scope.setting.markerData.list);
            }
        };
        $scope.initMarkerIcons = function () {
            var arrIconName = ['hotel_higlight', 'hotel', 'landmark', 'shopping', 'restaurant'];
            for (var i = 0; i < arrIconName.length; i++) {
                var image = new google.maps.MarkerImage('http://new-hls-manage.whl-staging.com/'
                    + '/sites/all/themes/public/img/gmarker/' + arrIconName[i] + '.png',
                    null, null, null,
                    new google.maps.Size(32, 32));

                $scope.markerIcons[i] = image;
            }
        };
        $scope.addMarkers = function (markerList) {
            jQuery.each(markerList, function(i, obj) {
                var options = {
                    _index: i,
                    lat: obj.mLat,
                    lng: obj.mLng,
                    title: obj.mTitle,
                    type: obj.mType
                };

                if (i == 0) { // Default hotel marker
                    $timeout(function() {
                        $scope.addMarkerEventListener($scope.createMarker(options));
                    }, 5000);
                }
                else {
                    $scope.addNewMarker(options);
                }
            });
        };
        $scope.addNewMarker = function (options) {
            var _index,
                title = '',
                lat   = '',
                lng   = '',
                type  = 1,
                newMarker = null;

            if ( options != null ) {
                _index = options._index;
                title = options.title;
                lat   = options.lat;
                lng   = options.lng;
                type  = options.type;
            }
            else {
                _index = $scope.setting.markerData.list.length;
                lat = $scope.gmap.getCenter().lat();
                lng = $scope.gmap.getCenter().lng();
                // Push new marker to data list
                $scope.setting.markerData.list.push({
                    mSaved: false,
                    mIndex: _index,
                    mTitle: title,
                    mTitleOrg: '',
                    mLat  : lat,
                    mLng  : lng,
                    mType : type
                });
            }

            // Add marker to gmap
            newMarker = $scope.createMarker({
                '_index': _index,
                'lat'   : lat,
                'lng'   : lng,
                'title' : title,
                'type'  : type
            });

            $timeout(function () {
                $scope.addMarkerEventListener(newMarker);
            }, 500);
        };
        $scope.addNewMarkerList = function() {
            $scope.addNewMarker(null);

            $scope.dataHasChanged();
        };
        $scope.createMarker = function (options) {
            var latlng = new google.maps.LatLng(options.lat, options.lng);
            var marker = new google.maps.Marker({
                position: latlng,
                map: $scope.gmap,
                title: options.title,
                draggable: true,
                optimized: false,
                icon: $scope.markerIcons[options.type]
            });
            marker._index = options._index;

            return marker;
        };
        $scope.addMarkerEventListener = function (marker) {
            var markerTrEl  = jQuery('#markerList #marker-' + marker._index),
                markerTypeEl  = markerTrEl.find('.marker_type'),
                markerTitleEl = markerTrEl.find('.maker_title'),
                markerLatEl = markerTrEl.find('.marker_lat'),
                markerLngEl = markerTrEl.find('.marker_lng'),
                markerObj   = $scope.setting.markerData.list[marker._index],
                ctrKey = false;

            // Marker type change event
            markerTypeEl.unbind('change').change(function(){
                marker.setIcon($scope.markerIcons[parseInt(jQuery(this).val())]);
                markerObj.mType  = markerTypeEl.val();
            });

            // Marker title change event
            markerTitleEl.unbind('change').change(function(){
                marker.setTitle(jQuery(this).val());
                markerObj.mTitle  = jQuery(this).val();
            });

            /* Set value and events for Lat and Lng input */
            markerLatEl.val(marker.getPosition().lat());
            markerLngEl.val(marker.getPosition().lng());

            // Force float number lat, lng input
            markerLatEl.add(markerLngEl).unbind('keypress').keypress(function(event) {
                var code = (event.keyCode ? event.keyCode : event.which);
                if ( String.fromCharCode(code) == 'e' || String.fromCharCode(code) == 'E' ) {
                    return false;
                }

                if ( String.fromCharCode(code) == '-' ) {
                    return true;
                }

                if ( ctrKey && (String.fromCharCode(code) == 'c'
                    || String.fromCharCode(code) == 'C'
                    || String.fromCharCode(code) == 'V'
                    || String.fromCharCode(code) == 'v') ) {

                    return true;
                }

                var value = jQuery(this).val() + String.fromCharCode(code) + '0';
                if ( isNaN(value) && (code != 8 && code != 46) ) {
                    return false;
                }
            });

            markerLatEl.add(markerLngEl).unbind('keydown').keydown(function(event) {
                ctrKey = event.ctrlKey;
            });

            // Lat, lng input change event
            markerLatEl.add(markerLngEl).unbind('change').change(function() {
                markerObj.mLat = (parseFloat(markerLatEl.val())) ? parseFloat(markerLatEl.val()) : 0;
                markerObj.mLng = (parseFloat(markerLngEl.val())) ? parseFloat(markerLngEl.val()) : 0;
                markerLatEl.val((!markerObj.mLat) ? 0 : markerObj.mLat);
                markerLngEl.val((!markerObj.mLng) ? 0 : markerObj.mLng);
                marker.setPosition(new google.maps.LatLng(markerObj.mLat, markerObj.mLng));

                $scope.gmap.panTo(marker.getPosition());
            });

            /* Save and delete marker event */
            markerTrEl.find('.marker_save').unbind('click').click(function() {
                markerObj.mTitle = markerTitleEl.val();
                markerObj.mLat = parseFloat(markerLatEl.val());
                markerObj.mLng = parseFloat(markerLngEl.val());
                //markerObj.mType  = parseInt(markerTypeEl.val(), 10);
                markerObj.mSaved = true;
                var markers_abc = [];
                jQuery.each($scope.setting.markerData.list, function(index, obj) {
                    markers_abc.push(obj);
                });
                var abc = JSON.stringify(markers_abc);
                jQuery('#map_zoom_save').val($scope.gmap.getZoom());
                apiService.save_map($scope.link.save_map, abc, $scope.gmap.getZoom()).then(function (response) {
                    $route.reload();
                });
                jQuery('#edit-lat-long-' + marker._index).modal('toggle');
                removePopup();
            });

            /* Save and delete marker event */
            markerTrEl.find('.marker_edit').unbind('click').click(function() {
                jQuery('#edit-lat-long-' + marker._index).modal('toggle');
            });

            // Delete marker event
            jQuery('#delete-mark-type-' + marker._index + ' #actionDelete').unbind('click').click(function() {
                marker.setMap(null);
                jQuery('#delete-mark-type-' + marker._index).modal('toggle');
                removePopup();
                $scope.setting.markerData.list[marker._index].mIndex = -1;
                marker = null;

                var markers_abc = [];
                jQuery.each($scope.setting.markerData.list, function(index, obj) {
                    markers_abc.push(obj);
                });
                
                var abc = JSON.stringify(markers_abc);
                jQuery('#map_zoom_save').val($scope.gmap.getZoom());
                apiService.save_map($scope.link.save_map, abc, $scope.gmap.getZoom()).then(function (response) {
                    $route.reload();
                });

                $scope.dataHasChanged();
            });
            // Delete marker popup
            markerTrEl.find('.marker_delete').unbind('click').click(function() {
                var name = jQuery('#maker_title_'+marker._index).val();
                jQuery('#delete-mark-type-' + marker._index + ' .container-full span').html(name);
                jQuery('#delete-mark-type-' + marker._index).modal('toggle');
                jQuery('#delete-mark-type-' + marker._index + ' #cancelDelete').unbind('click').click(function(){
                    jQuery('#delete-mark-type-' + marker._index).modal('toggle');
                });
            });

            google.maps.event.addListener(marker, 'drag', function(event) {
                markerLatEl.val(marker.getPosition().lat());
                markerLngEl.val(marker.getPosition().lng());
            });

            google.maps.event.addListener(marker, 'dragend', function(event) {
                markerObj.mLat   = marker.getPosition().lat();
                markerObj.mLng   = marker.getPosition().lng();
                markerObj.mSaved = false;
                $scope.gmap.panTo(marker.getPosition());

                $scope.dataHasChanged();
            });
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