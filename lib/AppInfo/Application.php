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

namespace OCA\TwoFactorWebEid\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use phpseclib3\File\ASN1;

class Application extends App implements IBootstrap {
	public const APP_NAME = 'twofactor_webeid';
	public const SUBJECT_CN_KEY_NAME = 'subject_cn';

	public function __construct(array $urlParams = array()) {
		parent::__construct(self::APP_NAME, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		require_once __DIR__.'/../../vendor/autoload.php';
		ASN1::loadOIDs([
            'id-pkix-ocsp-nonce' => '1.3.6.1.5.5.7.48.1.2',
            'id-sha1' => '1.3.14.3.2.26',
            'qcStatements(3)' => '1.3.6.1.5.5.7.1.3',
            'street' => '2.5.4.9',
            'id-pkix-ocsp-basic' => '1.3.6.1.5.5.7.48.1.1',
            'id-pkix-ocsp' => '1.3.6.1.5.5.7.48.1',
            'secp384r1' => '1.3.132.0.34',
            'id-pkix-ocsp-archive-cutoff' => '1.3.6.1.5.5.7.48.1.6',
            'id-pkix-ocsp-nocheck' => '1.3.6.1.5.5.7.48.1.5',
			"dilithium2" => "1.3.6.1.4.1.2.267.7.4.4",
			"p256_dilithium2" => "1.3.9999.2.7.1",
			"rsa3072_dilithium2" => "1.3.9999.2.7.2",
			"dilithium3" => "1.3.6.1.4.1.2.267.7.6.5",
			"p384_dilithium3" => "1.3.9999.2.7.3",
			"dilithium5" => "1.3.6.1.4.1.2.267.7.8.7",
			"p521_dilithium5" => "1.3.9999.2.7.4",
			"dilithium2_aes" => "1.3.6.1.4.1.2.267.11.4.4",
			"p256_dilithium2_aes" => "1.3.9999.2.11.1",
			"rsa3072_dilithium2_aes" => "1.3.9999.2.11.2",
			"dilithium3_aes" => "1.3.6.1.4.1.2.267.11.6.5",
			"p384_dilithium3_aes" => "1.3.9999.2.11.3",
			"dilithium5_aes" => "1.3.6.1.4.1.2.267.11.8.7",
			"p521_dilithium5_aes" => "1.3.9999.2.11.4",
			"falcon512" => "1.3.9999.3.1",
			"p256_falcon512" => "1.3.9999.3.2",
			"rsa3072_falcon512" => "1.3.9999.3.3",
			"falcon1024" => "1.3.9999.3.4",
			"p521_falcon1024" => "1.3.9999.3.5",
			"sphincsharaka128frobust" => "1.3.9999.6.1.1",
			"p256_sphincsharaka128frobust" => "1.3.9999.6.1.2",
			"rsa3072_sphincsharaka128frobust" => "1.3.9999.6.1.3",
			"sphincsharaka128fsimple" => "1.3.9999.6.1.4",
			"p256_sphincsharaka128fsimple" => "1.3.9999.6.1.5",
			"rsa3072_sphincsharaka128fsimple" => "1.3.9999.6.1.6",
			"sphincsharaka128srobust" => "1.3.9999.6.1.7",
			"p256_sphincsharaka128srobust" => "1.3.9999.6.1.8",
			"rsa3072_sphincsharaka128srobust" => "1.3.9999.6.1.9",
			"sphincsharaka128ssimple" => "1.3.9999.6.1.10",
			"p256_sphincsharaka128ssimple" => "1.3.9999.6.1.11",
			"rsa3072_sphincsharaka128ssimple" => "1.3.9999.6.1.12",
			"sphincsharaka192frobust" => "1.3.9999.6.2.1",
			"p384_sphincsharaka192frobust" => "1.3.9999.6.2.2",
			"sphincsharaka192fsimple" => "1.3.9999.6.2.3",
			"p384_sphincsharaka192fsimple" => "1.3.9999.6.2.4",
			"sphincsharaka192srobust" => "1.3.9999.6.2.5",
			"p384_sphincsharaka192srobust" => "1.3.9999.6.2.6",
			"sphincsharaka192ssimple" => "1.3.9999.6.2.7",
			"p384_sphincsharaka192ssimple" => "1.3.9999.6.2.8",
			"sphincsharaka256frobust" => "1.3.9999.6.3.1",
			"p521_sphincsharaka256frobust" => "1.3.9999.6.3.2",
			"sphincsharaka256fsimple" => "1.3.9999.6.3.3",
			"p521_sphincsharaka256fsimple" => "1.3.9999.6.3.4",
			"sphincsharaka256srobust" => "1.3.9999.6.3.5",
			"p521_sphincsharaka256srobust" => "1.3.9999.6.3.6",
			"sphincsharaka256ssimple" => "1.3.9999.6.3.7",
			"p521_sphincsharaka256ssimple" => "1.3.9999.6.3.8",
			"sphincssha256128frobust" => "1.3.9999.6.4.1",
			"p256_sphincssha256128frobust" => "1.3.9999.6.4.2",
			"rsa3072_sphincssha256128frobust" => "1.3.9999.6.4.3",
			"sphincssha256128fsimple" => "1.3.9999.6.4.4",
			"p256_sphincssha256128fsimple" => "1.3.9999.6.4.5",
			"rsa3072_sphincssha256128fsimple" => "1.3.9999.6.4.6",
			"sphincssha256128srobust" => "1.3.9999.6.4.7",
			"p256_sphincssha256128srobust" => "1.3.9999.6.4.8",
			"rsa3072_sphincssha256128srobust" => "1.3.9999.6.4.9",
			"sphincssha256128ssimple" => "1.3.9999.6.4.10",
			"p256_sphincssha256128ssimple" => "1.3.9999.6.4.11",
			"rsa3072_sphincssha256128ssimple" => "1.3.9999.6.4.12",
			"sphincssha256192frobust" => "1.3.9999.6.5.1",
			"p384_sphincssha256192frobust" => "1.3.9999.6.5.2",
			"sphincssha256192fsimple" => "1.3.9999.6.5.3",
			"p384_sphincssha256192fsimple" => "1.3.9999.6.5.4",
			"sphincssha256192srobust" => "1.3.9999.6.5.5",
			"p384_sphincssha256192srobust" => "1.3.9999.6.5.6",
			"sphincssha256192ssimple" => "1.3.9999.6.5.7",
			"p384_sphincssha256192ssimple" => "1.3.9999.6.5.8",
			"sphincssha256256frobust" => "1.3.9999.6.6.1",
			"p521_sphincssha256256frobust" => "1.3.9999.6.6.2",
			"sphincssha256256fsimple" => "1.3.9999.6.6.3",
			"p521_sphincssha256256fsimple" => "1.3.9999.6.6.4",
			"sphincssha256256srobust" => "1.3.9999.6.6.5",
			"p521_sphincssha256256srobust" => "1.3.9999.6.6.6",
			"sphincssha256256ssimple" => "1.3.9999.6.6.7",
			"p521_sphincssha256256ssimple" => "1.3.9999.6.6.8",
			"sphincsshake256128frobust" => "1.3.9999.6.7.1",
			"p256_sphincsshake256128frobust" => "1.3.9999.6.7.2",
			"rsa3072_sphincsshake256128frobust" => "1.3.9999.6.7.3",
			"sphincsshake256128fsimple" => "1.3.9999.6.7.4",
			"p256_sphincsshake256128fsimple" => "1.3.9999.6.7.5",
			"rsa3072_sphincsshake256128fsimple" => "1.3.9999.6.7.6",
			"sphincsshake256128srobust" => "1.3.9999.6.7.7",
			"p256_sphincsshake256128srobust" => "1.3.9999.6.7.8",
			"rsa3072_sphincsshake256128srobust" => "1.3.9999.6.7.9",
			"sphincsshake256128ssimple" => "1.3.9999.6.7.10",
			"p256_sphincsshake256128ssimple" => "1.3.9999.6.7.11",
			"rsa3072_sphincsshake256128ssimple" => "1.3.9999.6.7.12",
			"sphincsshake256192frobust" => "1.3.9999.6.8.1",
			"p384_sphincsshake256192frobust" => "1.3.9999.6.8.2",
			"sphincsshake256192fsimple" => "1.3.9999.6.8.3",
			"p384_sphincsshake256192fsimple" => "1.3.9999.6.8.4",
			"sphincsshake256192srobust" => "1.3.9999.6.8.5",
			"p384_sphincsshake256192srobust" => "1.3.9999.6.8.6",
			"sphincsshake256192ssimple" => "1.3.9999.6.8.7",
			"p384_sphincsshake256192ssimple" => "1.3.9999.6.8.8",
			"sphincsshake256256frobust" => "1.3.9999.6.9.1",
			"p521_sphincsshake256256frobust" => "1.3.9999.6.9.2",
			"sphincsshake256256fsimple" => "1.3.9999.6.9.3",
			"p521_sphincsshake256256fsimple" => "1.3.9999.6.9.4",
			"sphincsshake256256srobust" => "1.3.9999.6.9.5",
			"p521_sphincsshake256256srobust" => "1.3.9999.6.9.6",
			"sphincsshake256256ssimple" => "1.3.9999.6.9.7",
			"p521_sphincsshake256256ssimple" => "1.3.9999.6.9.8",
		]);
	}

	public function boot(IBootContext $context): void {
	}
}
