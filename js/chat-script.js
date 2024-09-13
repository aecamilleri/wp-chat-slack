function toggleChat() {
    const chatBox = document.getElementById('chat-box');
    chatBox.style.display = chatBox.style.display === 'none' ? 'block' : 'none';
}

function closeChat() {
    document.getElementById('chat-box').style.display = 'none';
}
