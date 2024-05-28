@extends('hotel.layout.app')

@section('main_content')
<div id="chat-box">
    <!-- Tin nhắn sẽ được thêm vào đây bằng JavaScript -->
</div>

<input type="text" id="user-input" placeholder="Type your message...">
<button onclick="sendMessage()">Send</button>

<script>
function sendMessage() {
    var message = document.getElementById("user-input").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            addMessage("You", message);
            addMessage("Gemini AI", response.response);
        }
    };
    xhttp.open("POST", "send_message.php", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({message: message}));
}

function addMessage(sender, message) {
    var chatBox = document.getElementById("chat-box");
    var messageElement = document.createElement("p");
    messageElement.textContent = sender + ": " + message;
    chatBox.appendChild(messageElement);
}
</script>
@endsection
