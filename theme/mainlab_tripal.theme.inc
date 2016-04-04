<?php 
$preprocessors = mainlab_tripal_get_preprocessors();

// If the override folder contains the preprocessor with the same name, use it
// instead of the default
$override = variable_get('mainlab_tripal_override_folder', 'templates');
$theme_path = drupal_get_path('module', 'mainlab_tripal') . '/theme/';
$prep_path = $theme_path;
if ($override != 'templates') {
  $prep_path .= $override;
}

foreach ($preprocessors AS $pp) {
  $pfile = $prep_path . '/' . $pp->filename;
  if (file_exists($pfile)) {
    require_once $pfile; // Override only if preprocessor file exists
  } else {
    require_once $theme_path . 'preprocessors/' . $pp->filename; // Use default preprocessor
  }
}


