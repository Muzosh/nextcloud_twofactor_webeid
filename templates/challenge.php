<?php
style('twofactor_webeid', 'style');
script('twofactor_webeid', 'web-eid');
script('twofactor_webeid', 'web-eid-challenge');
?>

<img class="two-factor-icon" src="<?php print_unescaped(image_path('twofactor_webeid', 'app.svg')); ?>" alt="">

<p><?php p('Custom Web-eID solution must be installed on your machine!') ?></p>
<p><?php p('Please insert your smartcard and click on button below.') ?></p>

<form name="webeidform" method="POST" class="webeid-form">
	<input id="webeid-challenge" name="challenge" type="text" hidden="true" value="testValue" readonly="readonly">
	<button id="webeid-submit" name="authenticate" class="primary two-factor-submit" type="button">
		<?php p('Authenticate'); ?>
	</button>
</form>