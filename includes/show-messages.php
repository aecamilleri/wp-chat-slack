<?php
// Mostrar mensajes recibidos desde Slack en el frontend mediante AJAX
function wp_chat_slack_show_messages() {
    $messages = get_option('slack_messages', []);
    
    // Obtener la URL base del plugin (la URL del directorio del plugin)
    $plugin_dir = plugin_dir_url(dirname(__FILE__)); // URL base del directorio del plugin
    $user_icon_url = $plugin_dir . 'img/user.svg'; // Ruta correcta para el ícono del usuario
    $slack_icon_url = $plugin_dir . 'img/slackUser.svg'; // Ruta correcta para el ícono de Slack

    // Inyectar el CSS en línea
    echo '<style>
        .chat-message {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .chat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .user-message .chat-icon {
            background-image: url(' . esc_url($user_icon_url) . ');
            background-size: cover;
            background-position: center;
        }
        
        .slack-message .chat-icon {
            background-image: url(' . esc_url($slack_icon_url) . ');
            background-size: cover;
            background-position: center;
        }
        
        .chat-message p {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
            word-wrap: break-word; /* Asegura que el texto se ajuste dentro del contenedor */
        }

        .user-message {
            flex-direction: row; /* Alinear ícono y mensaje en fila */
        }

        .slack-message {
            flex-direction: row-reverse; /* Alinear ícono y mensaje en fila, con el ícono a la izquierda */
            justify-content: flex-start;
        }
    </style>';

    // Mostrar los mensajes
    foreach ($messages as $message) {
        if (strpos($message, ' (IP:') !== false) {
            // Mensaje enviado por el usuario del frontend
            echo '<div class="chat-message user-message">';
            echo '<img src="' . esc_url($user_icon_url) . '" alt="User Icon" class="chat-icon">';
            echo '<p>' . esc_html($message) . '</p>';
            echo '</div>';
        } else {
            // Mensaje recibido desde Slack
            echo '<div class="chat-message slack-message">';
            echo '<img src="' . esc_url($slack_icon_url) . '" alt="Slack Icon" class="chat-icon">';
            echo '<p>' . esc_html($message) . '</p>';
            echo '</div>';
        }
    }
    wp_die(); 
}

add_action('wp_ajax_wp_chat_slack_fetch_messages', 'wp_chat_slack_show_messages');
add_action('wp_ajax_nopriv_wp_chat_slack_fetch_messages', 'wp_chat_slack_show_messages');


?>