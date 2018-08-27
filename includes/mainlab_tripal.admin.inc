<?php
/**
 * Launch page for mainlab_tripal administration.
 *
 */
function mainlab_tripal_admin_form() {
  // Get existing settings
  $enabled_themes = variable_get('mainlab_tripal_enabled_themes', array());

  $form = array();
  
  $form ['group'] = array (
    '#type' => 'fieldset',
    '#collapsible' => TRUE,
    '#title' => 'Mainlab Themes',
    '#description' => 'Check to enable the display.'
  );

  // Get available templates
  $templates = mainlab_tripal_get_templates ();
  uksort($templates, 'strcasecmp');
  $override_folders = array('templates' => 'Use default and do not override');

  // Parse the template file name
  foreach ($templates AS $key => $file) {
    if (!key_exists ($file->folder, $form['group'])) {
      if (preg_match('/^templates\//', $file->folder)) {
        $form ['group'][$file->folder] = array (
          '#type' => 'checkbox',
          '#title' => ucwords(str_replace(array('_', '/', 'templates'), array(' ', ' ', ''), $file->folder)),
          '#default_value' => key_exists(str_replace('/', '--', $file->folder), $enabled_themes) ? $enabled_themes[str_replace('/', '--', $file->folder)] : 0,
        );
      }
      else {
        $override_folders[$file->folder] = $file->folder;
      }
    }
  }
  
  // Folder to override theme templates
  $form ['override'] = array (
    '#type' => 'fieldset',
    '#collapsible' => TRUE,
    '#title' => 'Override Default Templates',
    '#description' => 'Select a folder to search for templates. Default templates will 
                                    be overridden by the counterpart (i.e. same name) in the folder you  
                                    selected. If you add a new template, save the settings again so theme 
                                    cache will be cleared and Drupal can pick up the change.'
  );
  $override = variable_get('mainlab_tripal_override_folder', 'templates');
  $form['override']['override_folder'] = array (
    '#type' => 'select',
    '#options' => $override_folders,
    '#default_value' => $override
  );
  
  // Advanced settings
  $form ['advanced'] = array (
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#title' => 'Advanced Settings',
      '#description' => 'More settings for the page display.'
  );  
  $cmap = variable_get('mainlab_tripal_cmap_links', 1);
  $form['advanced']['cmap_links'] = array (
      '#type' => 'checkbox',
      '#title' => 'Show CMap links on Featuremap, Genetic Marker, QTL, and MTL (heritable_phenotypic_marker) pages',
      '#default_value' => $cmap
  );
  return system_settings_form($form);
}

/**
 * Launch page for mainlab_tripal administration.
 *
 */
function mainlab_tripal_admin_form_validate($form, &$form_state) {
  $values = $form_state['values'];
  // override template folder
  variable_set('mainlab_tripal_override_folder', $values['override_folder']);
  // display of cmap links
  variable_set('mainlab_tripal_cmap_links', $values['cmap_links']);
  
  $enabled_themes = array();
  foreach ($values AS $key => $val) {
    if (preg_match('/^templates\//', $key)) {
      $enabled_themes[str_replace('/', '--', $key)] = $val;
    }
  }
  variable_set('mainlab_tripal_enabled_themes', $enabled_themes);

  // Get available templates
  $templates = mainlab_tripal_get_templates ();
  $enabled_templates = array();
  $high_priority = array();
  foreach ($templates AS $key => $file) {
    $folder = str_replace('/', '--', $file->folder);
    // Enable all templates in the overriding folder
    if ($file->folder == $values['override_folder']) {
      $high_priority[$file->name] = TRUE;
      $enabled_templates[$key] = $file->name;
    // Enable templates for enabled themes
    } else if (preg_match('/^templates--/', $folder) && $enabled_themes[$folder]) {
      $enabled_templates[$key] = $file->name;
    }
  }
  
  // Set high priority templates to override the default template
  foreach ($enabled_templates AS $key => $file) {
    if (preg_match('/^mainlab_tripal--templates\//', $key)  && key_exists($file, $high_priority)) {
      unset($enabled_templates[$key]);
    }
  }
  variable_set('mainlab_tripal_enabled_templates', $enabled_templates);
  drupal_theme_rebuild();
}
