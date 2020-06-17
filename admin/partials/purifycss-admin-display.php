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
        <button class="button button-primary active" id="live_button">Enable Live Mode</button> 
    </p>

    <p>
        <button class="button button-primary" id="test_button">Enable Test Mode</button>
    </p>

    <div class="manage-menus">
        <p> PurifyCSS API license key: <a href="#">Get licence key</a> </p>
        <p> Purify HTML Code</p>
        <p>
            <button class="button button-primary " id="css_button">Get clean CSS code</button> 
        </p>
        <p> Result: </p>
        <p> Clean CSS code:</p>
        <textarea class=" " name="" id="" cols="30" rows="10"></textarea>
    </div>

    <p>
        <button class="button button-primary" id="save_button">Save settings</button>
    </p>

</div>