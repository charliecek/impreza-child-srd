<?php 
/*
 * This is the page users will see logged out. 
 * You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
 * The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/
?>
<div class="lwa lwa-default lwa-register-only" data-id-prefix="registration-form-only_">
  <span class="lwa-status"></span>
  <form class="lwa-form" action="<?php echo esc_attr(LoginWithAjax::$url_login); ?>" method="post" style="display:none;" >
    <div class="lwa-username">
      <?php $msg = __('Username','login-with-ajax'); ?>
      <label for="registration-form-only_lwa_user_login"><?php esc_html_e( $msg, 'login-with-ajax' ); ?></label>
      <input type="text" name="log" id="registration-form-only_lwa_user_login" class="input"  value="<?php echo esc_attr($msg); ?>" onfocus="if(this.value == '<?php echo esc_attr($msg); ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo esc_attr($msg); ?>'}" />
    </div>

    <div class="lwa-password">
      <?php $msg = __('Password','login-with-ajax'); ?>
      <label for="registration-form-only_lwa_user_pass"><?php esc_html_e( $msg, 'login-with-ajax' ); ?></label>
      <input type="password" name="pwd" id="registration-form-only_lwa_user_pass" class="input"  value="<?php echo esc_attr($msg); ?>" onfocus="if(this.value == '<?php echo esc_attr($msg); ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo esc_attr($msg); ?>'}" />
    </div>
    
    <div class="lwa-login_form">
      <?php do_action('login_form'); ?>
    </div>
   
    <div class="lwa-submit-button">
      <input type="submit" name="wp-submit" id="registration-form-only_lwa_wp-submit" value="<?php esc_attr_e('Log In','login-with-ajax'); ?>" tabindex="100" />
      <input type="hidden" name="lwa_profile_link" value="<?php echo esc_attr($lwa_data['profile_link']); ?>" />
      <input type="hidden" name="login-with-ajax" value="login" />
      <?php if( !empty($lwa_data['redirect']) ): ?>
      <input type="hidden" name="redirect_to" value="<?php echo esc_url($lwa_data['redirect']); ?>" />
      <?php endif; ?>
    </div>
    
    <div class="lwa-links">
    </div>
  </form>
  <?php if ( get_option('users_can_register') && !empty($lwa_data['registration']) && $lwa_data['registration'] == 1 ) : ?>
    <div class="lwa-register" style="display:block;">
      <form class="registerform" data-id-prefix="registration-form-only_" action="<?php echo esc_attr(LoginWithAjax::$url_register); ?>" method="post">
        <p><strong><?php //esc_html_e('Register For This Site','login-with-ajax'); ?></strong></p>         
        <div class="lwa-leader">
          <?php 
            $msg = "Som organizátor Rueda flashmobu";
          ?>
          <input type="checkbox" name="is_leader" id="registration-form-only_is_leader" value="true"/>   
          <strong><label for="registration-form-only_is_leader"><?php esc_html_e( $msg,'login-with-ajax' ) ?></label></strong>
        </div>
        <div class="lwa-username">
          <?php $msg = __('Username','login-with-ajax') . " (bez medzier)";// . __('without spaces','login-with-ajax') . ")"; ?>
          <label for="registration-form-only_user_login"><?php esc_html_e( $msg,'login-with-ajax' ) ?></label>
          <input type="text" name="user_login" id="registration-form-only_user_login" value="" placeholder="<?php echo esc_attr($msg); ?>"/>   
        </div>
        <div class="lwa-email">
          <?php $msg = __('E-mail','login-with-ajax'); ?>
          <label for="registration-form-only_user_email"><?php esc_html_e( $msg,'login-with-ajax' ) ?></label>
          <input type="text" name="user_email" id="registration-form-only_user_email"  value="" placeholder="<?php echo esc_attr($msg); ?>"/>   
        </div>
        <div class="lwa-password">
          <?php $msg = __('Password','login-with-ajax'); ?>
          <label for="registration-form-only_user_password"><?php esc_html_e( $msg,'login-with-ajax' ) ?></label>
          <input type="password" name="user_password" id="registration-form-only_user_password"  value="" placeholder="<?php echo esc_attr($msg); ?>"/>   
        </div>
        <?php
          // If you want other plugins to play nice, you need this: 
          // do_action('register_form'); 
        ?>
        <p class="lwa-submit-button">
          <?php //esc_html_e('A password will be e-mailed to you.','login-with-ajax') ?>
<!--           Heslo vám bude zaslané na vašu emailovú adresu. -->
          <br />
          <input type="submit" name="wp-submit" id="registration-form-only_wp-submit" class="button-primary" data-id-prefix="registration-form-only_" value="<?php esc_attr_e('Register', 'login-with-ajax'); ?>" tabindex="100" />
          <input type="hidden" name="login-with-ajax" value="register" />
        </p>
      </form>
    </div>
  <?php endif; ?>
</div>