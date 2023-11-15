<?php

class socketclient(){
	private $host;
	private $port;
	private $socket;
	
	function __construct($name) {
		$this->host = 'localhost';
		$this->port = '1234';
		
		$this->connect();
	}
	
	public function connect(): bool {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            return false;
        }
        if (@socket_connect($this->socket, $this->host, $this->port) === false) {
            return false;
        }
        return true;
    }
	
	public function sendData(string $data): void {
        socket_write($this->socket, $data, strlen($data));
    }
}

$client = new socketclient();