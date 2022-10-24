<?php
// ..... codigo previo

// Enqueue javascript file
add_action('wp_enqueue_scripts', 'dcms_insert_custom_js');
function dcms_insert_custom_js(){
	wp_register_script('dcms_script', get_stylesheet_directory_uri(). '/js/script.js', array('jquery'), '1.0.0', true );
	wp_localize_script('dcms_script', 'dcms_form',
		[ 'ajaxUrl'=>admin_url('admin-ajax.php'),
		  'frmNonce' => wp_create_nonce('secret-key-form')
		]);
	wp_enqueue_script('dcms_script');
}

// Create the contact form
add_filter('the_content', 'dcms_show_contact_ajax_form');
function dcms_show_contact_ajax_form( $content ){
	if ( ! is_page('contacto') ) return $content;
	ob_start();
	?>
	<form id="frm-contact" action="">
		<label for="name">Nombre:</label>
		<input type="text" name="name" id="name" required>
		<br>
		<label for="email">Correo:</label>
		<input type="email" name="email" id="email" required>
		<br>
		<label for="message">Mensaje:</label>
		<textarea name="message" id="message" cols="30" rows="10" required></textarea>
		<br>

        <p>
            <input type="checkbox" id="terms" name="terms" required>
            <label id="lbl-terms" for="terms">Acepto los <a href="#">Términos y Condiciones</a></label>
        </p>

		<p class="frm-message"></p>

		<input id="submit" type="submit" name="submit" value="Enviar">
	</form>
	<?php
	$htm_form = ob_get_contents();
	ob_end_clean();

	return $content . $htm_form;
}

// Process ajax request
add_action('wp_ajax_nopriv_dcms_ajax_frm_contact','dcms_process_contact_form');
add_action('wp_ajax_dcms_ajax_frm_contact','dcms_process_contact_form');
function dcms_process_contact_form() {
	$nonce = $_POST['nonce']??'';

	dcms_validate_nonce($nonce, 'secret-key-form');

	$name = sanitize_text_field($_POST['name']??'');
	$email = sanitize_email($_POST['email']??'');
	$message = sanitize_textarea_field($_POST['message']??'');

	$adminmail = "destino@dominio.com";
	$subject = 'Formulario de contacto';
	$headers = "Reply-to: " . $name . " <" . $email . ">";

	$msg = "Nombre: " . $name . "\n";
	$msg .= "E-mail: " . $email . "\n\n";
	$msg .= "Mensaje: \n\n" . $message . "\n";

	$sent = wp_mail( $adminmail, $subject, $msg, $headers);

    $res = $sent ? [ 'status' => 1, 'message' => 'Se envió correctamente el formulario' ]
                    :[ 'status' => 0, 'message' => 'Hubo un error en el envío' ];

	wp_send_json($res);
}

function dcms_validate_nonce( $nonce, $nonce_name ){
	if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
		$res = [ 'status' => 0, 'message' => '✋ Error nonce validation!!' ];
		wp_send_json($res);
	}
}
