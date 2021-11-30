<?php

/**
 *
 * @copyright Copyright (c) 2021, Petr Muzikant (petr.muzikant@vut.cz)
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace OCA\TwoFactorSmartCard\Controller;

use OCA\TwoFactorSmartCard\AppInfo\Application;
use OCA\TwoFactorSmartCard\Provider\SmartCardProvider;
use OCA\TwoFactorSmartCard\Service\SmartCardService;
use OCP\AppFramework\Controller;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\IRequest;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class SettingsController extends Controller
{
	private $service;

	/** @var IUserSession */
	private $userSession;

	/** @var IRegistry */
	private $registry;

	private $logger;

	public function __construct(
		$appName,
		IRequest $request,
		IUserSession $userSession,
		SmartCardService $service,
		IRegistry $registry,
		SmartCardProvider $provider,
		LoggerInterface $logger
	) {
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
