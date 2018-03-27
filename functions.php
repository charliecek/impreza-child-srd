<?php
if (!function_exists('array_merge_recursive_simple')) {
  function array_merge_recursive_simple() {

      if (func_num_args() < 2) {
          trigger_error(__FUNCTION__ .' needs two or more array arguments', E_USER_WARNING);
          return;
      }
      $arrays = func_get_args();
      $merged = array();
      while ($arrays) {
          $array = array_shift($arrays);
          if (!is_array($array)) {
              trigger_error(__FUNCTION__ .' encountered a non array argument', E_USER_WARNING);
              return;
          }
          if (!$array)
              continue;
          foreach ($array as $key => $value)
              if (is_string($key))
                  if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key]))
                      $merged[$key] = call_user_func(__FUNCTION__, $merged[$key], $value);
                  else
                      $merged[$key] = $value;
              else
                  $merged[] = $value;
      }
      return $merged;
  }
}

/* Custom functions code goes here. */
add_action( 'admin_bar_menu', 'all_toolbar_nodes', 999 );

function all_toolbar_nodes( $wp_admin_bar ) {
  if (!current_user_can('manage_network')) {
    return;
  }
  $aNodes = array(
    'my-sites',
    'my-sites-super-admin',
    'blog-1',
    'blog-1-d',
    'blog-1-n',
    'blog-1-c',
  );
  $all_toolbar_nodes = $wp_admin_bar->get_nodes();
  foreach ( $all_toolbar_nodes as $node ) {
    if (!in_array($node->id, $aNodes)) {
      continue;
    }
    // use the same node's properties
    $args = $node;

    // put a span before the title
    //$args->title = '<pre class="my-class '.$node->id.'" style="display:none ;">'.var_export($node, true).'</pre>' . $node->title;
    
    // remove /wpsite in the address //
    $args->href = str_replace('/wpsite', '', $node->href);

    // update the Toolbar node
    $wp_admin_bar->add_node( $args );
  }

}

add_action( 'wp_head', 'kk_impreza_meta_tags', 4 );
function kk_impreza_meta_tags() {
  remove_action( 'wp_head', 'us_output_meta_tags', 5 );
  $oSite = get_blog_details();
  $bSkipOgAndTwitter = false;
  if ($oSite->domain == "festivaly.salsarueda.dance") {
    // The AI1EC plugin (and our filter below) takes care of OG and TWITTER meta tags //
    $bSkipOgAndTwitter = true;
  }
  
  // Some of the tags might be defined previously
  global $us_meta_tags;
  $us_meta_tags = apply_filters( 'us_meta_tags', isset( $us_meta_tags ) ? $us_meta_tags : array() );

  // Some must-have general tags
  if ( ! isset( $us_meta_tags['viewport'] ) ) {
    $us_meta_tags['viewport'] = 'width=device-width';
    if ( us_get_option( 'responsive_layout' ) ) {
      $us_meta_tags['viewport'] .= ', initial-scale=1';
    }
    $us_meta_tags['viewport'] = apply_filters( 'us_meta_viewport', $us_meta_tags['viewport'] );
  }
  if ( ! isset( $us_meta_tags['SKYPE_TOOLBAR'] ) ) {
    $us_meta_tags['SKYPE_TOOLBAR'] = 'SKYPE_TOOLBAR_PARSER_COMPATIBLE';
  }

  // Open Graph meta tags when needed
  if ( us_get_option( 'og_enabled' ) AND is_singular() AND isset( $GLOBALS['post'] ) ) {
    if ( ! isset( $us_meta_tags['og:title'] ) ) {
      $us_meta_tags['og:title'] = get_the_title();
    }
    if ( ! isset( $us_meta_tags['og:type'] ) ) {
      $us_meta_tags['og:type'] = 'website';
    }
    if ( ! isset( $us_meta_tags['og:url'] ) ) {
      $us_meta_tags['og:url'] = str_ireplace( "/wpsite", "", site_url( $_SERVER['REQUEST_URI'] ) );
    }
    if ( ! isset( $us_meta_tags['og:image'] ) AND ( $the_post_thumbnail_id = get_post_thumbnail_id() ) ) {
      $iMinSize = 200;
      $iSize = $iMinSize;
      $the_post_thumbnail_src = wp_get_attachment_image_src( $the_post_thumbnail_id, array($iSize, $iSize) );
      $iCount = 0;
      while ($iCount < 10 && $the_post_thumbnail_src && ($the_post_thumbnail_src[1] < $iMinSize || $the_post_thumbnail_src[2] < $iMinSize)) {
        $iSize += 100;
        $the_post_thumbnail_src = wp_get_attachment_image_src( $the_post_thumbnail_id, array($iSize, $iSize) );
        $iCount++;
      }
      if ( $the_post_thumbnail_src ) {
        $us_meta_tags['og:image'] = $the_post_thumbnail_src[0];
        if ( ! isset( $us_meta_tags['og:image:width'] ) AND  ! isset( $us_meta_tags['og:image:height'] ) ) {
          if (isset($the_post_thumbnail_src[1]) && isset($the_post_thumbnail_src[2])) {
            $us_meta_tags['og:image:width'] = $the_post_thumbnail_src[1];
            $us_meta_tags['og:image:height'] = $the_post_thumbnail_src[2];
          }
        }
      }
    }
    if ( ! isset( $us_meta_tags['og:description'] ) ) {
      if ( has_excerpt() AND ( $the_excerpt = get_the_excerpt() ) ) {
        $us_meta_tags['og:description'] = $the_excerpt;
      } else {
        // $aBlogInfo = get_blog_details(get_current_blog_id());
        $us_meta_tags['og:description'] = get_bloginfo('description');
      }
    }
  }
  if (!isset($us_meta_tags['fb:app_id']) || empty($us_meta_tags['fb:app_id'])) {
    $us_meta_tags['fb:app_id'] = '768253436664320'; // Kajo: SRD
  }

  $us_meta_tags = apply_filters( 'us_meta_tags_before_echo', isset( $us_meta_tags ) ? $us_meta_tags : array() );

  // Outputting the tags
  if ( isset( $us_meta_tags ) AND is_array( $us_meta_tags ) ) {
    foreach ( $us_meta_tags as $meta_name => $meta_content ) {
      if ( 0 === strpos($meta_name, 'og:') || 0 === strpos($meta_name, 'fb:') ) {
        $strType = "property";
      } else {
        $strType = "name";
      }
      if ($bSkipOgAndTwitter && (0 === strpos($meta_name, 'og:') || 0 === strpos($meta_name, 'twitter:'))) {
        continue;
      } 
      echo '<meta '.$strType.'="' . esc_attr( $meta_name ) . '" content="' . esc_attr( $meta_content ) . '">' . "\n";
    }
  }
}

add_filter( 'ai1ec_og_meta_tags', 'kk_fix_og_meta_tags', 10, 3 );
add_filter( 'ai1ec_twitter_meta_tags', 'kk_fix_og_meta_tags', 10, 3 );

function kk_fix_og_meta_tags( $aOgTags, $iPostID, $iInstanceID ) {
  if ( !isset($aOgTags['image']) || empty($aOgTags['image'])
        || !isset($aOgTags['image:width']) || empty($aOgTags['image:width'])
        || !isset($aOgTags['image:height']) || empty($aOgTags['image:height']) ) {
    if ( $the_post_thumbnail_id = get_post_thumbnail_id() ) {
      $iMinSize = 200;
      $iSize = $iMinSize;
      $the_post_thumbnail_src = wp_get_attachment_image_src( $the_post_thumbnail_id, array($iSize, $iSize) );
      $iCount = 0;
      while ($iCount < 10 && $the_post_thumbnail_src && ($the_post_thumbnail_src[1] < $iMinSize || $the_post_thumbnail_src[2] < $iMinSize)) {
        $iSize += 100;
        $the_post_thumbnail_src = wp_get_attachment_image_src( $the_post_thumbnail_id, array($iSize, $iSize) );
        $iCount++;
      }
      if ( $the_post_thumbnail_src ) {
        $aOgTags['image'] = $the_post_thumbnail_src[0];
        if ( ! isset( $aOgTags['image:width'] ) AND  ! isset( $aOgTags['image:height'] ) ) {
          if (isset($the_post_thumbnail_src[1]) && isset($the_post_thumbnail_src[2])) {
            $aOgTags['image:width'] = $the_post_thumbnail_src[1];
            $aOgTags['image:height'] = $the_post_thumbnail_src[2];
          }
        }
      }
      echo "<meta property=\"fb:app_id\" content=\"768253436664320\" />\n";
    }
  }
  return $aOgTags;
}


function srd__us_load_header_settings( $aSettings ) {
  $aHide = array( 'top_left', 'top_center', 'top_right' );
  $aOverwritten = array(
    'data' => array(
      'text:3' => array(
        'text' => '',
        'link' => '',
        'icon' => '',
        'size' => 0,
        'design_options' => 'hidden',
      ),
      'socials:1' => array (
        'facebook' => '',
        'twitter' => '',
        'google' => '',
        'linkedin' => '',
        'youtube' => '',
        'vimeo' => '',
        'flickr' => '',
        'behance' => '',
        'instagram' => '',
        'xing' => '',
        'pinterest' => '',
        'skype' => '',
        'dribbble' => '',
        'vk' => '',
        'tumblr' => '',
        'soundcloud' => '',
        'twitch' => '',
        'yelp' => '',
        'deviantart' => '',
        'foursquare' => '',
        'github' => '',
        'odnoklassniki' => '',
        's500px' => '',
        'houzz' => '',
        'medium' => '',
        'tripadvisor' => '',
        'rss' => '',
      ),
    ),
  );
  if (function_exists('pll_current_language')) {
    $iBlogID = get_current_blog_id();
    $sLangSlug = pll_current_language('slug');
    if ($iBlogID == 6 && $sLangSlug == 'en') {
//       echo "<!-- ".var_export($aSettings['data'], true)." -->";
      $aNewSettings = array_merge_recursive_simple($aSettings, $aOverwritten);
//       echo "<!-- ".var_export($aNewSettings, true)." -->";
      if (isset($aHide) && !empty($aHide)) {
        foreach ($aNewSettings as $key => $val) {
          if ($key == 'data' || !isset($val['layout']) || empty($val['layout'])) {
            continue;
          }
          foreach ($aHide as $where) {
            if (isset($val['layout'][$where]) && !empty($val['layout'][$where])) {
              $aNewSettings[$key]['layout']['hidden'] = array_merge($aNewSettings[$key]['layout']['hidden'], $val['layout'][$where]);
              $aNewSettings[$key]['layout'][$where] = array();
            }
          }
        }
      }
//       echo "<!-- ".var_export($aNewSettings, true)." -->";
      return $aNewSettings;
    }
  }
  return $aSettings;
}
add_filter( 'us_load_header_settings', 'srd__us_load_header_settings', 11 );

if (function_exists('pll_register_string')) {
  pll_register_string("Read More", "Read More", "Impreza");
}
// function srd_register_pll_strings() {
//   pll_register_string("Read More", "Read More", "Impreza");
// }
// add_action('wp_head', 'srd_register_pll_strings');
// add_action('admin_head', 'srd_register_pll_strings');

// apply_filters( 'gettext', $translations, $text, $domain );
function srd_gettext( $translations, $text, $domain ) {
  if ($translations != $text || !function_exists('pll_current_language')) {
    return $translations;
  }
  $sLangSlug = pll_current_language('slug');
  if ($sLangSlug == 'en') {
    return $translations;
  }
  if ($domain === "us") {
    return srd_us_gettext( $translations, $text, $domain, $sLangSlug );
  }
  return $translations;
}
function srd_us_gettext( $translations, $text, $domain, $lang ) {
  if ($text === "Read More") {
    $translated = pll__($text);
//     echo "<!-- " . var_export( array($translations, $translated, pll_translate_string($text, $lang), $text, $domain, $lang), true ) . " -->";
    return $translated;
  }
  return $translations;
}
add_filter( 'gettext', 'srd_gettext', 10, 3 );
