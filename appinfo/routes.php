<?php

return [
	'routes' => [
		['name' => 'settings#deletePassword', 'url' => '/settings/deletePassword', 'verb' => 'DELETE'],
		['name' => 'settings#getStatus', 'url' => '/settings/getStatus', 'verb' => 'GET'],
		['name' => 'settings#setPassword', 'url' => '/settings/setPassword', 'verb' => 'POST'],
	]
];
