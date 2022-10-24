(function( $ ) {
	'use strict';

	$('#frm-contact').submit( function(e) {
		e.preventDefault();

		$.ajax({
			url : dcms_form.ajaxUrl,
			type: 'post',
			dataType: 'json',
			data: {
				action : 'dcms_ajax_frm_contact',
				nonce : dcms_form.frmNonce,
				name: $('#name').val(),
				email: $('#email').val(),
				message: $('#message').val()
			},
			beforeSend: function(){
				$('.frm-message').show().removeClass(['error','success']).text('Enviando...');
				$('#submit').prop('disabled', true);
			}})
			.done( function(res) {
				const noticeClass = res.status === 1 ? 'success' : 'error';
				$('.frm-message').removeClass([['error','success']]).addClass(noticeClass).text(res.message);
			})
			.always(function(){
				$('#submit').prop('disabled', false);
				$('#frm-contact')[0].reset();
			})
	});

})( jQuery );
