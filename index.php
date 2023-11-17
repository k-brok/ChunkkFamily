<html>
<body><script
			  src="https://code.jquery.com/jquery-3.7.1.min.js"
			  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
			  crossorigin="anonymous"></script>
    <div id="chat-box"></div>
<script>  
	function showMessage(messageHTML) {
		$('#chat-box').append(messageHTML);
	}

	$(document).ready(function(){
		const evtSource = new EventSource("/websocket.php", {
			withCredentials: true,
		});
 
		evtSource.onopen = function(event) { 
			showMessage("<div class='chat-connection-ack'>Connection is established!</div>");		
		}
		evtSource.onmessage = function(event) {
			var Data = JSON.parse(event.data);
			showMessage("<div class='"+Data.message_type+"'>"+Data.message+"</div>");
			$('#chat-message').val('');
		};
		
		evtSource.onerror = function(event){
			console.log("evtSource error: ", event);
			showMessage("<div class='error'>Error: "+event+"</div>");
		};
		evtSource.onclose = function(event){
			showMessage("<div class='chat-connection-ack'>Connection Closed</div>");
		}; 
		
		evtSource.addEventListener("ping", (event) => {
			const time = JSON.parse(event.data).time;
			showMessage("<div class='error'>ping: "+time+"</div>");
});

		$('#frmChat').on("submit",function(event){
			event.preventDefault();
			$('#chat-user').attr("type","hidden");		
			var messageJSON = {
				chat_user: $('#chat-user').val(),
				chat_message: $('#chat-message').val()
			};
			evtSource.send(JSON.stringify(messageJSON));
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