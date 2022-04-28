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

use LogicException;
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

class WebEidProvider implements IProvider, IProvidesIcons, IActivatableByAdmin, IDeactivatableByAdmin
{
    /** @var WebEidService */
    private $webEidService;

    /** @var IURLGenerator */
    private $urlGenerator;

    /** @var IRegistry */
    private $registry;

    public function __construct(
        WebEidService $webEidService,
        IURLGenerator $urlGenerator,
		IRegistry $registry
    ) {
        $this->webEidService = $webEidService;
        $this->urlGenerator = $urlGenerator;
		$this->registry = $registry;
    }

    public function enableFor(IUser $user)
    {
		$this->registry->enableProviderFor($this, $user);
    }

    public function disableFor(IUser $user)
    {
		$this->registry->disableProviderFor($this, $user);
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
        return new Template(Application::APP_NAME, 'challenge');
    }

    /**
     * Verify the given challenge.
     *
     * @param string $challenge
     */
    public function verifyChallenge(IUser $user, $challenge): bool
    {
        if (!$this->webEidService->hasSecret($user)) {
            //throw new LogicException("Provider shouldn't be enabled for somebody who doesn't have his password set!");
        }

        return false;//$this->webEidService->authenticate($user);
    }

    /**
     * Decides whether 2FA is enabled for the given user.
     */
    public function isTwoFactorAuthEnabledForUser(IUser $user): bool
    {
        return $this->webEidService->hasSecret($user);
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
