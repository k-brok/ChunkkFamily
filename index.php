<html>
<body><script
			  src="https://code.jquery.com/jquery-3.7.1.min.js"
			  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
			  crossorigin="anonymous"></script>
    <div id="root"></div>
    <script>  

	$(document).ready(function(){
		var websocket = new WebSocket("ws://localhost:1234/websockets.php"); 
		websocket.onopen = function(event) { 
			$("#root").append("<div class='chat-connection-ack'>Connection is established!</div>");		
		}
		websocket.onmessage = function(event) {
			var Data = JSON.parse(event.data);
			$("#root").append("<div class='"+Data.message_type+"'>"+Data.message+"</div>");
		};
		
		websocket.onerror = function(event){
			$("#root").append("<div class='error'>Problem due to some Error</div>");
		};
		websocket.onclose = function(event){
			$("#root").append("<div class='chat-connection-ack'>Connection Closed</div>");
		}; 
	});
</script>

</body>
</html>