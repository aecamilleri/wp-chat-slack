<?php
// Enviar mensaje desde WordPress a Slack
function wp_chat_slack_send_message() {
    if (isset($_POST['chat-message']) && isset($_SESSION['user_name'])) {
        $message = sanitize_text_field($_POST['chat-message']);
        $user_name = $_SESSION['user_name'];
        $user_ip = $_SESSION['user_ip'];

        $formatted_message = $user_name . ' (IP: ' . $user_ip . '): ' . $message;

        $webhook_url = get_option('wp_chat_slack_webhook', '');

        $payload = json_encode(array(
            'text' => $formatted_message
        ));

        $args = array(
            'body'        => $payload,
            'headers'     => array('Content-Type' => 'application/json'),
            'method'      => 'POST',
            'data_format' => 'body',
        );

        wp_remote_post($webhook_url, $args);
    }
}
add_action('wp_ajax_wp_chat_slack_send_message', 'wp_chat_slack_send_message');
add_action('wp_ajax_nopriv_wp_chat_slack_send_message', 'wp_chat_slack_send_message');
?>