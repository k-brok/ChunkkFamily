<html>
<body><script
			  src="https://code.jquery.com/jquery-3.7.1.min.js"
			  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
			  crossorigin="anonymous"></script>
    <div id="root"></div>
<script>  
	function showMessage(messageHTML) {
		$('#chat-box').append(messageHTML);
	}

	$(document).ready(function(){
		var websocket = new WebSocket("ws://localhost:8090/websockets.php"); 
		websocket.onopen = function(event) { 
			showMessage("<div class='chat-connection-ack'>Connection is established!</div>");		
		}
		websocket.onmessage = function(event) {
			var Data = JSON.parse(event.data);
			showMessage("<div class='"+Data.message_type+"'>"+Data.message+"</div>");
			$('#chat-message').val('');
		};
		
		websocket.onerror = function(event){
			showMessage("<div class='error'>Problem due to some Error</div>");
		};
		websocket.onclose = function(event){
			showMessage("<div class='chat-connection-ack'>Connection Closed</div>");
		}; 
		
		$('#frmChat').on("submit",function(event){
			event.preventDefault();
			$('#chat-user').attr("type","hidden");		
			var messageJSON = {
				chat_user: $('#chat-user').val(),
				chat_message: $('#chat-message').val()
			};
			websocket.send(JSON.stringify(messageJSON));
		});
	});
</script>
<form id="frmChat">
  <label for="fname">First name:</label><br>
  <input type="text" id="chat-user" name="fname" value="John"><br>
  <label for="lname">Last name:</label><br>
  <input type="text" id="chat-message" name="lname" value="Doe"><br><br>
  <input type="submit" value="Submit">
</form> 
</body>
</html>