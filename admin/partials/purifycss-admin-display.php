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
        <button class="button button-primary <?=get_option('purifycss_livemode')=='1'?'active':''?>" id="live_button"><?=__('Enable Live Mode','purifycss')?></button> 
    </p>

    <p>
        <button class="button " id="test_button"><?=__('Enable Test Mode','purifycss')?></button>
    </p>

    <div class="manage-menus">

        <p><?=__('PurifyCSS API license key:','purifycss')?> <a href="#"><?=__('Get licence key','purifycss')?></a> </p>

        <p> 
            <input name="api-key" type="text" id="api-key" value="<?=get_option('purifycss_api_key')?>" autocomplete="off" class="regular-text"> 
            <button class="button button-primary " id="activate_button"><?=__('Activate','purifycss')?></button> 
        </p>
        
        <p class="expand-click"> <span class="dashicons dashicons-arrow-right"></span> <span class="clickable"><?=__('PurifyCSS options','purifycss')?> </span> </p>
        <p class="d-none pl-5 expand-block">
            <?=__('Custom HTML Code:','purifycss')?> <br/>
            <textarea class=" " name="" id="customhtml_text" cols="100" rows="10"></textarea>
        </p>

        <p>
            <button class="button button-primary " id="css_button"><?=__('Get clean CSS code','purifycss')?></button> 
        </p>

        <p><?=__('Result:','purifycss')?> </p>

        <p><?=__('Clean CSS code:','purifycss')?></p>

        <textarea class=" " name="" id="" cols="100" rows="10"></textarea>
    </div>

    <p>
        <button class="button button-primary" id="save_button"><?=__('Save settings','purifycss')?></button>
    </p>

</div>