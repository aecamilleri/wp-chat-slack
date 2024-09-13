<?php
// Cargar scripts y estilos necesarios en el frontend
function wp_chat_slack_enqueue_scripts() {
    // Encolar el archivo JS
    wp_enqueue_script('wp-chat-slack-js', plugins_url('../js/wp-chat-slack.js', __FILE__), array('jquery'), null, true);
    
    // Pasar parámetros de Ajax a JavaScript
    wp_localize_script('wp-chat-slack-js', 'wp_chat_slack', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
    
    // Encolar el archivo CSS
    wp_enqueue_style('wp-chat-slack-css', plugins_url('../css/wp-chat-slack.css', __FILE__));

    // Encolar el JS para manejar la burbuja de chat
    wp_enqueue_script('wp-chat-bubble-js', plugins_url('../js/wp-chat-bubble.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'wp_chat_slack_enqueue_scripts');

?>