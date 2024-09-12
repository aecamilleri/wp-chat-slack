<?php
/*
Plugin Name: WP Chat Slack
Description: Plugin para integrar chat en el frontend con Slack.
Version: 1.0
Author: Alejandro Camilleri
*/

session_start(); // Inicia la sesi車n para almacenar datos del usuario

// Incluir archivos separados
require_once plugin_dir_path(__FILE__) . 'includes/rest-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/show-messages.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/send-message.php';
require_once plugin_dir_path(__FILE__) . 'includes/receive-message.php';

// Agregar una entrada al menú lateral
function agregar_menu_personalizado() {
    add_menu_page(
        'Mi Plugin', // Título de la página
        'Mi Plugin', // Texto del menú
        'manage_options', // Capacidad requerida para acceder
        'mi-plugin', // Slug único
        'mostrar_pagina_personalizada', // Función para mostrar la página
        'dashicons-admin-plugins', // Icono (puedes cambiarlo)
        25 // Posición en el menú
    );
}
add_action('admin_menu', 'agregar_menu_personalizado');

// Función para mostrar la página personalizada
function mostrar_pagina_personalizada() {
    // Guardar el webhook si se ha enviado el formulario
    if (isset($_POST['slack_webhook'])) {
        $webhook = sanitize_text_field($_POST['slack_webhook']);
        update_option('wp_chat_slack_webhook', $webhook);
        echo '<div class="updated"><p>Webhook guardado correctamente.</p></div>';
    }

    // Obtener el valor actual del webhook
    $webhook = get_option('wp_chat_slack_webhook', '');

    // Mostrar el formulario de configuración
    echo '<div class="wrap">
        <h1>Configuración de WP Chat Slack</h1>
        <form method="post" action="">
            <label for="slack_webhook">Webhook de Slack:</label>
            <input type="text" id="slack_webhook" name="slack_webhook" value="' . esc_attr($webhook) . '" style="width: 100%;" required>
            <br><br>
            <input type="submit" class="button-primary" value="Guardar Webhook">
        </form>
    </div>';
}

?>
