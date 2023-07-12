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

use GuzzleHttp\Psr7\Uri;
use web_eid\web_eid_authtoken_validation_php\certificate\CertificateData;
use web_eid\web_eid_authtoken_validation_php\certificate\CertificateLoader;
use web_eid\web_eid_authtoken_validation_php\challenge\ChallengeNonceGenerator;
use web_eid\web_eid_authtoken_validation_php\challenge\ChallengeNonceGeneratorBuilder;
use web_eid\web_eid_authtoken_validation_php\challenge\ChallengeNonceStore;
use web_eid\web_eid_authtoken_validation_php\validator\AuthTokenValidator;
use web_eid\web_eid_authtoken_validation_php\validator\AuthTokenValidatorBuilder;
use OCA\TwoFactorWebEid\AppInfo\Application;
use OCP\IConfig;
use OCP\ISession;
use OCP\IUser;
use phpseclib3\File\X509;
use Psr\Log\LoggerInterface;

class WebEidService
{
	/** @var ISession */
	private $session;

	/** @var LoggerInterface */
	private $logger;

	/** @var WebEidConfig */
	private $config;

	/** @var IConfig */
	private $userConfig;

	public function __construct(
		LoggerInterface $logger,
		ISession $session,
		WebEidConfig $config,
		IConfig $userConfig
	) {
		$this->session = $session;
		$this->logger = $logger;
		$this->config = $config;
		$this->userConfig = $userConfig;
	}

	public function authenticate(X509 $userCertificate, IUser $user): bool
	{
		$certCN = CertificateData::getSubjectCN($userCertificate);

		if (
			$this->userConfig->getUserValue(
				$user->getUID(),
				Application::APP_NAME,
				Application::SUBJECT_CN_KEY_NAME
			) === $certCN
		) {
			return true;
		}

		$this->logger->error(
			'WebEid authtoken validation successful, but CommonName does not match. UserID: ' .
				$user->getUID() .
				', CN: ' .
				$certCN
		);

		return false;
	}

	public function getSessionBasedChallengeNonceStore(): ChallengeNonceStore
	{
		return new SessionBackedChallengeNonceStore($this->session);
	}

	public function getGenerator(ChallengeNonceStore $challengeNonceStore): ChallengeNonceGenerator
	{
		return (new ChallengeNonceGeneratorBuilder())
			->withNonceTtl($this->config['CHALLENGE_NONCE_TTL_SECONDS'])
			->withChallengeNonceStore($challengeNonceStore)
			->build();
	}

	public function loadTrustedCACertificatesFromCertFiles(): array
	{
		$pathnames = array_map(
			'basename',
			glob(
				$this->config['TRUSTED_CERT_PATH'] . '/*.{crt,cer,pem,der}',
				GLOB_BRACE
			)
		);

		return CertificateLoader::loadCertificatesFromPath($this->config['TRUSTED_CERT_PATH'], ...$pathnames);
	}

	public function getValidator(): AuthTokenValidator
	{
		return (new AuthTokenValidatorBuilder())
			->withSiteOrigin(new Uri($this->config['ORIGIN']))
			->withTrustedCertificateAuthorities(...self::loadTrustedCACertificatesFromCertFiles())
			->withoutUserCertificateRevocationCheckWithOcsp()
			->build();
	}
}
