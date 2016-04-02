'use strict';
// Declare app level module which depends on filters, and services
var app = angular.module('ngAdmin', ['ngRoute', 'ngResource']);

var isObject = angular.isObject,
    isUndefined = angular.isUndefined,
    isDefined = angular.isDefined,
    isFunction = angular.isFunction,
    isString = angular.isString,
    isArray = angular.isArray,
    isNumber = angular.isNumber,
    isDate = angular.isDate,
    isInvalid = 'undefined',
    isEmpty = '',
    forEach = angular.forEach,
    bodyElement = angular.element(document.body),
    injector = angular.injector(['ng']),
    $q = injector.get('$q'),
    $http = injector.get('$http'),
    loadingClass = 'deferred-bootstrap-loading',
    errorClass = 'deferred-bootstrap-error';


function __render( value1, value2 ) {
    if ( value2 != isUndefined ) return { key : value1, value: value2};
    return { key : value1 };
}

// (function (app) {
//     function AppConfig() {
//         throw "Static Class. AppConfig cannot be instantiated.";
//     }
//
//     var self = AppConfig;
//
//     self.testMode = false;
//     self.actionPath = "/admin/";
//     self.templatePath = "view/";
//     self.uploadPath = "../uploads/";
//     app.conf = AppConfig;
//
// }(app = app || {}));


var $uploadOptions = {};
$uploadOptions.default = {
    auto_upload: true,
    upload_path: ktsSetting.ngKTSPath.action + 'file@upload',
    delete_path: ktsSetting.ngKTSPath.action + 'file@delete',
    form_build_id: '',
    form_token: '',
    form_id: ''
};

var $tinymceOptions = {};
$tinymceOptions.default = {
    version: 3,
    theme: "advanced",
    width: "100%",
    height: 400,
    menubar:false,
    statusbar: false,
    plugins: "pagebreak,advhr,advimage,advlink,paste,inlinepopups",
    theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,removeformat,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent",
    theme_advanced_buttons2: "pastetext,link,unlink,anchor,|,sub,sup,|,image,pagebreak,|,code,|,formatselect,|,youtube",
    theme_advanced_blockformats: "p,h1,h2,h3,h4,h5,h6",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "left",
    theme_advanced_resizing: true,
    theme_advanced_resize_horizontal: false,
    theme_advanced_path: false,
    force_br_newlines: false,
    force_p_newlines: true,
    forced_root_block: "p",
    theme_advanced_fonts: ktsSetting.ngKTSPath.tinyMCEFonts,
    setup: function(ed) {
        ed.onChange.add(function(ed, l) {
            jQuery("#" + ed.editorId).val(l.content);
        });
    }
};
$tinymceOptions.small = {
    version: 3,
    theme: "advanced",
    width: "100%",
    height: 250,
    menubar:false,
    statusbar: false,
    plugins: "pagebreak,advhr,advimage,advlink,paste,inlinepopups",
    theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,removeformat,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent",
    theme_advanced_buttons2: "pastetext,link,unlink,anchor,|,sub,sup,|,image,pagebreak,|,code,|,formatselect,|,youtube",
    theme_advanced_blockformats: "p,h1,h2,h3,h4,h5,h6",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "left",
    theme_advanced_resizing: true,
    theme_advanced_resize_horizontal: false,
    theme_advanced_path: false,
    force_br_newlines: false,
    force_p_newlines: true,
    forced_root_block: "p",
    theme_advanced_fonts: ktsSetting.ngKTSPath.tinyMCEFonts,
    setup: function(ed) {
        ed.onChange.add(function(ed, l) {
            jQuery("#" + ed.editorId).val(l.content);
        });
    }
};
$tinymceOptions.tiny = {
    version: 3,
    theme: "advanced",
    width: "100%",
    height: 180,
    menubar:false,
    statusbar: false,
    plugins: "pagebreak,advhr,advimage,advlink,paste,inlinepopups",
    theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,removeformat,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent",
    theme_advanced_buttons2: "pastetext,link,unlink,anchor,|,sub,sup,|,image,pagebreak,|,code,|,formatselect,|,youtube",
    theme_advanced_blockformats: "p,h1,h2,h3,h4,h5,h6",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "left",
    theme_advanced_resizing: true,
    theme_advanced_resize_horizontal: false,
    theme_advanced_path: false,
    force_br_newlines: false,
    force_p_newlines: true,
    forced_root_block: "p",
    theme_advanced_fonts: ktsSetting.ngKTSPath.tinyMCEFonts,
    setup: function(ed) {
        ed.onChange.add(function(ed, l) {
            jQuery("#" + ed.editorId).val(l.content);
        });
    }
};

app.config(['$locationProvider', function ($locationProvider) {
    $locationProvider.html5Mode(false).hashPrefix('!');
}]);


var controllers = [];
controllers.push({name: 'DashboardController', template: 'dashboard.html', url: '/dashboard', title: 'Dashboard'});
controllers.push({
    name: 'PageController',
    template: 'website/page.html',
    url: '/website/page',
    title: 'Page Management'
});
controllers.push({
    name: 'LanguageController',
    template: 'website/language.html',
    url: '/website/language',
    title: 'Language Management'
});
controllers.push({
    name: 'SettingController',
    template: 'website/setting.html',
    url: '/website/setting',
    title: 'Website Development'
});
controllers.push({
    name: 'MemberController',
    template: 'misc/member/list.html',
    url: '/misc/member',
    title: 'User Management'
});

app.config(['$routeProvider', function ($routeProvider) {
    var ktsPath =  ktsSetting.ngKTSPath;
    for ( var i in controllers ) {
        var c = controllers[i];
        c.path = c.url;
        c.template = ktsPath.template + c.template || ktsPath.template + c.url.replace("/", "_") + '.html';
        c.controller = c.name;
        c.title = c.title || "Administrator | Website Management";
        $routeProvider.when(c.path, {templateUrl: c.template, controller: c.controller, title: c.title});
    }
    $routeProvider.otherwise({redirectTo: '/dashboard'});
}]);

app.run(function ($rootScope, $location) {
    //jQuery(document).foundation();
    var ktsPath = ktsSetting.ngKTSPath;

    $rootScope.$on('$locationChangeStart', function (event) {
        //Prevent run app
        if ($location.path() == '/' || $location.path() == '') {
            event.preventDefault();
        }

        var cpath = $location.path();
        if (cpath[cpath.length - 1] == '/') {
            cpath = cpath.slice(0, -1);
        }
        //console.log(cpath);

        //Redirect special links
        //if ( typeof mappedUrl[cpath] !== 'undefined' ) {
        //    $location.path(mappedUrl[cpath].replace('{hotel_id}', 1));
        //    return;
        //}

    });


    $rootScope.$on('$locationChangeSuccess', function (event) {
        jQuery('#page_current_url').val($location.path());
    });

    $rootScope.$on("$routeChangeSuccess", function (event, currentRoute, previousRoute) {
        var pageTitle = '';
        //Change page title, based on Route information
        if (currentRoute && currentRoute.title) {
            pageTitle = currentRoute.title;
        }
        $rootScope.pageTitle = pageTitle;
    });

    $rootScope.$on('$viewContentLoaded', function ($scope) {
        if ($rootScope.pageTitle != '')
            $rootScope.pageTitle = $rootScope.pageTitle + ' | Hotel Link Solutions';
        else {
            $rootScope.pageTitle = jQuery("#meta-title").val() + ' | Hotel Link Solutions';
        }

        var cpath = $location.path();
    });

    $rootScope.dataHasChanged = function () {
        jQuery('#data_not_save').val(1);
        //console.log('aaaa');
    };

    $rootScope.dataHasSaved = function () {
        jQuery('#data_not_save').val(0);
    };


    $rootScope.hotel_list_change = false;
    //$cope.$watch('hotel_list_change') //reload hotel list

    $rootScope.changeNotSavePage = function ($route) {
        jQuery('#data_not_save').val(0);
        jQuery('#page_change_url_abc').val(0);
        jQuery('#data_not_save_alert').foundation('reveal', 'close');
        jQuery('.reveal-modal-bg').hide();

        if (jQuery('#data_key_press').val() == '1') {
            jQuery('#data_key_press').val(0);
            window.location.reload();
        }

        if (jQuery('#data_change_language').val() == '1') {
            jQuery('#data_change_language').val(0);
            if (jQuery('#select_language').length) {
                jQuery('#select_language').trigger('change');
                return;
            }
        }

        $rootScope.$apply(function () {
            //var url = jQuery('#page_change_url').val();
            //var cur_url = window.location.hash.replace('#!/', '/');
            //var redirect_url = url;
            //if ( typeof mappedUrl[url] !== 'undefined' ) {
            //    redirect_url = mappedUrl[url].replace('{hotel_id}', 111);
            //}
            //
            //$location.path(redirect_url);
            //if ( url == cur_url ) {
            //    $route.reload();
            //}
        });
    };
});

app.controller('MainController',
    function ($rootScope, $scope, $route, $location, $http, $sce, $timeout, $compile) {
        $rootScope.$on('$locationChangeSuccess', function (event) {
            if (!$route.current) {
                console.log('success: ' + $location.path().slice(0, -1));
                var cpath = $location.path();
                if (cpath[cpath.length - 1] == '/') {
                    cpath = cpath.slice(0, -1);
                }
                //Redirect special links
                //if ( typeof mappedUrl[cpath] !== 'undefined' ) {
                //    $location.path(mappedUrl[cpath].replace('{hotel_id}', 111));
                //    return;
                //}

                return;
            }

            if (!$rootScope.has_load_data) {

                jQuery('#hotel_loading').show();

                $timeout(function () {
                    TopMenu.initMenu();
                    changeActiveMenuItem();
                });

                //$rootScope.has_load_data = true;
            }

        });
    });

app.factory('apiService', function($http, $resource, $q) {
    var ktsPath = ktsSetting.ngKTSPath;
    return {
        list: function(url, params) {
            var query = ( params != isUndefined || params != "") ? '?'+jQuery.param(params) : "";
            var path = ktsPath.action + url + query;
            var resource = $resource(path);
            var deferred = $q.defer();
            resource.get(
                {},
                function(event) {
                    deferred.resolve(event);
                },
                function(response) {
                    deferred.reject(response);
                }
            );
            var promise = deferred.promise;
            return promise;
        },
        get: function( url, pid ) {
            var url = ktsPath.action + url;
            var resource = $resource(url, {pid: '@id'});
            var deferred = $q.defer();
            resource.get(
                { pid: pid },
                function( event ) {
                    deferred.resolve(event);
                },
                function( response ) {
                    deferred.reject( response );
                }
            );
            var promise = deferred.promise;
            return promise;
        },
        save: function( url, form ) {
            var path = ktsPath.action + url;
            var form = jQuery("#" + form.$name).serialize();
            var promise= $http.post( path, form, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}} )
                .then( function (response ) {
                    return response.data;
                });
            return promise;
        },
        delete: function( url, pid ){
            var url = ktsPath.action + url;
            var query = 'pid=' + pid;
            var promise = $http.post(url, query, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}})
                .then(function (response) {
                    return response.data;
                });
            return promise;
        }
    }
});