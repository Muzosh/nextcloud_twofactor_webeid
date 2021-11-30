<?php

namespace OCA\TwoFactorSmartCard\Provider;

use LogicException;
use OCA\TwoFactorSmartCard\AppInfo\Application;
use OCA\TwoFactorSmartCard\Service\SmartCardService;
use OCA\TwoFactorSmartCard\Settings\PersonalSettings;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\Authentication\TwoFactorAuth\IProvidesPersonalSettings;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Template;

class SmartCardProvider implements IProvider, IProvidesIcons, IProvidesPersonalSettings
{
	/** @var SmartCardService */
	private $smartCardService;

	/** @var IURLGenerator */
	private $urlGenerator;

	/**
	 * @param SmartCardService $smartCardService
	 */
	public function __construct(SmartCardService $smartCardService, IURLGenerator $urlGenerator)
	{
		$this->smartCardService = $smartCardService;
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
	 */
	public function verifyChallenge(IUser $user, $challenge): bool
	{
		if (!$this->smartCardService->hasSecret($user)) {
			throw new LogicException("Provider shouldn't be enabled for somebody who doesn't have his password set!");
		}

		return $this->smartCardService->authenticate($user);
	}

	/**
	 * Decides whether 2FA is enabled for the given user
	 *
	 * @param IUser $user
	 * @return boolean
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user): bool
	{
		return $this->smartCardService->hasSecret($user);
	}

	public function getLightIcon(): string
	{
		return $this->urlGenerator->imagePath(Application::APP_NAME, 'app.svg');
	}

	public function getDarkIcon(): string
	{
		return $this->urlGenerator->imagePath(Application::APP_NAME, 'app.svg');
	}

	public function getPersonalSettings(IUser $user): IPersonalProviderSettings
	{
		return new PersonalSettings();
	}
}
