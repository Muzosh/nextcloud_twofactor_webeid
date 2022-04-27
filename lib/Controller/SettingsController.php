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

namespace OCA\TwoFactorWebEid\Controller;

use OCA\TwoFactorWebEid\AppInfo\Application;
use OCA\TwoFactorWebEid\Provider\WebEidProvider;
use OCA\TwoFactorWebEid\Service\WebEidService;
use OCP\AppFramework\Controller;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\IRequest;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class SettingsController extends Controller
{
	/** @var WebEidService */
	private $service;

	/** @var IUserSession */
	private $userSession;

	/** @var IRegistry */
	private $registry;

	/** @var LoggerInterface */
	private $logger;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IUserSession $userSession
	 * @param WebEidService $service
	 * @param IRegistry $registry
	 * @param WebEidProvider $provider
	 * @param LoggerInterface $logger
	 */
	public function __construct(
		$appName,
		IRequest $request,
		IUserSession $userSession,
		WebEidService $service,
		IRegistry $registry,
		WebEidProvider $provider,
		LoggerInterface $logger
	) {
		parent::__construct($appName, $request);
		$this->userSession = $userSession;
		$this->service = $service;
		$this->provider = $provider;
		$this->registry = $registry;
		$this->logger = $logger;
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 * 
	 * @param string $pass
	 */
	public function setPassword($pass)
	{
		$user = $this->userSession->getUser();
		$this->service->storeSecret($user, $pass);
		$this->registry->enableProviderFor($this->provider, $user);
	}

	/**
	 * @NoAdminRequired
	 * @PasswordConfirmationRequired
	 */
	public function deletePassword()
	{
		$user = $this->userSession->getUser();
		$this->service->removeSecret($user);
		$this->registry->disableProviderFor($this->provider, $user);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @return array $statusByRegistry
	 */
	public function getStatus()
	{
		$user = $this->userSession->getUser();
		$statusByCreds = $this->service->hasSecret($user);
		$statusByRegistry = $this->registry->getProviderStates($user)[Application::APP_NAME];

		if ($statusByRegistry === null) {
			$this->logger->error("WebEid provider is not recognized at all.");
			return array(null);
		}

		if ($statusByCreds !== $statusByRegistry) {
			$this->logger->error("Status by set credentials does not match status from registry.");
			return array(null);
		}

		return array($statusByRegistry);
	}
}
