<?php

declare(strict_types=1);


namespace OCA\SmartCardTwoFactor\Service;;

use OCP\IUser;
use Psr\Log\LoggerInterface;

class SmartCardService
{
	private $presharedPassword;
	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
		$this->presharedPassword = "password1234";
	}

	/**
	 * 
	 * @param IUser $user 
	 * @return bool 
	 */
	public function authenticate(IUser $user): bool
	{
		// CONFIG:
		$host = $_SERVER["REMOTE_ADDR"];
		$port = 5050;

		// Create socket, set timeout and connect
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 1, 'usec' => 500000));
		$connected = socket_connect($socket, $host, $port);

		$auth_result = False;

		// if connected is true (successfull)
		if ($connected) {

			$this->logger->debug("Socket for smart card two factor authentication at (" . $host . ":" . $port . ") created and connected.");

			// get cryptosafe random bytes challenge
			$challenge = random_bytes(52);

			// send challenge via socket
			socket_send($socket, $challenge, 52, 0);

			// Read first byte, unpack it into array of integers and take first item
			// (unpack indexing starts at 1)
			$success = unpack('C*', socket_read($socket, 1))[1];

			if ($success) {
				$response = socket_read($socket, 20);
				$hash = sha1($challenge . $this->presharedPassword, true);
				$auth_result = $response == $hash;
			} else {
				$this->logger->error("Smart card two factor authentication failed for (" . $host . "). Please check local connector logs for more details.");
			}
		}

		$this->logger->warning("Socket for smart card two factor authentication at (" . $host . ":" . $port . ") created but not connected.");

		socket_close($socket);

		return $auth_result;
	}
}
