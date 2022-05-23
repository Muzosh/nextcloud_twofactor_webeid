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

namespace OCA\TwoFactorWebEid\Service;

use DateTime;
use muzosh\web_eid_authtoken_validation_php\challenge\ChallengeNonce;
use muzosh\web_eid_authtoken_validation_php\challenge\ChallengeNonceStore;
use OCP\ISession;

class SessionBackedChallengeNonceStore extends ChallengeNonceStore {
	private const CHALLENGE_NONCE_KEY = 'web-eid-challenge-nonce';

	private $session;

	public function __construct(ISession $session) {
		$this->session = $session;
	}

	/**
	 * @UseSession
	 */
	public function put(ChallengeNonce $challengeNonce): void {
		$this->session[self::CHALLENGE_NONCE_KEY] = serialize($challengeNonce);
	}

	/**
	 * @UseSession
	 */
	protected function getAndRemoveImpl(): ?ChallengeNonce {
		if (!$this->session[self::CHALLENGE_NONCE_KEY]) {
			return null;
		}

		$challengeNonce = unserialize($this->session[self::CHALLENGE_NONCE_KEY], array(
			'allowed_classes' => array(ChallengeNonce::class, DateTime::class),
		));

		if (!$challengeNonce) {
			return null;
		}

		unset($this->session[self::CHALLENGE_NONCE_KEY]);

		return $challengeNonce;
	}
}
