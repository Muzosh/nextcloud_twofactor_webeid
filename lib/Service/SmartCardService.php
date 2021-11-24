<?php

declare(strict_types=1);


namespace OCA\SmartCardTwoFactor\Service;

use OC;
use OCA\SmartCardTwoFactor\Provider\YubikeyProvider;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\IUser;
use OCA\SmartCardTwoFactor\Db\YubiKey;
use OCA\SmartCardTwoFactor\Db\YubiKeyMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\Authentication\TwoFactorAuth\TwoFactorException;

use OCP\Activity\IManager;
use OCP\ILogger;
use OCP\IRequest;
use OCP\ISession;

class SmartCardService
{
	/**
	 * @var string
	 */
	private $presharedPassword = "password1234";

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
			// get cryptosafe random bytes challenge
			$challenge = random_bytes(52);

			// send challenge via socket
			socket_send($socket, $challenge, 52, 0);

			// Read first byte, unpack it into array of integers and take first item
			// (unpack indexing starts at 1)
			if (unpack('C*', socket_read($socket, 1))[1]) {
				$response = socket_read($socket, 20);
				$hash = sha1($challenge . $this->presharedPassword, true);
				$auth_result = $response == $hash;
			}

			socket_close($socket);
		}

		return $auth_result;
	}
}
