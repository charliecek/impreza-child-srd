<?php 
/*
* This is the page users will see logged in. 
* You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
* The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/
?>
<div class="lwa">
  <?php
    florp_profile_form();
    $user = wp_get_current_user();
    if ($lwa_data['hide_info_box'] != '1') :
  ?>
  <div class="florp-user-info-box">
    <span class="lwa-title-sub"><!--Dobrý deň, --><span class="florp_onchange florp_first_name"><?php echo $user->first_name; ?></span> <span class="florp_onchange florp_last_name"><?php echo $user->last_name; ?></span></span>
    <table>
      <tr>
        <td class="avatar lwa-avatar" style="display: none;">
          <?php // echo get_avatar( $user->ID, $size = '50' ); // no Avatar - there is no means of setting it anyway // ?>
        </td>
        <td class="lwa-info" style="text-align: center;">
          <?php
            //Admin URL
            if ( $lwa_data['profile_link'] == '1' ) {
              if( function_exists('bp_loggedin_user_link') ){
                ?>
                <a href="<?php bp_loggedin_user_link(); ?>"><?php esc_html_e('Profile','login-with-ajax') ?></a><br/>
                <?php	
              }else{
                ?>
                <a href="<?php echo trailingslashit(get_admin_url()); ?>profile.php"><?php esc_html_e('Profile','login-with-ajax') ?></a><br/>
                <?php	
              }
            }
            //Logout URL
            ?>
            <a id="wp-logout" href="<?php echo wp_logout_url() ?>"><?php esc_html_e( 'Log Out' ,'login-with-ajax') ?></a><br />
            <?php
            //Blog Admin
            if( current_user_can('list_users') ) {
              ?>
              <a href="<?php echo get_admin_url(); ?>"><?php esc_html_e("blog admin", 'login-with-ajax'); ?></a>
              <?php
            }
          ?>
        </td>
      </tr>
    </table>
  </div>
  <?php
    endif;
  ?>
</div>
