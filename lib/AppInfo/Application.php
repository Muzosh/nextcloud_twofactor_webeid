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

namespace OCA\TwoFactorWebEid\AppInfo;

use OCA\TwoFactorWebEid\Provider\WebEidProvider;
use OCA\TwoFactorWebEid\Service\WebEidService;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IURLGenerator;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class Application extends App implements IBootstrap
{
    public const APP_NAME = 'twofactor_webeid';

    public function __construct(array $urlParams = array())
    {
        parent::__construct(self::APP_NAME, $urlParams);
    }

    public function register(IRegistrationContext $context): void
    {
        require_once __DIR__.'/../../vendor/autoload.php';
        // $context->registerService(WebEidProvider::class, function (ContainerInterface $c): WebEidProvider {
        //     return new WebEidProvider(
        //         $c->get(LoggerInterface::class),
        //         $c->get(IURLGenerator::class),
        //         $c->get(WebEidService::class)
        //     );
        // });
    }

    public function boot(IBootContext $context): void
    {
    }
}
