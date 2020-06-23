<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/f2re
 * @since      1.0.0
 *
 * @package    Purifycss
 * @subpackage Purifycss/admin/partials
 */
?>

<div class="purifycss-body">
    <h1>PurifyCSS</h1>

    <p>
        <button class="button inspan button-primary <?=get_option('purifycss_livemode')=='1'?'active':''?>" id="live_button">
            <span class="enable"><?=__('Enable Live Mode','purifycss')?></span>
            <span class="disable"><?=__('Disable Live Mode','purifycss')?></span>
        </button> 
    </p>

    <p>
        <button class="button inspan <?=get_option('purifycss_testmode')=='1'?'active':''?>" id="test_button">
            <span class="enable"><?=__('Enable Test Mode','purifycss')?></span>
            <span class="disable"><?=__('Disable Test Mode','purifycss')?></span>
        </button>
    </p>

    <div class="manage-menus">

        <p><?=__('PurifyCSS API license key:','purifycss')?> <a href="https://purifycss.online/license"><?=__('Get licence key','purifycss')?></a> </p>

        <p> 
            <input name="api-key" type="text" id="api-key" value="<?=get_option('purifycss_api_key')?>" autocomplete="off" class="regular-text"> 
            <button class="button button-primary " id="activate_button"><?=__('Activate','purifycss')?></button>
            <span class="activated-text green-text <?=get_option('purifycss_api_key_activated')==true?'':'d-none'?>"><span class="dashicons dashicons-yes"></span> Activated!</span>
        </p>
        
        <p class="expand-click"> <span class="dashicons dashicons-arrow-right"></span> <span class="clickable"><?=__('PurifyCSS options','purifycss')?> </span> </p>
        <div class="d-none pl-5 expand-block">
            <?=__('Custom HTML Code:','purifycss')?> <br/>
            <textarea class="html_editor" name="" id="customhtml_text" cols="100" rows="10" autocomplete="off"><?=get_option('purifycss_customhtml')?></textarea>
        </div>

        <p>
            <button class="button button-primary " id="css_button"><?=__('Get clean CSS code','purifycss')?></button> 
        </p>

        <p class="result-block <?=get_option('purifycss_resultdata')!=''?'':'d-none'?>"><?=__('Result:','purifycss')?> <?=get_option('purifycss_resultdata')?> </p>

        <p><?=__('Clean CSS code:','purifycss')?></p>

        <textarea class="css_editor" name="" id="purified_css" cols="100" rows="10"><?=PurifycssHelper::get_css();?></textarea>
    </div>

    <p>
        <button class="button button-primary" id="save_button"><?=__('Save settings','purifycss')?></button>
    </p>

</div>