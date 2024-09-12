<?php
// Recibir mensajes desde Slack
function wp_chat_slack_receive_message() {
    $input = file_get_contents('php://input');
    $event_data = json_decode($input, true);

    error_log('Evento recibido de Slack: ' . print_r($event_data, true));

    if (isset($event_data['type']) && $event_data['type'] === 'url_verification') {
        header('Content-Type: application/json');
        echo json_encode(['challenge' => $event_data['challenge']]);
        exit;
    }

    if (isset($event_data['event']) && isset($event_data['event']['text'])) {
        $message = sanitize_text_field($event_data['event']['text']);
        
        // Guardar el mensaje en la base de datos en un array
        $messages = get_option('slack_messages', []);
        $messages[] = $message;
        update_option('slack_messages', $messages);

        error_log('Mensaje guardado: ' . $message);

        wp_send_json_success(['status' => 'ok']);
    } else {
        error_log('Error: No se recibiэ┤ un evento vижlido de Slack.');
    }

    wp_send_json_success(['status' => 'ok']);
}
?>