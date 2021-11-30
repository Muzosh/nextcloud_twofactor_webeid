<?php
style('twofactor_smartcard', 'smartcard-personal');
script('twofactor_smartcard', 'smartcard-personal');
?>

<span><?php p('Create new smartcard password:') ?></span>
<input id="twofactor_smartcard-smartcard-password" type="text" value="" placeholder="<?php p('12 characters'); ?>" />
<input id="twofactor_smartcard-submit-button" type="button" value="Create/Overwrite" />
<input id="twofactor_smartcard-delete-button" type="button" value="Delete" />
<p><?php p('Status: ') ?><span id="twofactor_smartcard-loading"><span class="icon-loading-small twofactor_smartcard-loading-spinner"></span></span><span id="twofactor_smartcard-status"></span></p>
<p><span id="twofactor_smartcard-settings-msg" style="display: none;"></span></p>