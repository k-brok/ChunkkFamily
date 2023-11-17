<?php
$address = '127.0.0.1';
$port    = 1234;

echo "Starting socket server on $address:$port\n";

set_time_limit(0);
ob_implicit_flush();

$null = null;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    showErrorAndDie("socket_create()");
}

if (socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1) === false) {
    showErrorAndDie("socket_set_option()");
}

if (socket_bind($socket, $address, $port) === false) {
    showErrorAndDie("socket_bind()");
}

if (socket_listen($socket) === false) {
    showErrorAndDie("socket_listen()");
}

$clients = array($socket);

while (true) {
    //manage multiple connections
    $changed = $clients;
    //returns the socket resources in $changed array
    socket_select($changed, $null, $null, $null, 0);

    //check for new clients
    if (in_array($socket, $changed)) {
        $socket_new   = socket_accept($socket); //accept new socket
        $clients[]    = $socket_new; //add socket to client array
        $found_socket = array_search($socket, $changed);
        unset($changed[$found_socket]);
    }

    //loop through all connected clients
    foreach ($changed as $changed_socket) {
        $buf = @socket_read($changed_socket, 1024);
        if ($buf === false || $buf === "") { // check disconnected client
            // remove client for $clients array
            $found_socket = array_search($changed_socket, $clients);
            unset($clients[$found_socket]);
        } else {
            // A message was received
            $command = $buf[0];
            $data    = substr($buf, 1);
            switch ($command) {
                // Get mode
                case '0':
                    sendMode();
                    break;
                // Set mode
                case '1':
                    if (in_array($data, $allowedModes)) {
                        $currentBBMode = $data;
                    }
                    sendMode();
                    break;
                // Echo
                case '2':
                    sendMessage('{"action":"echo","data":"' . $data . '"}');
                    break;
                // Invalid command
                default:
                    echo "Unknown command " . $buf;
            }
        }
    }
}

function sendMessage(string $msg): void {
    global $clients;
    foreach ($clients as $changed_socket) {
        @socket_write($changed_socket, $msg, strlen($msg));
    }
}

/**
 * @return never
 */
function showErrorAndDie(string $functionName): void {
    echo $functionName . " failed. Reason: " . socket_strerror(socket_last_error()) . "\n";
    sleep(5);
    die();
}