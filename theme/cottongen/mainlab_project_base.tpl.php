<?php
$node = $variables['node'];
$project = $variables['node']->project;
$project = chado_expand_var($project, 'table', 'project_relationship', array('return_array' => 1));
$project = chado_expand_var($project,'table', 'project_contact', array('return_array' => 1));

// expand the project to include the properties.
$project = chado_expand_var($project,'table', 'projectprop', array('return_array' => 1));
$projectprops = $project->projectprop;
$properties = new stdClass();
foreach ($projectprops as $property) {
  $property = chado_expand_var($property,'field','projectprop.value');
  $type = $property->type_id->name;
  $properties->$type = $property->value;
}
$headers = array();
$rows = array();
// Contact Name row
$rows[] = array(
  array(
    'data' => 'Name',
    'header' => TRUE,
    'width' => '20%',
  ),
  $project->name,
);
$rows[] = array(
  array(
    'data' => 'Type',
    'header' => TRUE,
    'width' => '20%',
  ),
  $properties->project_type,
);
$rows[] = array(
  array(
    'data' => 'Description',
    'header' => TRUE,
    'width' => '20%',
  ),
  $properties->description ? $properties->description : 'n/a',
);

if ($project->project_contact) {
  $project_contact = $project->project_contact;
  $display_contact = '';
  foreach ($project_contact AS $pc) {
    $cid = $pc->contact_id->contact_id;
    $cname = $pc->contact_id->name;
    $link = mainlab_tripal_link_record('contact', $cid);
    if ($link) {
      $display_contact .= "<a href=\"$link\">" . $cname . "</a><br>";
    }
    else {
      $display_contact .= $cname . "<br>";
    }
  }
  $rows[] = array(
    array(
      'data' => 'Contact',
      'header' => TRUE,
      'width' => '20%',
    ),
    $display_contact,
  );
}

if (isset($project->project_relationship->subject_project_id)) {
  $superdata = $project->project_relationship->subject_project_id;
  $display_superdata = '';
  foreach ($superdata AS $super) {
    if ($super->type_id->name == 'is_a_subproject_of') {
      $superid = $super->object_project_id->project_id;
      $supername = $super->object_project_id->name;
      $link = mainlab_tripal_link_record('project', $superid);
      if ($link) {
        $display_superdata .= "<a href=\"$link\">" . $supername . "</a><br>";
      }
      else {
        $display_superdata .= $supername . "<br>";
      }
    }
  }
  $rows[] = array(
    array(
      'data' => 'Super Dataset',
      'header' => TRUE,
      'width' => '20%',
    ),
    $display_superdata,
  );
}

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
