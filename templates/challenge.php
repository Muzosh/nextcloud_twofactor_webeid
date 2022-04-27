<?php
style('twofactor_webeid', 'style');
?>

<img class="two-factor-icon" src="<?php print_unescaped(image_path('twofactor_webeid', 'app.svg')); ?>" alt="">

<p><?php p('Custom Web-eID solution must be installed on your machine!') ?></p>
<p><?php p('Please insert your smartcard and click on button below.') ?></p>

<form name="webeidform" method="POST" class="webeid-form">
	<button class="primary two-factor-submit" name="challenge" type="submit">
		<?php p('Authenticate'); ?>
	</button>
</form>