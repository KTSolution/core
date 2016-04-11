//jQuery(document).foundation();

function setLocationPathOnLogin() {
	var redirect_path = '';	
	if ( location.hash ) {
		redirect_path = location.hash;
	}
	else {
		redirect_path = location.pathname + location.search;
		if ( location.pathname == '/user/logout' ) {
			redirect_path = '/admin/';
		}
		redirect_path = redirect_path == '/admin/' ? '#!/dashboard' : redirect_path;
		if ( redirect_path[0] == '/admin/' ) {
			//console.log('slide');
			redirect_path = redirect_path.slice(1);
		}
	}

	//redirect_path = redirect_path.replace('/?destination=', '');
	jQuery('#user-login').attr('action', 'Login@login/');
	jQuery('#user-login #redirect').val(redirect_path);
}

jQuery(document).ready(function() {
	setLocationPathOnLogin();
	
	jQuery(window).on('hashchange', function() {
		setLocationPathOnLogin();
	});

	jQuery('#login-name').focus();
	
	var summit_form = false;
	jQuery('#user-login #btn-log-in').click(function(e) {
		e.preventDefault();

        if ( jQuery.cookie('user_expired') == null ) {

    		//window.location.reload();
    		//return;
    	}
		summit_form = true;

		if ( checkLoginInfo(true) ) { console.log("ppp");
			jQuery('#user-login').submit();
		} 
	});
	
	jQuery('#user-login input#login-name, #user-login input#login-pass, .captcha .form-item-captcha-response input').keydown(function(e) {
		jQuery('#user-login label.error').addClass('hide');
		jQuery('#user-login input.alert-border').removeClass('alert-border');
		
		//alert(e.keyCode);
		if ( summit_form ) {
			checkLoginInfo();
		}
		
		if (e.keyCode == 13) {
			jQuery('#user-login #btn-log-in').trigger('click');
		}
	}).blur(function() {
		if ( summit_form ) {
			checkLoginInfo();
		}
	});

	//Forgot pass
	jQuery('.forgot-pass').click(function() {
		jQuery('#forgot-pass-div').removeClass('hide');
	});
	
	jQuery('#btn-send-details').click(function(e) {
		e.preventDefault();
		jQuery('#user-pass').submit();
	});
	
	var $email = jQuery('#user-pass #edit-name');
	var $error_msg = jQuery('#user-pass #user-pass-error-message');
	var email_pass_submit = false;
	
	jQuery('#user-pass #edit-name').keydown(function() {
		if(!email_pass_submit) {
			return;
		}
		
		if(!$email.val().trim() || !validateEmail($email.val().trim())) {
			$error_msg.removeClass('hide');
			$email.addClass('alert-border');			
		} else {
			$error_msg.addClass('hide');
			$email.removeClass('alert-border');
		}		
	});
	
	jQuery('#user-pass').submit(function(e) {
		//Do something here
		e.preventDefault();		
		email_pass_submit = true;
				
		var $msg = jQuery('#user-pass-message');
		$msg.addClass('hide');		
		$email.focus();
		if(!$email.val().trim() || !validateEmail($email.val().trim())) {
			$error_msg.removeClass('hide');
			$email.addClass('alert-border');
			return;
		}
		$error_msg.addClass('hide');
		$email.removeClass('alert-border');
		
		$loading = jQuery('.user-pass-sending');
		jQuery.ajax({
			type: 'POST',
			url: 'Login@pass',
			dataType: 'json',
			data: jQuery('#user-pass').serialize(),
			beforeSend: function() {
				$email.attr( 'disabled', 'disabled' );
				$loading.removeClass('hide');				
			},
			success: function(response) {
				$loading.addClass('hide');
				$email.removeAttr( 'disabled', 'disabled' );
				
				if(!response) {
					return;
				}
				
				if(response.message) {
					$msg.removeClass('hide').text(response.message);
				}
				
				if(response.status == 1) {
					setTimeout(function() {
						window.location.reload();
					}, 1000);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$loading.addClass('hide');
				$email.removeAttr( 'disabled', 'disabled' );
			}
		});
	});
});

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function checkLoginInfo(force) {
    var $captcha = jQuery('.captcha .form-item-captcha-response input');
	var $name = jQuery('#user-login input#login-name');
	var $pass = jQuery('#user-login input#login-pass');
    var $cmsg = jQuery('#user-login #login-captcha-message');
	var $nmsg = jQuery('#user-login #login-name-message');
	var $pmsg = jQuery('#user-login #login-pass-message');
	var error_name = false;
	var flag = true;

	if ( !$name.val().trim() ) {
		if ( force ) {
			$name.focus();
		}
		$nmsg.removeClass('hide');
		$name.addClass('alert-border');		
		error_name = true;
		flag = false;
	} 
	else {
		$name.removeClass('alert-border');
		$nmsg.addClass('hide');
	}
	
	if ( !$pass.val().trim() ) {
		if ( force && !error_name ) {
			$pass.focus();
		}
        jQuery('#err-pass').hide();
		$pmsg.removeClass('hide');
		$pass.addClass('alert-border');
		flag = false;
		//return false;
	} 
	else {
		$pass.removeClass('alert-border');
		$pmsg.addClass('hide');
	}
	
	/*if ( force ) {
		$nmsg.addClass('hide');
		$pmsg.addClass('hide');
	}*/
	
	return flag;
}