<?php

declare(strict_types=1);

namespace OCA\TwoFactorSmartCard\Settings;

use OCA\TwoFactorSmartCard\AppInfo\Application;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Template;

class PersonalSettings implements IPersonalProviderSettings
{
	public function getBody(): Template
	{
		return new Template(Application::APP_NAME, 'personal');
	}
}
