<?php
/**
 *
 */
?>

<div id="misc" class="clearfix">

    <?php include_once 'left-sidebar.php'; global $user; if($user->language == 'en') $lang = "en_us"; else $lang = $user->language; ?>

    <div class="content-side">
		<input type="hidden" id="meta-title" value="<?php print t("Website", array(), array('langcode'=>$lang))." | ". t("Website Development", array(), array('langcode'=>$lang))." - ". t("Languages", array(), array('langcode'=>$lang));?>">
		
        <h6 class="mrgt15"><?php print t("Languages", array(), array('context'=>'hls;system:1;module:4;section:15', 'langcode'=>$lang));?></h6>
        <form action="{{settings.data.languages_form['#action']}}"
            method="post" name="languages_form"
            id="{{settings.data.languages_form.form_id['#id']}}" >

            <table width="100%" class="mrgb20 tbl-border">
                <thead>
                    <tr>
                        <th width="50%" ng-click="sort('name', sort_type)">
                            <?php print t('Language', array(), array('context'=>'hls;system:1;module:4;section:15', 'langcode'=>$lang)); ?>
                            <span class="icon-menu"></span>
                        </th>
                        <th width="25%">
                            <?php print t('Active', array(), array('context'=>'hls;system:1;module:4;section:15', 'langcode'=>$lang)); ?>
                            <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                tooltip-help="languages_active"></span>
                        </th>
                        <th width="25%">
                            <?php print t('Live', array(), array('context'=>'hls;system:1;module:4;section:15', 'langcode'=>$lang)); ?>
                            <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                tooltip-help="languages_live"></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="item in lang_list">
                        <td>{{item.name}}</td>
                        <td>
                            <input type="checkbox"
                                name="hotel_lang[{{item.language}}]"
                                ng-model="item.is_active" value="0"
                                ng-true-value="1" ng-false-value="0"
                                ng-disabled="item.language == 'en'" />
                        </td>
                        <td>
                            <input type="checkbox"
                                name="hotel_lang[{{item.language}}]"
                                ng-model="item.is_live" value="1"
                                ng-true-value="1" ng-false-value="0"
                                ng-click="makeLanguageLive($event, item.allow_live, item.name)"
                                ng-disabled="item.language == 'en'" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <input type="hidden" name="hotel_lang[en]" value="{{lang_en_status}}"/>

            <input type="hidden" id="save-status" value="false"/>
            <input type="hidden" name="form_build_id"
                value="{{settings.data.languages_form.form_build_id['#value']}}" />
            <input type="hidden" name="form_token"
                value="{{settings.data.languages_form.form_token['#value']}}" />
            <input type="hidden" name="form_id"
                value="{{settings.data.languages_form.form_id['#value']}}" />
        </form>

        <a class="button small" href="#"
            ng-loading-button="saving"
            completed-message="<?php print t('Saved!', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>"
            ng-click="save(settings, settings.data.languages_form)">
            <?php print t("Save", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>
        </a>

    </div>
    <?php include_once 'left-demo.php'; global $user; if($user->language == 'en') $lang = "en_us"; else $lang = $user->language; ?>
</div>

<!-- Missing content alert -->
<div id="missing_content_alert" class="reveal-modal tiny" data-reveal>
    <div class="title row">
        <div class="columns large-12">
            <label><?php print t('Missing Content', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></label>
        </div>
    </div>
    <div class="content row">
        <div class="columns large-12" id="missing_content_alert_message">
            <p><?php print t("The content for the language {language_name} is incomplete. You can only turn a language live once the content is loaded for every required field.", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></p>
            <p><?php print t("Please select {language_name} in the Content Language drop down on the top left, go through each page and complete the content for all of the require fields.", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></p>
        </div>
    </div>

    <a class="small button" href="#" ng-click="closeMissingAlert()"><?php print t('OK', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></a>
    <a class="close-reveal-modal">&#215;</a>
</div>
<!-- Missing content alert -->