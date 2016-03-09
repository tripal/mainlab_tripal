<?php
/**
 * Launch page for mainlab_tripal administration.
 *
 */
function mainlab_tripal_admin_form() {
  // Get existing settings
  $settings = variable_get('mainlab_tripal_enabled_templates', array());
  dpm($settings);

  $form = array();
  // Get available templates
  $templates = mainlab_tripal_get_templates ();

  // Parse the template file name
  foreach ($templates AS $key => $file) {
    if (! key_exists ($file->folder, $form)) {
      $form [$file->folder] = array (
        '#type' => 'fieldset',
        '#collapsible' => TRUE,
        '#title' => str_replace(array('_', '/'), array(' ', ' '), $file->folder),
        '#description' => t('Check to enable display:'),
      );
    }
    $form[$file->folder][$key] = array(
      '#type' => 'checkbox',
      '#title' => $file->name,
      '#default_value' => key_exists($key, $settings) ? $settings[$key] : 0,
    );
  }
  
  return system_settings_form($form);
}

/**
 * Launch page for mainlab_tripal administration.
 *
 */
function mainlab_tripal_admin_form_validate($form, &$form_state) {
  $values = $form_state['values'];
  $settings = array();
  foreach ($values AS $key => $val) {
    if (preg_match('/^mainlab_tripal--/', $key)) {
      $settings[$key] = $val;
    }
  }
  variable_set('mainlab_tripal_enabled_templates', $settings);
  drupal_theme_rebuild();
}