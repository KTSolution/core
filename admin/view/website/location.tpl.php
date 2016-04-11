<?php
/**
 *
 */
?>

<div id="website" class="clearfix">

    <?php include_once 'left-sidebar.php'; global $user; if($user->language == 'en') $lang = "en_us"; else $lang = $user->language; ?>

    <div class="content-side">

        <h6 class="mrgt15"><?php print t("Location", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></h6>
		<input type="hidden" id="meta-title" value="<?php print t("Website", array(), array('langcode'=>$lang))." | ". t("Website Development", array(), array('langcode'=>$lang))." - ". t("Location", array(), array('langcode'=>$lang));?>">
        <form action="{{settings.data.location_form['#action']}}" method="post"
            name="location_form" id="{{settings.data.location_form.form_id['#id']}}" >

            <div class="row">
                <div class="columns large-12">
                    <div>
                        <label>
                            <?php print t("Intro", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?>
                            <span class="has-tip tip-right icon icon-info2"
                                data-options="disable_for_touch:true" tooltip-help="location_intro"></span>
                        </label>

                        <div class="large-4">
                            <input type="text" class="" placeholder="<?php print t('Section Title', array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang)); ?>"
                                id="page_title" name="page_title"
                                ng-model="settings.data.location_form.hotel_page['#value'].page_title"
                                required
                                ng-class="{'alert-border': ngErrorRequired('page_title')}" />

                            <label for="page_title" class="error"
                                ng-show="ngErrorRequired('page_title')">
                                <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
                            </label>
                        </div>

                        <div class="large-6">
                            <div class="tinymce-border"
                                ng-class="{'alert-border': ngErrorRequired('page_desc')}">
                                <textarea placeholder="" tinymce="tiny_options"
                                    name="page_desc" id="page_desc"
                                    ng-model="settings.data.location_form.hotel_page['#value'].page_desc"
                                    required></textarea>
                            </div>

                            <label for="page_desc" class="error"
                                ng-show="ngErrorRequired('page_desc')">
                                <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
                            </label>
                        </div>
                    </div>

                    <div class="mrgb20">
                        <label>
                            <?php print t("Map Type", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?>
                            <span class="has-tip tip-right icon icon-info2"
                                data-options="disable_for_touch:true" tooltip-help="location_map_type"></span>
                        </label>

                        <div class="large-6 clearfix mrgt-5">
                            <input type="radio" id="static_map" value="1" name="map_type"
                                ng-model="settings.data.location_form.hotel_inf['#value'].map_type"
                                ng-true-value="1" class="mrgb5 rd-ck-inline left" />
                            <label for="static_map" class="inline left font11 mrgr10 mrgb-2"><?php print t("Static", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></label>

                            <input type="radio" id="google_map" value="2" name="map_type"
                                ng-model="settings.data.location_form.hotel_inf['#value'].map_type"
                                ng-true-value="2" class="mrgb5 rd-ck-inline left"/>
                            <label for="google_map" class="inline left font11 mrgr10 mrgb-2"><?php print t("Google Map", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></label>
                        </div>

                        <div class="row"
                            ng-show="settings.data.location_form.hotel_inf['#value'].map_type == 1">
                            <div class="columns large-4">
                                <div class="row collapse">
                                    <div class="columns large-8">
                                        <input name="files[map_file]" type="file" id="uxMapStatic"
                                            fileupload="" class="mrgb0"/>

                                        <input type="text" placeholder="No file selected"
                                            id="uxMapStaticText"/>
                                    </div>

                                    <div class="columns large-4">
                                        <a href="#" class="button postfix mrgb0" id="uxMapStaticButton">
                                            <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="left">
                                <label class="inline mrgb0">
                                    <small>{{smap_settings.width}}x{{smap_settings.height}} px | {{smap_settings.size}} kb</small>
                                </label>
                            </div>
                        </div>

                        <div id="Googlemap" class="large-6"
                            ng-show="settings.data.location_form.hotel_inf['#value'].map_type != 1">
                            <div id="gmap" style="width: 100%; height: 322px"></div>
                        </div>

                        <div class="large-6 static-map"
                            ng-show="settings.data.location_form.file_manage['#value'] != ''
                                && settings.data.location_form.hotel_inf['#value'].map_type == 1">
                            <img src="{{settings.data.location_form.file_manage['#value']}}" />

                            <div class="delete-map">
                                <span class="button info tiny right"
                                    ng-click="deleteStaticMap()" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</span>
                            </div>
                        </div>
                    </div>

                    <div class="large-6 mrgb2"
                        ng-show="settings.data.location_form.hotel_inf['#value'].map_type != 1">
                        <table width="100%" class="add-bg">
                            <thead>
                                <tr >
                                    <th width="45%" class="text-left"><?php print t("Mark Type", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></th>
                                    <th width="35%" class="text-left"><?php print t("Label", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></th>
                                    <th width="10%"><?php print t("Lat/Long", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></th>
                                    <th width="10%"><?php print t("Delete", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody id="markerList">

                                <tr ng-repeat="(index, obj) in markerData.list"
                                    id="marker-{{index}}" class="marker" ng-if="obj.mIndex >= 0">

                                    <td valign="top">
                                        <select class="marker_type"
                                            ng-model="obj.mType"
                                            ng-init="obj.mType = obj.mType + ''"
                                            ng-options="id as name for (id, name) in markerData.typeList">
                                        </select>
                                    </td>

                                    <td valign="top">
                                        <input type="text" class="maker_title"
                                            ng-model="obj.mTitle"
                                            id="{{'maker_title_' + index}}"
                                            ng-name="'maker_title[' + index + ']'"
                                            ng-class="{'alert-border': ngErrorRequired('maker_title[' + index + ']')}"
                                            ng-required="settings.data.location_form.hotel_inf['#value'].map_type != 1"/>

                                        <label for="{{'maker_title_' + index}}" class="error"
                                            ng-show="ngErrorRequired('maker_title[' + index + ']')">
                                            <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
                                        </label>
                                    </td>

                                    <td align="center" class="marker_edit">
                                        <a href="#">
                                            <span class="icon-dark-gray icon-location font-size-sml"></span>
                                        </a>
                                    </td>

                                    <td align="center" class="marker_delete">
                                        <a href="#" data-reveal-id="file-delete" ng-if="index > 0">
                                            <span class="icon-gray icon-trash font-size-sml"></span>
                                        </a>
                                    </td>

                                    <td>
                                        <div id="delete-mark-type-{{index}}" class="reveal-modal tiny" data-reveal>
                                            <div class="title row">
                                                <div class="columns large-12"><?php print t("Delete Item", array(), array('context'=>'hls;system:1;module:4;section:6', 'langcode'=>$lang));?></div>
                                            </div>

                                            <div class="content row">
                                                <div class="columns large-12">
                                                    <?php print t("Are you sure you want to delete <span>{-hotel_name-}</span>?", array(), array('context'=>'hls;system:1;module:4;section:6', 'langcode'=>$lang));?>
                                                </div>
                                            </div>

                                            <a class="small button left button-grey" id="cancelDelete" href="#"><?php print t("No", array(), array('context'=>'hls;system:1;module:4;section:6', 'langcode'=>$lang));?></a>

                                            <a class="small button mrgl10" id="actionDelete" href="#"><?php print t("Yes", array(), array('context'=>'hls;system:1;module:4;section:6', 'langcode'=>$lang));?></a>

                                            <a class="close-reveal-modal" ng-click="">&#215;</a>
                                        </div>
                                        <div id="edit-lat-long-{{index}}" class="reveal-modal tiny" data-reveal>
                                           <form  method="post" name="edit-map" id="edit-map">
                                                <div class="title row">
                                                    <div class="columns large-12">
                                                        <label>
                                                            <?php print t("Edit Latitude and Longitude", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang)); ?>
                                                            <span class="has-tip tip-right icon icon-info2"
                                                                data-options="disable_for_touch:true"
                                                                tooltip-help="location_lat_lon_info"></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="content clearfix">
                                                    <div class="columns large-12">
                                                        <div class="row">
                                                            <label class="left inline"><?php print t("X", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang)); ?></label>
                                                            <div class="large-4 left mrgl5 mrgr4">
                                                                <input type="text" class="marker_lat"
                                                                    value="{{obj.mLat}}" />
                                                            </div>

                                                            <label class="left inline mrgl20"><?php print t("Y", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang)); ?></label>
                                                            <div class="large-4 left mrgl5 mrgr4">
                                                                <input type="text" class="marker_lng"
                                                                    value="{{obj.mLng}}" />
                                                            </div>                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="MarkListSave"  id="MarkListSave" value="" />
                                                <input type="hidden" name="map_zoom_save" id="map_zoom_save" value="16" />
                                                <a class="small button marker_save" href="#"
                                                ng-loading-button="saving"
                                                >
                                                    <?php print t("Save", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>
                                                </a>

                                                <a class="close-reveal-modal">&#215;</a>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="5" class="last-bg_gray" ng-click="addNewMarkerList()">
                                        <label class="clickable">
                                            <small><span class="icon-plus icon-gray"></span></small>
                                            <small class="txt-dark-gray"><?php print t("add new mark", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></small>
                                        </label>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <label>
                            <?php print t("Map Layout", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?>
                            <span class="has-tip tip-right icon icon-info2"
                                data-options="disable_for_touch:true" tooltip-help="location_map_layout"></span>
                        </label>

                        <div class="large-6 clearfix mrgt-5">
                            <input type="radio" id="map_layout" class="map_layout_check rd-ck-inline left"
                                value="<?php echo HLS_MAP_LAYOUT_MAP; ?>"
                                name="map_layout"
                                ng-model="map_layout"
                                ng-click="map_layout = <?php echo HLS_MAP_LAYOUT_MAP; ?>; initMap()" />
                            <label for="map_layout" class="font11 inline left mrgr10 mrgb-2"><?php print t("Map", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang)); ?></label>

                            <input type="radio" id="satellite_layout" class="map_layout_check rd-ck-inline left"
                                value="<?php echo HLS_MAP_LAYOUT_SATELLITE; ?>"
                                name="map_layout"
                                ng-model="map_layout"
                                ng-click="map_layout = <?php echo HLS_MAP_LAYOUT_SATELLITE; ?>; initMap()" />
                            <label for="satellite_layout" class="font11 inline left mrgr10 mrgb-2"><?php print t("Satellite", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang)); ?></label>

                            <input type="radio" id="terrian_layout" class="map_layout_check rd-ck-inline left"
                                value="<?php echo HLS_MAP_LAYOUT_TERRIAN; ?>"
                                name="map_layout"
                                ng-model="map_layout"
                                ng-click="map_layout = <?php echo HLS_MAP_LAYOUT_TERRIAN; ?>; initMap()" />
                            <label for="terrian_layout" class="font11 inline left mrgb-2"><?php print t("Terrain", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang)); ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="save-status" value="false"/>
            <input type="hidden" name="form_build_id" value="{{settings.data.location_form.form_build_id['#value']}}" />
            <input type="hidden" name="form_token" value="{{settings.data.location_form.form_token['#value']}}" />
            <input type="hidden" name="form_id" value="{{settings.data.location_form.form_id['#value']}}" />
            <input type="hidden" name="Marklist" id="Marklist" value="" />
            <input type="hidden" name="map_zoom" id="map_zoom" value="16" />
            <input type="hidden" name="delete_static_map" value="{{delete_static_map}}" />

            <a class="button small" href="#" ng-loading-button="saving_settings"
            	completed-message="<?php print t('Saved!', array(), array('context'=>'hls;system:1;module:1')); ?>"
                ng-click="save(settings, location_form)"><?php print t("Save", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?></a>
        </form>
    </div>
    <?php include_once 'left-demo.php'; global $user; if($user->language == 'en') $lang = "en_us"; else $lang = $user->language; ?>
</div>

<div id="delete-static-map" class="reveal-modal tiny" data-reveal>
    <div class="title row">
        <div class="columns large-12"><?php print t("Delete Static Map", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></div>
    </div>
    <div class="content row">
        <div class="columns large-12">
            <img src="{{settings.data.location_form.file_manage['#value']}}"
                width="120" height="90" class="mrgl10 mrgb10 right">
            <?php print t("Are you sure you want to delete this map?", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?>
        </div>
    </div>
    <a class="small button left button-grey" href="#"
       ng-click="cancelDeleteImg()" ng-loading-button="deleting_img"><?php print t("No", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></a>
    <a class="small button" href="#" ng-click="deleteImg()"><?php print t("Yes", array(), array('context'=>'hls;system:1;module:4;section:7', 'langcode'=>$lang));?></a>
    <a class="close-reveal-modal" ng-click="">&#215;</a>
</div>
