<?php

// /**
//  *
//  * @copyright Copyright (c) 2022, Petr Muzikant (petr.muzikant@vut.cz)
//  *
//  * @license GNU AGPL version 3 or any later version
//  *
//  * This program is free software: you can redistribute it and/or modify
//  * it under the terms of the GNU Affero General Public License as
//  * published by the Free Software Foundation, either version 3 of the
//  * License, or (at your option) any later version.
//  *
//  * This program is distributed in the hope that it will be useful,
//  * but WITHOUT ANY WARRANTY; without even the implied warranty of
//  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  * GNU Affero General Public License for more details.
//  *
//  * You should have received a copy of the GNU Affero General Public License
//  * along with this program.  If not, see <https://www.gnu.org/licenses/>.
//  *
//  */

// declare(strict_types=1);

// namespace OCA\TwoFactorWebEid\Controller;

// use OC\Core\Controller\TwoFactorChallengeController;
// use OCP\AppFramework\Controller;
// use OCP\IRequest;

// class ChallengeController extends TwoFactorChallengeController
// {
// 	/**
// 	 * @param string $appName
// 	 * @param IRequest $request
// 	 */
// 	public function __construct(
// 		$appName,
// 		IRequest $request
// 	) {
// 		parent::__construct($appName, $request);
// 	}
// }
// apparently controller methods cannot be called during 2FA: see https://help.nextcloud.com/t/expose-controller-method-for-2fa-application-to-obtain-challenge-nonce-csfr-check-failed-but-no-error-was-generated-in-log-files/138270