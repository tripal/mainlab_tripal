<?php
$contact = $variables['node']->contact; ?>

<div class="tripal_contact-data-block-desc tripal-data-block-desc"></div> <?php

// Properties
$contact = chado_expand_var($contact, 'table', 'contactprop', array('return_array' => 1));
$props = $contact->contactprop;
$properties = new stdClass();
foreach ($props AS $prop) {
  $term = $prop->type_id->name;
  $value = $prop->value;
  $properties->$term = $value;
}

$contact = chado_expand_var($contact, 'table', 'project_contact', array('return_array' => 1));
$project_contact = $contact->project_contact;

$headers = array();
$rows = array();
// Contact Name row
$rows[] = array(
  array(
    'data' => 'Name',
    'header' => TRUE,
    'width' => '20%',
  ),
  $contact->name,
);

// Contact Properties
if (isset($properties->institution)) {
  $rows[] = array(
  array(
  'data' => 'Institution',
  'header' => TRUE
  ),
  $properties->institution,
  );
}
if (isset($properties->country)) {
  $rows[] = array(
    array(
      'data' => 'Country',
      'header' => TRUE
    ),
    $properties->country,
  );
}
if (isset($properties->email)) {
  $rows[] = array(
    array(
      'data' => 'Email',
      'header' => TRUE
    ),
    $properties->email,
  );
}
if (isset($properties->phone)) {
  $rows[] = array(
    array(
      'data' => 'Phone',
      'header' => TRUE
    ),
    $properties->phone,
  );
}
if (isset($properties->address)) {
  $rows[] = array(
    array(
      'data' => 'Address',
      'header' => TRUE
    ),
    $properties->address,
  );
}
if (isset($properties->keywords)) {
  $rows[] = array(
    array(
      'data' => 'Research Interest',
      'header' => TRUE
    ),
    $properties->keywords,
  );
}
if (isset($properties->last_update)) {
  $rows[] = array(
    array(
      'data' => 'Last Update',
      'header' => TRUE
    ),
    $properties->last_update,
  );
}
if ($project_contact) {
  $pdisplay = '';
  foreach ($project_contact AS $pc) {
    $project = $pc->project_id;
    $pid = $project->project_id;
    $pname = $project->name;
    $link = mainlab_tripal_link_record('project', $pid);
    if ($link) {
      $pdisplay .= "<a href=\"" .$link . "\">" . $pname . "</a><br>";
    }
    else {
      $pdisplay .= $pname . "<br>";
    }
  }
  $rows[] = array(
    array(
      'data' => 'Project',
      'header' => TRUE
    ),
    $pdisplay,
  );
}

// allow site admins to see the contact ID
if (user_access('view ids')) {
  // Pub ID
  $rows[] = array(
    array(
      'data' => 'Contact ID',
      'header' => TRUE,
      'class' => 'tripal-site-admin-only-table-row',
    ),
    array(
      'data' => $contact->contact_id,
      'class' => 'tripal-site-admin-only-table-row',
    ),
  );
}

// the $table array contains the headers and rows array as well as other
// options for controlling the display of the table.  Additional
// documentation can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_contact-table-base',
    'class' => 'tripal-data-table'
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);

// once we have our table array structure defined, we call Drupal's theme_table()
// function to generate the table.
print theme_table($table);
?>
