<?php

namespace OCA\TwoFactorSmartCard\Controller;

use OCA\TwoFactorSmartCard\Provider\SmartCardProvider;
use OCA\TwoFactorSmartCard\Service\SmartCardService;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\AppFramework\Controller;
use OCP\Authentication\TwoFactorAuth\IRegistry;

class SettingsController extends Controller
{
	private $service;

	/** @var IUserSession */
	private $userSession;

	/** @var IRegistry */
	private $registry;

	public function __construct($appName, IRequest $request, IUserSession $userSession, SmartCardService $service, IRegistry $registry, SmartCardProvider $provider)
	{
		parent::__construct($appName, $request);
		$this->userSession = $userSession;
		$this->service = $service;
		$this->provider = $provider;
		$this->registry = $registry;
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
		$status = $this->service->hasSecret($user);

		return array($status);
	}
}
