<div class="wrap gs-form">
   <div class="card" id="googlesheet">
      <h2 class="title"><?php echo esc_html(__('Contact Form 7 - Google Sheet Integration', 'gsconnector')); ?></h2>

      <br class="clear">

      <div class="inside">
         <p class="gs-alert"> <?php echo esc_html(__('Click "Get code" to retrieve your code from Google Drive to allow us to access your spreadsheets. And paste the code in the below textbox. ', 'gsconnector')); ?></p>
         <p class="gs-integration-box">
            <label><?php echo esc_html(__('Google Access Code', 'gsconnector')); ?></label>
            <?php if (!empty(get_option('gs_token')) && get_option('gs_token') !== "") { ?>
               <input type="text" name="gs-code" id="gs-code" value="" disabled placeholder="<?php echo esc_html(__('Currently Active', 'gsconnector')); ?>"/>
               <input type="button" name="deactivate-log" id="deactivate-log" value="<?php _e('Deactivate', 'gsconnector'); ?>" class="button button-primary" />
               <span class="tooltip"> <img src="<?php echo GS_CONNECTOR_PRO_URL; ?>assets/img/help.png" class="help-icon"> <span class="tooltiptext tooltip-right">On deactivation, all your data saved with authentication will be removed and you need to reauthenticate with your google account.</span></span>
               <span class="loading-sign-deactive">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <?php } else { ?>
               <input type="text" name="gs-code" id="gs-code" value="" placeholder="<?php echo esc_html(__('Enter Code', 'gsconnector')); ?>"/>
               <a href="https://accounts.google.com/o/oauth2/auth?access_type=offline&approval_prompt=force&client_id=1021473022177-agam4fkd36jkefe9ru8bvrsrara7b7s3.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&response_type=code&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F" target="_blank" class="button">Get Code</a>
            <?php } ?>

            <?php if (empty(get_option('gs_token'))) { ?>
            <p><input type="button" name="save-gs-code" id="save-gs-code" value="<?php _e('Save', 'gsconnector'); ?>"
                      class="button button-primary" /></p>
            <?php } ?>
         <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
         </p>

         <p>
            <label><?php echo esc_html(__('Debug Log', 'gsconnector')); ?></label>
            <label><a href= "<?php echo GS_CONNECTOR_PRO_URL . 'logs/log.txt'; ?>" target="_blank" class="debug-view" >View</a></label>
            <label><a class="debug-clear" ><?php echo esc_html(__('Clear', 'gsconnector')); ?></a></label><span class="clear-loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
         </p>
         <p id="gs-validation-message"></p>
         <span id="deactivate-message"></span>
         <p class="gs-sync-row"><?php echo __('<a id="gs-sync" data-init="yes">Click here </a>  to fetch Sheet details to be set at Contact Forms Google Sheet Pro settings.', 'gsconnector'); ?><span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
         <!-- set nonce -->
         <input type="hidden" name="gs-ajax-nonce" id="gs-ajax-nonce" value="<?php echo wp_create_nonce('gs-ajax-nonce'); ?>" />

      </div>
   </div>
</div>