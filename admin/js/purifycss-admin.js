jQuery(document).ready(function($){
	'use strict';

	var purified_css;
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 */

	init();


	/**
	 * function of initialize settings window
	 */
	function init(){
		purified_css = wp.codeEditor.initialize( "purified_css", {"codemirror":{"indentUnit":4,"indentWithTabs":true,"inputStyle":"contenteditable","lineNumbers":true,"lineWrapping":true,"styleActiveLine":true,"continueComments":true,"extraKeys":{"Ctrl-Space":"autocomplete","Ctrl-\/":"toggleComment","Cmd-\/":"toggleComment","Alt-F":"findPersistent","Ctrl-F":"findPersistent","Cmd-F":"findPersistent"},"direction":"ltr","gutters":[],"mode":"text\/css","lint":false,"autoCloseBrackets":true,"matchBrackets":true},"csslint":{"errors":true,"box-model":true,"display-property-grouping":true,"duplicate-properties":true,"known-properties":true,"outline-none":true},"jshint":{"boss":true,"curly":true,"eqeqeq":true,"eqnull":true,"es3":true,"expr":true,"immed":true,"noarg":true,"nonbsp":true,"onevar":true,"quotmark":"single","trailing":true,"undef":true,"unused":true,"browser":true,"globals":{"_":false,"Backbone":false,"jQuery":false,"JSON":false,"wp":false}},"htmlhint":{"tagname-lowercase":true,"attr-lowercase":true,"attr-value-double-quotes":false,"doctype-first":false,"tag-pair":true,"spec-char-escape":true,"id-unique":true,"src-not-empty":true,"attr-no-duplication":true,"alt-require":true,"space-tab-mixed-disabled":"tab","attr-unsafe-chars":true}} );
				
		/**
		 * bind event click on buttons
		 */
		$('#live_button').off('click').on('click', livebutton_click );
		$('#test_button').off('click').on('click', testbutton_click );
		$('#activate_button').off('click').on('click', activatebutton_click );
		$('#css_button').off('click').on('click', cssbutton_click );
		$('#save_button').off('click').on('click', savebutton_click );
		

		$('.expand-click').off('click').on('click', toogletext_click );
		

	}


	/**
	 * SaveCSS button click to send request to get CSS
	 * @param {event} ev 
	 */
	function savebutton_click(ev){
		let customhtml='';
		if ( typeof(customhtml_text.codemirror)!=='undefined' ){
			customhtml = customhtml_text.codemirror.doc.getValue();
		}else{
			customhtml = $('#customhtml_text').val();
		}
		sendAjax( { action:'purifycss_savecss', customhtml:customhtml }, (data)=>{
			// console.log(data);
			window.location.reload();
		} );
	}

	/**
	 * GetCSS button click to send request to get CSS
	 * @param {event} ev 
	 */
	function cssbutton_click(ev){
		let customhtml='';
		if ( typeof(customhtml_text.codemirror)!=='undefined' ){
			customhtml = customhtml_text.codemirror.doc.getValue();
		}else{
			customhtml = $('#customhtml_text').val();
		}
		sendAjax( { action:'purifycss_getcss', customhtml:customhtml }, (data)=>{
			console.log(data);
			$('.result-block').html("Result: "+data.resmsg).show();
			purified_css.codemirror.doc.setValue(data.styles);
			// enable/disable live mode if code generated succesfully
			if ( data.livemode=='1' ){
				$('#live_button').addClass('active');
			}else{
				$('#live_button').removeClass('active');				
			}
		} );
	}

	/**
	 * Expand/ scrollup block
	 * @param {event} ev 
	 */
	function toogletext_click(ev){
		$('.expand-click').toggleClass('active');
		if ( $('.expand-click').hasClass('active') ){
			$('.expand-block').removeClass('d-none');
			$('.expand-click .dashicons').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');

			if ( !$('#customhtml_text').hasClass('initialized') ){
				// mark textarea as already initialized
				$('#customhtml_text').addClass('initialized');
				
				// initialize code editor
				customhtml_text = wp.codeEditor.initialize( "customhtml_text", customhtml_text_param ); 
			}

		}else{
			$('.expand-block').addClass('d-none');
			$('.expand-click .dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
		}
	} 

	/**
	 * Activate button click to enable live mode
	 * @param {event} ev 
	 */
	function activatebutton_click(ev){
		let keyval='';
		// get api value
		keyval = $('#api-key').val();

		sendAjax( { action:'purifycss_activate', key:keyval }, (data)=>{
			if ( data.status=="OK" ){
				$('.activated-text').removeClass('d-none');
			}
			console.log(data);
		} );
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
	 * Test button click to enable test mode
	 * @param {event} ev 
	 */
	function testbutton_click(ev){
		sendAjax( { action:'purifycss_testmode' }, (data)=>{
			// get testmode status
			if ( typeof(data.testmode)!=='undefined'  ){
				if ( data.testmode==1 ){
					$('#test_button').addClass('active');
				}else{
					$('#test_button').removeClass('active');
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
