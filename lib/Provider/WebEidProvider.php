<?php

/**
 * @copyright Copyright (c) 2021, Petr Muzikant (petr.muzikant@vut.cz)
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
 */

declare(strict_types=1);

namespace OCA\TwoFactorWebEid\Provider;

use muzosh\web_eid_authtoken_validation_php\authtoken\WebEidAuthToken;
use muzosh\web_eid_authtoken_validation_php\certificate\CertificateData;
use muzosh\web_eid_authtoken_validation_php\challenge\ChallengeNonceGenerator;
use muzosh\web_eid_authtoken_validation_php\challenge\ChallengeNonceStore;
use muzosh\web_eid_authtoken_validation_php\exceptions\AuthTokenException;
use muzosh\web_eid_authtoken_validation_php\validator\AuthTokenValidator;
use OCA\TwoFactorWebEid\AppInfo\Application;
use OCA\TwoFactorWebEid\Service\WebEidService;
use OCP\Authentication\TwoFactorAuth\IActivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
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

    /** @var ChallengeNonceGenerator */
    private $generator;

    /** @var ChallengeNonceStore */
    private $nonceStore;

    /** @var AuthTokenValidator */
    private $validator;

    public function __construct(
        LoggerInterface $logger,
        IURLGenerator $urlGenerator,
        WebEidService $webEidService
    ) {
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
        $this->webEidService = $webEidService;
        $this->nonceStore = $this->webEidService->getSessionBasedChallengeNonceStore();
        $this->generator = $this->webEidService->getGenerator($this->nonceStore);
        $this->validator = $this->webEidService->getValidator();
    }

    public function enableFor(IUser $user): void
    {
    }

    public function disableFor(IUser $user): void
    {
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
        return 'Web-eID smart-card 2FA';
    }

    /**
     * Get the description for selecting the 2FA provider.
     */
    public function getDescription(): string
    {
        return 'temp description';
    }

    /**
     * Get the template for rending the 2FA provider view.
     */
    public function getTemplate(IUser $user): Template
    {
        $challengeNonce = $this->generator->generateAndStoreNonce();

        $template = new Template(Application::APP_NAME, 'challenge');
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
        $challengeNonce = $this->nonceStore->getAndRemove();
        if (is_null($challengeNonce)) {
            // TODO: handle it
            return false;
        }

        try {
            $cert = $this->validator->validate(
                new WebEidAuthToken($challenge),
                $challengeNonce->getBase64EncodedNonce()
            );

            return $user->getUID() == CertificateData::getSubjectCN($cert);
        } catch (AuthTokenException $e) {
            $this->logger->error('WebEid authentication token validation unsuccessful: '.$e->getMessage(), $e->getTrace());

            return false;
        }
    }

    /**
     * Decides whether 2FA is enabled for the given user.
     */
    public function isTwoFactorAuthEnabledForUser(IUser $user): bool
    {
        return true;
    }

    public function getLightIcon(): string
    {
        return $this->urlGenerator->imagePath(Application::APP_NAME, 'app.svg');
    }

    public function getDarkIcon(): string
    {
        return $this->urlGenerator->imagePath(Application::APP_NAME, 'app.svg');
    }
}
