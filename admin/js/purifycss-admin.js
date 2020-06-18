jQuery(document).ready(function($){
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 */

	init();


	/**
	 * function of initialize settings window
	 */
	function init(){

		/**
		 * bind event click on buttons
		 */
		$('#live_button').off('click').on('click', livebutton_click );
		

	}

	/**
	 * Live button click to enable live mode
	 * @param {event} ev 
	 */
	function livebutton_click(ev){
		sendAjax( { action:'purifycss_livemode' }, (data)=>{
			// get livemode status
			if ( typeof(data.livemode)!=='undefined'  ){
				if ( data.livemode==1 ){
					$('#live_button').addClass('active');
				}else{
					$('#live_button').removeClass('active');
				}
			}
		} );
	}

	/**
	 * Send ajax request via jQuery
	 * @param {*} url 
	 * @param {*} param 
	 * @param {*} callback 
	 * @param {*} errorMsg 
	 */
	function sendAjax(param, callback=function(){}, errorMsg){
		// block all buttons while ajax request sending
		$('.purifycss-body .button').addClass('disabled');

		$.ajax({
			url: ajaxurl,
			method: "POST",
			data: param,
		}).done( (data)=>{
			if ( data.status == 'OK' ){
				callback(data);
				// show notice
				if ( typeof(data.msg)!=='undefined' ){
					$('.notice').remove();
					$('#wpbody-content').prepend('<div class="notice notice-success is-dismissible"><p>'+data.msg+'</p></div>');
				}
			}else{
				// show error
				if ( typeof(data.msg)!=='undefined' ){
					$('.notice').remove();
					$('#wpbody-content').prepend('<div class="notice notice-error is-dismissible"><p>'+data.msg+'</p></div>');
				}
			}
		} )
		.fail( ()=>{
			console.log(errorMsg);
			$('.notice').remove();
			$('#wpbody-content').prepend('<div class="notice notice-error is-dismissible"><p>'+errorMsg+'</p></div>');
		} )
		.always( ()=>{
			// enable all buttons when ajax request ending
			$('.purifycss-body .button').removeClass('disabled');
		} );
	}

});
