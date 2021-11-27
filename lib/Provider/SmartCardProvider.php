<?php

namespace OCA\SmartCardTwoFactor\Provider;

use OCA\SmartCardTwoFactor\Service\SmartCardService;
use OCP\Authentication\TwoFactorAuth\IActivatableAtLogin;
use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\ILoginSetupProvider;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Template;

class SmartCardProvider implements IProvider, IProvidesIcons
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
		return 'smartcard';
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
		// If necessary, this is also the place where you might want
		// to send out a code via e-mail or SMS.

		// 'challenge' is the name of the template
		return new Template('twofactor_smartcard', 'challenge');
	}

	/**
	 * Verify the given challenge
	 *
	 * @param IUser $user
	 * @param string $challenge
	 */
	public function verifyChallenge(IUser $user, $challenge): bool
	{
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
		// return $this->smartCardService->hasSecret($user);
		// 2FA is enforced for all users
		return true;
	}

	public function getLightIcon(): string
	{
		return $this->urlGenerator->imagePath('twofactor_smartcard', 'app.svg');
	}

	public function getDarkIcon(): string
	{
		return $this->urlGenerator->imagePath('twofactor_smartcard', 'app.svg');
	}
}
