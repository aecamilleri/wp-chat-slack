<?php
// Función para obtener la ubicación del usuario
function get_user_location($ip) {
    $api_key = 'dd81e95e16c641eba6fc0fd3b355ffa6'; // Reemplaza con tu API key de ipgeolocation.io
    $url = "https://api.ipgeolocation.io/ipgeo?apiKey=$api_key&ip=$ip";

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return 'Ubicación desconocida';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['city']) && isset($data['country_name'])) {
        return $data['city'] . ', ' . $data['country_name'];
    }

    return 'Ubicación desconocida';
}

// Mostrar formulario de registro inicial o chat
function wp_chat_slack_shortcode() {
    ob_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_name']) && isset($_POST['user_email'])) {
        // Procesar el formulario de registro inicial
        $_SESSION['user_name'] = sanitize_text_field($_POST['user_name']);
        $_SESSION['user_email'] = sanitize_email($_POST['user_email']);
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['session_id'] = uniqid('session_', true); // Generar un ID de sesión único
        
        // Obtener la ubicación del usuario
        $_SESSION['user_location'] = get_user_location($_SESSION['user_ip']);
        
        // Limpiar mensajes anteriores
        update_option('slack_messages', []);
        
        // Enviar datos del usuario a Slack
        wp_chat_slack_send_user_info();

        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['end_session'])) {
        // Terminar la sesión
        session_destroy();
        wp_redirect(home_url()); // Redirigir a la página de inicio o a otra página específica
        exit;
    }

    if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_ip']) || !isset($_SESSION['user_email'])) {
        // Mostrar formulario de registro inicial
        ?>
        <form id="wp-chat-slack-initial-form" method="post">
            <label for="user_name">Nombre:</label>
            <input type="text" id="user_name" name="user_name" required>
            <label for="user_email">Correo Electrónico:</label>
            <input type="email" id="user_email" name="user_email" required>
            <input type="submit" value="Iniciar Chat">
        </form>
        <?php
    } else {
        // Mostrar formulario de chat y mensajes
        ?>
        <div id="chat-messages">
        <p>¡Bienvenido al chat! ¿En qué podemos ayudarte hoy?</p> <!-- Mensaje de bienvenida -->
    
        <?php
        // Obtener todos los mensajes recibidos desde Slack
        $messages = get_option('slack_messages', []);
    
        // Mostrar todos los mensajes recibidos, excepto el primer mensaje de bienvenida
        $first_message = true;
        foreach ($messages as $message) {
            // Omite el primer mensaje
            if ($first_message) {
                $first_message = false;
                continue;
            }
    
            // Mostrar mensaje si no contiene la palabra "New chat from"
            if (strpos($message, 'New chat from') === false) {
                echo '<p>' . esc_html($message) . '</p>';
            }
        }
        ?>
    </div>


        <form id="wp-chat-slack-form" method="post">
            <input type="text" id="chat-message" name="chat-message" placeholder="Escribe tu mensaje...">
            <button type="submit">Enviar</button>
        </form>
        <form id="end-session-form" method="post">
            <input type="hidden" name="end_session" value="1">
            <button type="submit">Terminar Sesión</button>
        </form>
        <?php
    }

    return ob_get_clean();
}

add_shortcode('wp_chat_slack', 'wp_chat_slack_shortcode');

// Función para enviar datos del usuario a Slack
function wp_chat_slack_send_user_info() {
    $user_name = $_SESSION['user_name'];
    $user_email = $_SESSION['user_email'];
    $user_ip = $_SESSION['user_ip'];
    $session_id = $_SESSION['session_id'];
    $user_location = $_SESSION['user_location'];
    $user_browser = $_SERVER['HTTP_USER_AGENT'];
    $user_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Página desconocida';

    $formatted_message = "*New chat from* *$user_name*.\n\n*Visitor Information*\n> *Name:* $user_name\n> *Location:* $user_location\n> *IP Address:* $user_ip\n> *Email:* $user_email\n> *Browser:* $user_browser\n> *Page:* <$user_page|$user_page>";

    $webhook_url = get_option('wp_chat_slack_webhook', '');

    if (!empty($webhook_url)) {
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
    } else {
        error_log('Error: Webhook de Slack no configurado.');
    }
}
