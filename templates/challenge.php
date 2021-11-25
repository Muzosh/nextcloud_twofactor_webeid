<?php
style('twofactor_smartcard', 'style');
?>

<img class="two-factor-icon" src="<?php print_unescaped(image_path('twofactor_smartcard', 'app.svg')); ?>" alt="">

<p><?php p('Please insert your smartcard and click on button below.') ?></p>

<form name="smartcardform" method="POST" class="smartcard-form">
	<button class="primary two-factor-submit" name="challenge" type="submit">
		<?php p('Authenticate'); ?>
	</button>
</form>