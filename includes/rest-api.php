<?php
// Registrar la ruta REST API
add_action('rest_api_init', function() {
    register_rest_route('wp-chat-slack/v1', '/receive-message', array(
        'methods' => 'POST',
        'callback' => 'wp_chat_slack_receive_message',
        'permission_callback' => '__return_true', // Permitir el acceso sin autenticaci¨®n
    ));
});
?>