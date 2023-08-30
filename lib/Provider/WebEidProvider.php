<?php

/**
 *
 * @copyright Copyright (c) 2022, Petr Muzikant (petr.muzikant@vut.cz)
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

use web_eid\web_eid_authtoken_validation_php\authtoken\WebEidAuthToken;
use web_eid\web_eid_authtoken_validation_php\exceptions\AuthTokenException;
use web_eid\web_eid_authtoken_validation_php\exceptions\ChallengeNonceExpiredException;
use web_eid\web_eid_authtoken_validation_php\exceptions\ChallengeNonceNotFoundException;
use OCA\TwoFactorWebEid\AppInfo\Application;
use OCA\TwoFactorWebEid\Service\WebEidService;
use OCP\Authentication\TwoFactorAuth\IActivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Template;
use Psr\Log\LoggerInterface;

class WebEidProvider implements IProvider, IProvidesIcons, IActivatableByAdmin, IDeactivatableByAdmin
{
	/** @var LoggerInterface */
	private $logger;

	/** @var IURLGenerator */
	private $urlGenerator;

	/** @var WebEidService */
	private $webEidService;

	/** @var IRegistry */
	private $registry;

	public function __construct(
		LoggerInterface $logger,
		IURLGenerator $urlGenerator,
		WebEidService $webEidService,
		IRegistry $registry
	) {
		$this->logger = $logger;
		$this->urlGenerator = $urlGenerator;
		$this->webEidService = $webEidService;
		$this->registry = $registry;
	}

	public function enableFor(IUser $user)
	{
		$this->registry->enableProviderFor($this, $user);
	}

	public function disableFor(IUser $user)
	{
		$this->registry->enableProviderFor($this, $user);
	}

	/**
	 * Get unique identifier of this 2FA provider.
	 */
	public function getId(): string
	{
		return Application::APP_NAME;
	}

	/**
	 * Get the display name for selecting the 2FA provider.
	 */
	public function getDisplayName(): string
	{
		return 'Web-eID 2FA';
	}

	/**
	 * Get the description for selecting the 2FA provider.
	 */
	public function getDescription(): string
	{
		return 'This provider enables second authentication factor using Web-eID.';
	}

	/**
	 * Get the template for rending the 2FA provider view.
	 */
	public function getTemplate(IUser $user): Template
	{
		$generator = $this->webEidService->getGenerator(
			$this->webEidService->getSessionBasedChallengeNonceStore()
		);
		$challengeNonce = $generator->generateAndStoreNonce();

		$template = new Template(Application::APP_NAME, 'WebEidChallenge');
		$template->append('nonce', $challengeNonce->getBase64EncodedNonce());

		return $template;
	}

	/**
	 * Verify the given challenge.
	 *
	 * @param string $challenge
	 */
	public function verifyChallenge(IUser $user, $challenge): bool
	{
		try {
			$challengeNonce = $this->webEidService->getSessionBasedChallengeNonceStore()->getAndRemove();
			try {
				$userCertificate = $this->webEidService->getValidator()->validate(
					new WebEidAuthToken($challenge),
					$challengeNonce->getBase64EncodedNonce()
				);

				return $this->webEidService->authenticate($userCertificate, $user);
			} catch (AuthTokenException $e) {
				$this->logger->error('WebEid authtoken validation unsuccessful: ' . $e->getMessage(), $e->getTrace());
			}
		} catch (ChallengeNonceNotFoundException $e) {
			$this->logger->error('WebEid challenge not found: ' . $e->getMessage(), $e->getTrace());
		} catch (ChallengeNonceExpiredException $e) {
			$this->logger->error('WebEid challenge nonce expired: ' . $e->getMessage(), $e->getTrace());
		}

		return false;
	}

	public function isTwoFactorAuthEnabledForUser(IUser $user): bool
	{
		$providerStates = $this->registry->getProviderStates($user);

		return array_key_exists(Application::APP_NAME, $providerStates)
			? boolval($providerStates[Application::APP_NAME])
			: false;
	}

	public function getLightIcon(): string
	{
		return $this->urlGenerator->imagePath(Application::APP_NAME, 'webeid-light.svg');
	}

	public function getDarkIcon(): string
	{
		return $this->urlGenerator->imagePath(Application::APP_NAME, 'webeid-dark.svg');
	}
}
