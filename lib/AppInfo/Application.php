<?php

declare(strict_types=1);

namespace OCA\TwoFactorSmartCard\AppInfo;

use OCP\AppFramework\App;

class Application extends App
{
	public const APP_NAME = 'twofactor_smartcard';

	public function __construct(array $urlParams = [])
	{
		parent::__construct(self::APP_NAME, $urlParams);
	}
}
