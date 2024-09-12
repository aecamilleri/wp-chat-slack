jQuery(document).ready(function($) {
    let lastMessage = ''; // Variable para almacenar el último mensaje recibido

    $('#wp-chat-slack-form').on('submit', function(e) {
        e.preventDefault();
        var message = $('#chat-message').val();
        console.log('Enviando mensaje:', message);

        $.ajax({
            url: wp_chat_slack.ajax_url,
            type: 'POST',
            data: {
                action: 'wp_chat_slack_send_message',
                'chat-message': message
            },
            success: function(response) {
                $('#chat-message').val('');
                console.log('Respuesta del servidor:', response);
                fetchMessages(); // Llamar a la función para actualizar los mensajes
            }
        });
    });

    function fetchMessages() {
        console.log('Actualizando mensajes...');
        $.ajax({
            url: wp_chat_slack.ajax_url,
            type: 'POST',
            data: {
                action: 'wp_chat_slack_fetch_messages'
            },
            success: function(response) {
                console.log('Mensajes recibidos:', response);
                
                // Comparar el mensaje recibido con el último mensaje mostrado
                if (response.trim() !== lastMessage.trim()) {
                    $('#chat-messages').html(response);
                    lastMessage = response.trim(); // Actualizar el último mensaje mostrado
                } else {
                    console.log('El mensaje no ha cambiado, no se actualiza el frontend.');
                }
            }
        });
    }

    // Llamar a la función para cargar los mensajes al cargar la página
    fetchMessages();

    // Polling every 10 seconds to check for new messages
    setInterval(fetchMessages, 10000);
});
