<?php
/*
 * Create a page that lists all features filtering by the ogramisn (i.e. $organism in 'genus-species' format), 
 * or the feature type (i.e. $type), or the germplasm name ($stock). All these variables are optional if the passed
 * in value = '_' (i.e. underscore). P.S. This function requires tripal_feature module
*/
function mainlab_feature_listing ($organism, $type, $stock) {
  $organism = check_plain($organism);
  $type = check_plain($type);
  $stock = check_plain($stock);
  $sql = "";
  $conditions = array();
  if ($organism != '_') {
    $fullname = explode("-", $organism);
    $genus = $fullname [0];
    $cond_q = " feature.organism_id IN (SELECT organism_id FROM {organism} WHERE genus = '$genus' ";
    if (count($fullname) > 1) {
      $species = $fullname [1];
      $cond_q .= "AND species = '$species'";
    }
    $cond_q .= ")";
    
    array_push($conditions, $cond_q);
  }
  if ($type != '_') {
    array_push($conditions, " feature.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = '$type' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))");
  }
  if ($stock != '_') {
    $sql .= " INNER JOIN {feature_stock} ON feature.feature_id = feature_stock.feature_id";
    array_push($conditions, " stock_id = (SELECT stock_id FROM {stock} WHERE uniquename = '$stock' AND name IS NOT NULL)");
  }
  
  if (count($conditions) > 0) {
    $sql .= " WHERE";
  }
  $counter = 0;
  foreach($conditions AS $con) {
    $sql .= $con;
    if ($counter != count($conditions) - 1) {
      $sql .= " AND";
    }
    $counter ++;
  }
  $qcount = "SELECT count(*) FROM {feature}" . $sql;
  $total = chado_query($qcount)->fetchField();
  $num_per_page = 20;
  $output = "<p>Total <strong>" . $total . "</strong> features.</p>";
  $q = "SELECT feature_id, uniquename, cvterm.name AS type, genus, species 
       FROM {feature} 
       INNER JOIN {cvterm} ON cvterm_id = type_id
       INNER JOIN {organism} O ON feature.organism_id = O.organism_id" . $sql;

  $result = chado_pager_query($q, array(), 20, 0, $qcount);
  $rows = array();
  $row_count = 0;
  $page = pager_default_initialize($total, $num_per_page);

  // Next, retrieve and display the items for the current page.
  $offset = $num_per_page * $page;
  while ($row = $result->fetchObject()) {
    $class = '';
    if ($row_count % 2 == 0) {
      $class = array('tripal-table-even-row');
    } else {
      $class = array('tripal-table-odd-row');
    }
    $link = mainlab_tripal_link_record('feature', $row->feature_id);
    if ($link) {
      $rows[] = array('data' => array($offset + $row_count + 1, "<a href=\"$link\">" . $row->uniquename . "</a>", $row->type, $row->genus . " " . $row->species)
                                 , 'class' =>$class);
    } else {
      $rows[] = array('data' => array($offset + $row_count + 1, $row->uniquename, $row->type, $row->genus . " " . $row->species)
                                 , 'class' =>$class);
    }
    $row_count ++;
  }
  $header = array('No', 'Uniquename', 'Type', 'Species');
  $attribute = array('class' => 'tripal-table tripal-table-horz');
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $table = array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => $attribute,
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  if ($total > 0) {
      $output .= theme_table($table);
      $output .= theme('pager');
  }
  return $output;
  
}