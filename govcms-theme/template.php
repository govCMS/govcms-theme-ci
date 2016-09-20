<?php

/**
 * Page alter.
 */
function govcmstheme_bootstrap_page_alter($page) {
  $mobileoptimized = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'MobileOptimized',
      'content' => 'width'
    )
  );
  $handheldfriendly = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'HandheldFriendly',
      'content' => 'true'
    )
  );
  $viewport = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'viewport',
      'content' => 'width=device-width, initial-scale=1, maximum-scale=2, user-scalable=0'
    )
  );
  drupal_add_html_head($mobileoptimized, 'MobileOptimized');
  drupal_add_html_head($handheldfriendly, 'HandheldFriendly');
  drupal_add_html_head($viewport, 'viewport');
}

/**
 * Override or insert variables into the html template.
 */
function govcmstheme_bootstrap_process_html(&$vars) {
  // Hook into color.module
  if (module_exists('color')) {
    _color_html_alter($vars);
  }
}

function govcmstheme_bootstrap_preprocess_html(&$vars) {
  $path = drupal_get_path_alias();
  $aliases = explode('/', $path);

  foreach($aliases as $alias) {
    if($alias == 'search') {
      $vars['classes_array'][] = 'search-results';
    }
  }
}


/**
 * Preprocess variables for block.tpl.php
 */
function govcmstheme_bootstrap_preprocess_block(&$variables) {
  $variables['classes_array'][] = 'clearfix';
}

/**
 * Preprocess Views exposed form
 */
function govcmstheme_bootstrap_preprocess_views_exposed_form(&$vars, $hook) {

  if (strrpos($vars['form']['#id'], 'views-exposed-form', -strlen($vars['form']['#id'])) !== FALSE) {
    $vars['form']['submit']['#attributes']['class'] = array('btn btn-info');
    $vars['form']['submit']['#value'] = "Filter";
    $vars['form']['reset']['#attributes']['class'] = array('btn btn-info');
    unset($vars['form']['submit']['#printed']);
    unset($vars['form']['reset']['#printed']);
    $vars['button'] = drupal_render($vars['form']['submit']);
    $vars['reset_button'] = drupal_render($vars['form']['reset']);
  }
}


function govcmstheme_bootstrap_js_alter(&$javascript) {
  $node_admin_paths = array(
    'node/*/edit',
    'node/add',
    'node/add/*',
  );
  $replace_jquery = TRUE;
  if (path_is_admin(current_path())) {
    $replace_jquery = FALSE;
  }
  else {
    foreach ($node_admin_paths as $node_admin_path) {
      if (drupal_match_path(current_path(), $node_admin_path)) {
        $replace_jquery = FALSE;
      }
    }
  }
  // Swap out jQuery to use an updated version of the library.
  if ($replace_jquery) {
    $javascript['misc/jquery.js']['data'] = '//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js';
  }
}

function govcmstheme_bootstrap_html_tag($vars) {
  if ($vars['element']['#tag'] == 'script') {
    unset($vars['element']['#value_prefix']);
    unset($vars['element']['#value_suffix']);
  }
  return theme_html_tag($vars);
}

function govcmstheme_bootstrap_menu_tree__main_menu($variables) {
  return '<ul class="nav nav-pills pull-right">' . $variables['tree'] . '</ul>';
}

function govcmstheme_bootstrap_menu_link__main_menu($variables) {
  //unset all the classes
  if (!empty($element['#attributes']['class'])) {
    foreach ($element['#attributes']['class'] as $key => $class) {
      if ($class != 'active') {
        unset($element['#attributes']['class'][$key]);
      }
    }
  }

  $element = $variables['element'];
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . "</li>\n";
}

function govcmstheme_bootstrap_menu_tree__menu_footer_sub_menu($variables) {
  return '<ul class="list-inline small-links">' . $variables['tree'] . '</ul>';
}


function govcmstheme_bootstrap_form_alter(&$form, &$form_state, $form_id) {
  if (!empty($form['actions']) && $form['actions']['submit']) {
    $form['actions']['submit']['#attributes'] = array('class' => array('btn', 'btn-primary'));
    if(isset($form_id) && ($form_id == 'webform_client_form_126' || $form_id == 'webform_client_form_131')) {
      $form['actions']['submit']['#suffix'] = '<br /><small>Please do not include any unnecessary personal, financial, or sensitive information.  Information will only be used for purposes for which you provide it. Please see our <a href="/privacy">Privacy Policy</a> for further information.</small>';
    }

    if(isset($form_id) && $form_id == 'webform_client_form_466') {
      $form['actions']['submit']['#value'] = 'Start my site';
    }

  }

  //URLS:
  // Email Confirmed (): /easybake-email-confirmed
  // Baker Url (ezbake_baker_url): https://baker.govcms.gov.au
  // Verification Required (ezbake_confirm_url): /easybake-verification-required
  // Verification Error (ezbake_error_url): /easybake-verification-error
  // Check if we are dealing with Easy Bake webform
  $is_node = array_key_exists('#node', $form);
  $is_webform = $is_node && $form['#node']->type == "webform";
  $is_easybake_form = $is_webform && $form['#node']->machine_name == "EasyBake";
  if ($is_easybake_form) {
    // In case of AJAX call we need to add values Drupal.settings
    _push_ezbake_settings_to_js($form);
    // displays a drupal error if there is a GET param for error
    // and fill in form with values
    // @see https://govcms.atlassian.net/wiki/display/EZB/Baker+API for response types
    $query_params = drupal_get_query_parameters();
    if (!empty($query_params['error'])) {
      $error_msg = "Error: " . $query_params['error'];
      // read details (input as dot notation, e.g, details.message
      // but . is replaced by _ in PHP)
      if (!empty($query_params['details_message'])) {
        $error_msg .= "<br>Details: " . $query_params['details_message'];
      }
      // fill in the form fields
      $fields = array('contact_name', 'contact_email', 'phone_number', 'site_name', 'agency_name', 'website_purpose');
      foreach ($fields as $field) {
        $query_field_name = "details_form_values_" . $field;
        if (!empty($query_params[$query_field_name])) {
          $form['submitted'][$field]['#default_value'] = $query_params[$query_field_name];
        }
      }
      if (!empty($query_params['details_field'])) {
        form_set_error($query_params['details_field'], $error_msg);
      }
      else {
        drupal_set_message($error_msg, 'error');
      }
    }
    $form['#attributes']['name'] = 'easybake-order-form';
    $form['submitted']['#tree'] = False;
    $form['#action'] = variable_get('ezbake_baker_url') . '/order/submit?redirect=true';
  }
}

function govcmstheme_bootstrap_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
    $crumbs = '';
    foreach($breadcrumb as $value) {
      $temp = substr($value, 0, strpos($value, '>') + 1);
      $temp .= "← Back to ";
      $temp .= get_between('>', '</', $value);
      $temp .= " page</a>";
      $crumbs .= $temp;
    }
  }
  return $crumbs;
}

function get_between($var1="",$var2="",$pool){
  $temp1 = strpos($pool,$var1)+strlen($var1);
  $result = substr($pool,$temp1,strlen($pool));
  $dd=strpos($result,$var2);
  if($dd == 0){
    $dd = strlen($result);
  }

  return substr($result,0,$dd);
}

/**
 * Returns HTML for a date element formatted as an interval.
 */
function govcmstheme_bootstrap_display_interval($variables) {
  $entity = $variables['entity'];
  $options = $variables['display']['settings'];
  $dates = $variables['dates'];
  $attributes = $variables['attributes'];

  // Get the formatter settings, either the default settings for this node type
  // or the View settings stored in $entity->date_info.
  if (!empty($entity->date_info) && !empty($entity->date_info->formatter_settings)) {
    $options = $entity->date_info->formatter_settings;
  }

  $time_ago_vars = array(
    'start_date' => $dates['value']['local']['object'],
    'end_date' => $dates['value2']['local']['object'],
    'interval' => $options['interval'],
    'interval_display' => $options['interval_display'],
  );

  if ($return = theme('date_time_ago', $time_ago_vars)) {
    $return = get_between(">", "</", $return);
    return '<p class="post-meta"' . drupal_attributes($attributes) . ">$return</p>";
  }
  else {
    return '';
  }
}

/**
 * Implements hook_form_alter().
 */
function govcmstheme_bootstrap_form_search_api_page_search_form_alter(&$form, &$form_state, $form_id) {
  //Add CSS to form tag
  if (!isset($form['#attributes']['class'])) {
    $form['#attributes'] = array('class' => array('form-inline', 'navbar-form', 'search-form', 'move-into-top'));
  }
  else {
    $form['#attributes']['class'][] = 'form-inline';
    $form['#attributes']['class'][] = 'navbar-form';
    $form['#attributes']['class'][] = 'search-form';
    $form['#attributes']['class'][] = 'move-into-top';
  }

  //Hide label.. can't add classes directly to label so add span inside label... hackery
  $form['form']['keys_1']['#title'] = '<span class="sr-only">Search</span>';
  $form['form']['keys_1']['#theme_wrappers'] = array();
  unset($form['form']['keys_1']['#size']);

  //Add classes to input field
  $form['form']['keys_1']['#attributes']['class'][] = 'form-control';
  $form['form']['keys_1']['#attributes']['class'][] = 'input-lg';

  $form['form']['submit_1']['#attributes']['class'][] = 'sr-only';

  $form['form']['submit_2'] = array(
    '#type' => 'item',
    '#markup' => '<button type="submit" id="edit-submit-2" name="op" value="Search" class="form-submit input-group-addon btn-lg"><i class="icon-search"></i><span class="sr-only">Search</span></button>',
    '#weight' => 1000,
    '#theme_wrappers' => array(),
  );
  $form['form']['submit_1']['#theme_wrappers'] = array();

  $form['form']['#prefix'] = '<div class="input-group">';
  $form['form']['#suffix'] = '</div>';
}

// Remove Height and Width Inline Styles from Drupal Images
function govcmstheme_bootstrap_preprocess_image(&$variables) {
  foreach (array('width', 'height') as $key) {
    unset($variables[$key]);
  }
}


// Stop Drupal's meddling CSS loading
function govcmstheme_bootstrap_css_alter(&$css) {
  unset($css[drupal_get_path('module','system').'/system.theme.css']);
}

/*
 * Helper function to construct settings array to be passed to Drupal.settings
 * in order to execute and AJAX call
 *
 * @form
 * EasyBake webform
 */
function _push_ezbake_settings_to_js(&$form) {
  // Initialise JS settings array
  $ezbake_settings = array();
  // Initialise flag to disable submit button if needed
  $disable_submit = FALSE;
  // Get the Baker URL
  $baker_url = variable_get('ezbake_baker_url');
  if (!$baker_url) {
    if (user_access('administer site configuration')) {
      $msg = "The Baker URL variable (ezbake_baker_url) is not set for this form. Form submission will not work.";
    }
    else {
      $msg = "This form cannot be submitted at the moment. Please contact site administrator for more information.";
    }
    drupal_set_message($msg, 'error');
    $disable_submit = TRUE;
  }
  else {
    $ezbake_settings['bakerURL'] = $baker_url;
  }
  // Get the confirmation page URL
  $confirmation_page_url = variable_get('ezbake_confirm_url');
  if (!$confirmation_page_url) {
    if (user_access('administer site configuration')) {
      $msg = "The confirmation page URL (ezbake_confirm_url) has not been set. Responses from the baker might not work properly.";
      drupal_set_message($msg, 'warning');
    }
  }
  else {
    $ezbake_settings['confirmPageURL'] = $confirmation_page_url;
  }
  // Get the error page URL
  $error_page_url = variable_get('ezbake_error_url');
  if (!$error_page_url) {
    if (user_access('administer site configuration')) {
      $msg = "The error page URL (ezbake_error_url) has not been set. Responses from the baker might not work properly.";
      drupal_set_message($msg, 'warning');
    }
  }
  else {
    $ezbake_settings['errorPageURL'] = $error_page_url;
  }
  if ($disable_submit) {
    $form['actions']['submit']['#disabled'] = TRUE;
  }
  if (!empty($ezbake_settings)) {
    // Push settings to JS
    drupal_add_js(array('ezBake' => $ezbake_settings), 'setting');
  }
}
