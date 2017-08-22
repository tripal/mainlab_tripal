<?php
$contact = $variables['node']->contact; ?>

<div class="tripal_contact-data-block-desc tripal-data-block-desc"></div> <?php

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
// Contact Type row
$rows[] = array(
  array(
    'data' => 'Type',
    'header' => TRUE
  ),
  $contact->type_id->name,
);
$rows[] = array(
  array(
    'data' => 'Description',
    'header' => TRUE
  ),
  $contact->description,
);
// Properties
$contact = chado_expand_var($contact, 'table', 'contactprop', array('return_array' => 1));
$props = $contact->contactprop;
foreach ($props AS $prop) {
  $term = ucwords(str_replace('_', ' ', $prop->type_id->name));
  $value = $prop->value;
  $rows[] = array(
    array(
      'data' => $term,
      'header' => TRUE
    ),
    $value,
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
