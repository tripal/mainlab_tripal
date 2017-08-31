<?php
$feature  = $variables['node']->feature;  ?>

<div class="tripal_feature-data-block-desc tripal-data-block-desc"></div> <?php
 
// the $headers array is an array of fields to use as the colum headers. 
// additional documentation can be found here 
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
// This table for the analysis has a vertical header (down the first column)
// so we do not provide headers here, but specify them in the $rows array below.
$headers = array();
$opt = array(
  'return_array' => 1,
  'include_fk' => array(
    'feature_id' => 1
  )
);
$feature = chado_expand_var($feature, 'table', 'feature_stock', $opt);
//$feature = chado_expand_var($feature, 'table', 'feature_pub', $opt);
$feature = chado_expand_var($feature, 'table', 'library_feature', $opt);

$feature_stock = isset($feature->feature_stock) ? $feature->feature_stock : NULL;
//$feature_pub = isset($feature->feature_pub) ? $feature->feature_pub : NULL;
$feature_lib = isset($feature->library_feature) ? $feature->library_feature : NULL;

// the $rows array contains an array of rows where each row is an array
// of values for each column of the table in that row.  Additional documentation
// can be found here:
// https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7 
$rows = array();

// Name row
$rows[] = array(
  array(
    'data' => 'NCBI Accession',
    'header' => TRUE,
    'width' => '20%',
  ),
  $feature->name
);
// Unique Name row
$rows[] = array(
  array(
    'data' => 'Version',
    'header' => TRUE
  ),
  "<a href=\"https://www.ncbi.nlm.nih.gov/nuccore/$feature->uniquename\" target=\"_blank\">$feature->uniquename</a>"
);
// Type row
$rows[] = array(
  array(
    'data' => 'Type',
    'header' => TRUE
  ),
  $feature->type_id->name
);
// Organism row
$organism = $feature->organism_id->genus ." " . $feature->organism_id->species ." (" . $feature->organism_id->common_name .")";
$link = mainlab_tripal_link_record('organism', $feature->organism_id->organism_id);
if ($link) {
  $organism = l("<i>" . $feature->organism_id->genus . " " . $feature->organism_id->species . "</i>", $link, array('html' => TRUE));
} 
$rows[] = array(
  array(
    'data' => 'Species',
    'header' => TRUE,
  ),
  $organism
);
if ($feature_lib) {
  $display_lib = '';
  foreach ($feature_lib AS $fl) {
    $lib = chado_generate_var('library', array('library_id' => $fl->library_id));
    $lname = $lib->name ? $lib->name : $lib->uniquename;
    $link = mainlab_tripal_link_record('library', $fl->library_id);
    if ($link) {
      $display_lib .= "<a href=\"$link\">" . $lname . '</a><br>';
    }
    else {
      $display_lib .= $lname . '<br>';
    }
  }
  $rows[] = array(
    array(
      'data' => 'Library',
      'header' => TRUE
    ),
    $display_lib
  );
}

if ($feature_stock) {
  $display_stock = '';
  foreach ($feature_stock AS $fs) {
    $stock = chado_generate_var('stock', array('stock_id' => $fs->stock_id));
    $sname = $stock->name ? $stock->name : $stock->uniquename;
    $link = mainlab_tripal_link_record('stock', $fs->stock_id);
    if ($link) {
      $display_stock .= "<a href=\"$link\">" . $sname . '</a><br>';
    }
    else {
      $display_stock .= $sname . '<br>';
    }
  }
  $rows[] = array(
    array(
      'data' => 'Stock',
      'header' => TRUE
    ),
    $display_stock
  );
}

$sql = 'SELECT count(*) FROM {feature_pub} FP WHERE feature_id = :feature_id';
$num_pubs = chado_query($sql, array(':feature_id' => $feature->feature_id))->fetchField();

if ($num_pubs) {
  $rows[] = array(
    array(
      'data' => 'Publication',
      'header' => TRUE
    ),
    "[<a href=\"?pane=publications\">view all</a>]"
  );
}

$sql = 
  "SELECT 
     F.feature_id,
     F.name,
     F.uniquename,
     (SELECT name FROM {cvterm} WHERE cvterm_id = F.type_id) AS type  
   FROM {feature_relationship} FR 
   INNER JOIN {feature} F ON FR.subject_id = F.feature_id
   WHERE object_id = :feature_id AND FR.type_id = 
   (SELECT cvterm_id FROM {cvterm} WHERE name = 'linked_to'
    AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'feature_property'))";
$feature_rel = chado_query($sql, array(':feature_id' => $feature->feature_id));
$gene = NULL;
$mrna = NULL;
$prot = NULL;
while ($rel = $feature_rel->fetchObject()) {
  if ($rel->type == 'gene') {
    $gene = $rel;
  }
  else if ($rel->type == 'mRNA') {
    $mrna = $rel;
  }
  else if ($rel->type == 'polypeptide') {
    $prot = $rel;
  }
}
if ($gene) {
  $link = mainlab_tripal_link_record('feature', $gene->feature_id);
  $rows[] = array(
    array(
      'data' => 'Gene',
      'header' => TRUE
    ),
    $link ? "<a href=\"$link\">$gene->name</a>" : $gene->name
  );
}

if ($mrna) {
  $link = mainlab_tripal_link_record('feature', $mrna->feature_id);
  $rows[] = array(
    array(
      'data' => 'mRNA',
      'header' => TRUE
    ),
    $link ? "<a href=\"$link\">$mrna->name</a>" : $mrna->name
  );
}

if ($prot) {
  $sql = 
    "SELECT 
       accession,
       db.urlprefix 
     FROM {feature_dbxref} FX 
     INNER JOIN {dbxref} X ON X.dbxref_id = FX.dbxref_id 
     INNER JOIN {db} ON db.db_id = X.db_id
     WHERE feature_id = :feature_id
     AND db.name = 'GI'";
  $pt = chado_query($sql, array(':feature_id' => $prot->feature_id))->fetchObject();
  $rows[] = array(
    array(
      'data' => 'Protein',
      'header' => TRUE
    ),
    "<a href=\"$pt->urlprefix$pt->accession\" target=\"_blank\">$pt->accession</a>"
  );
  $sql = 
    "SELECT value 
     FROM {featureprop} 
     WHERE type_id IN (SELECT cvterm_id FROM {cvterm} WHERE name = 'product') 
     AND feature_id = :feature_id";
  $product = chado_query($sql, array(':feature_id' => $prot->feature_id))->fetchField();
  $rows[] = array(
    array(
      'data' => 'Protein product',
      'header' => TRUE
    ),
    $product
  );
}

// allow site admins to see the feature ID
if (user_access('view ids')) { 
  // Feature ID
  $rows[] = array(
    array(
      'data' => 'Feature ID',
      'header' => TRUE,
      'class' => 'tripal-site-admin-only-table-row',
    ),
    array(
      'data' => $feature->feature_id,
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
    'id' => 'tripal_feature-table-base',
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
