<?php

declare(strict_types=1);


namespace OCA\TwoFactorSmartCard\Service;

;

use OCP\IUser;
use OCP\Security\ICredentialsManager;
use Psr\Log\LoggerInterface;
use RangeException;

class SmartCardService {
	private const credentialKey = "smartcard_password";

	private $logger;
	private $credentialsManager;

	public function __construct(LoggerInterface $logger, ICredentialsManager $credentialsManager) {
		$this->logger = $logger;
		$this->credentialsManager = $credentialsManager;
	}

	public function storeSecret(IUser $user, string $secret) {
		if (strlen($secret) != 12) {
			throw new RangeException("Smartcard password must be of length 12!");
		}

		$this->credentialsManager->store($user->getUID(), $this::credentialKey, $secret);
	}

	public function removeSecret(IUser $user) {
		return $this->credentialsManager->delete($user->getUID(), $this::credentialKey);
	}

	public function getSecret(IUser $user): string {
		return $this->credentialsManager->retrieve($user->getUID(), $this::credentialKey);
	}

	public function hasSecret(IUser $user): bool {
		return boolval($this->credentialsManager->retrieve($user->getUID(), $this::credentialKey));
	}

	/**
	 *
	 * @param IUser $user
	 * @return bool
	 */
	public function authenticate(IUser $user): bool {
		return true;
		// CONFIG:
		$host = $_SERVER["REMOTE_ADDR"];
		$port = 5050;

		// Create socket, set timeout and connect
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 1, 'usec' => 500000));
		$connected = socket_connect($socket, $host, $port);

		$auth_result = false;

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
				$hash = sha1($challenge . $this->getSecret($user), true);
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
