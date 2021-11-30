<?php

namespace OCA\TwoFactorSmartCard\Controller;

use OCA\TwoFactorSmartCard\AppInfo\Application;
use OCA\TwoFactorSmartCard\Provider\SmartCardProvider;
use OCA\TwoFactorSmartCard\Service\SmartCardService;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\AppFramework\Controller;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use Psr\Log\LoggerInterface;

class SettingsController extends Controller
{
	private $service;

	/** @var IUserSession */
	private $userSession;

	/** @var IRegistry */
	private $registry;

	private $logger;

	public function __construct($appName, IRequest $request, IUserSession $userSession, SmartCardService $service, IRegistry $registry, SmartCardProvider $provider, LoggerInterface $logger)
	{
		parent::__construct($appName, $request);
		$this->userSession = $userSession;
		$this->service = $service;
		$this->provider = $provider;
		$this->registry = $registry;
		$this->logger = $logger;
	}

	public function setPassword($pass)
	{
		$user = $this->userSession->getUser();
		$this->service->storeSecret($user, $pass);
		$this->registry->enableProviderFor($this->provider, $user);
	}

	public function deletePassword()
	{
		$user = $this->userSession->getUser();
		$this->service->removeSecret($user);
		$this->registry->disableProviderFor($this->provider, $user);
	}

	public function getStatus()
	{
		$user = $this->userSession->getUser();
		$statusByCreds = $this->service->hasSecret($user);
		$statusByRegistry = $this->registry->getProviderStates($user)[Application::APP_NAME];

		if ($statusByRegistry === null) {
			$this->logger->error("Smartcard provider is not recognized at all.");
			return array(null);
		}

		if ($statusByCreds !== $statusByRegistry) {
			$this->logger->error("Status by set credentials does not match status from registry.");
			return array(null);
		}

		return array($statusByCreds);
	}
}
