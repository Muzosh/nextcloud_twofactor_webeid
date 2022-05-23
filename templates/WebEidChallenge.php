<?php
style('twofactor_webeid', 'style');
script('twofactor_webeid', 'web-eid');
script('twofactor_webeid', 'web-eid-challenge');
?>

<img class="two-factor-icon" src="<?php print_unescaped(image_path('twofactor_webeid', 'webeid-card.svg')); ?>">

<!-- <p><?php p('Custom Web-eID solution must be installed on your machine!') ?></p> -->
<p><?php p('Please insert your smartcard and click on button below.') ?></p>

<form id="webeid-form" method="POST" class="webeid-form">
	<input id="webeid-nonce" type="text" hidden="true" value="<?php p($_['nonce'][0]) ?>" readonly="readonly">
	<input id="webeid-token" type="text" name="challenge" hidden="true" value="" readonly="readonly">
	<div id="webeid-loading" style="margin-bottom:20px; display:none">
		<span class="icon-loading-dark webeid-loading-spinner"></span>
	</div>
	<span id="webeid-error"></span>
	<button id="webeid-authenticate" class="primary two-factor-submit" type="button">
		<?php p('Authenticate'); ?>
	</button>
</form>