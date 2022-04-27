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

namespace OCA\TwoFactorWebEid\Provider;

use LogicException;
use OCA\TwoFactorWebEid\AppInfo\Application;
use OCA\TwoFactorWebEid\Service\WebEidService;
use OCA\TwoFactorWebEid\Settings\PersonalSettings;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\Authentication\TwoFactorAuth\IProvidesPersonalSettings;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Template;

class WebEidProvider implements IProvider, IProvidesIcons, IProvidesPersonalSettings
{
	/** @var WebEidService */
	private $webEidService;

	/** @var IURLGenerator */
	private $urlGenerator;

	/**
	 * @param WebEidService $webEidService
	 */
	public function __construct(
		WebEidService $webEidService,
		IURLGenerator $urlGenerator
	) {
		$this->webEidService = $webEidService;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * Get unique identifier of this 2FA provider
	 *
	 * @return string
	 */
	public function getId(): string
	{
		return Application::APP_NAME;
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDisplayName(): string
	{
		return 'Smart Card Two Factor';
	}

	/**
	 * Get the description for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Smart card two-factor authentication';
	}

	/**
	 * Get the template for rending the 2FA provider view
	 *
	 * @param IUser $user
	 * @return Template
	 */
	public function getTemplate(IUser $user): Template
	{
		return new Template(Application::APP_NAME, 'challenge');
	}

	/**
	 * Verify the given challenge
	 *
	 * @param IUser $user
	 * @param string $challenge
	 * @return bool
	 */
	public function verifyChallenge(IUser $user, $challenge): bool
	{
		if (!$this->webEidService->hasSecret($user)) {
			throw new LogicException("Provider shouldn't be enabled for somebody who doesn't have his password set!");
		}

		return $this->webEidService->authenticate($user);
	}

	/**
	 * Decides whether 2FA is enabled for the given user
	 *
	 * @param IUser $user
	 * @return boolean
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user): bool
	{
		return $this->webEidService->hasSecret($user);
	}

	/**
	 * @return string
	 */
	public function getLightIcon(): string
	{
		return $this->urlGenerator->imagePath(Application::APP_NAME, 'app.svg');
	}

	/**
	 * @return string
	 */
	public function getDarkIcon(): string
	{
		return $this->urlGenerator->imagePath(Application::APP_NAME, 'app.svg');
	}

	/**
	 * @param IUser $user
	 * @return IPersonalProviderSettings
	 */
	public function getPersonalSettings(IUser $user): IPersonalProviderSettings
	{
		return new PersonalSettings();
	}
}
