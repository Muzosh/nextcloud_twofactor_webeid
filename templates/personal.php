<?php
// style('twofactor_webeid', 'webeid-personal');
// script('twofactor_webeid', 'webeid-personal');
?>

<span><?php p('Create new smartcard password:') ?></span>
<input id="twofactor_webeid-password" type="text" value="" placeholder="<?php p('12 characters'); ?>" />
<input id="twofactor_webeid-submit-button" type="button" value="Create/Overwrite" />
<input id="twofactor_webeid-delete-button" type="button" value="Delete" />
<p><?php p('Status: ') ?><span id="twofactor_webeid-loading"><span class="icon-loading-small twofactor_webeid-loading-spinner"></span></span><span id="twofactor_webeid-status"></span></p>
<p><span id="twofactor_webeid-settings-msg" style="display: none;"></span></p>