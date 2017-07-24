<?php
/*
 * Create a page that lists all features filtering by the ogramisn (i.e. $organism in 'genus-species' format), 
 * or the feature type (i.e. $type), or the germplasm name ($stock). All these variables are optional if the passed
 * in value = '_' (i.e. underscore). P.S. This function requires tripal_feature module
*/
function mainlab_gene_class_page ($symbol = '') {
  if ($symbol == '') {
    drupal_goto('gene_class_listing');
  }
  $output = "";
  $output .= "<p>Genes with symbol <strong>$symbol</strong>.</p>";
  
  $sql = 
  "SELECT 
      F.feature_id,
      (SELECT nid FROM chado_feature WHERE feature_id = GENE.feature_id) AS nid,
      GENE.uniquename,
      (SELECT genus || ' ' || species FROM organism WHERE organism_id = F.organism_id) AS organism,
      string_agg(C.name, '; ') AS contact
      FROM {feature} F
      INNER JOIN {feature_relationship} FR ON F.feature_id = subject_id
      INNER JOIN {feature} GENE ON GENE.feature_id = object_id 
      LEFT JOIN {feature_contact} FC ON F.feature_id = FC.feature_id
      LEFT JOIN {contact} C ON C.contact_id = FC.contact_id 
        WHERE F.uniquename = :symbol 
        AND F.type_id = 
        (SELECT cvterm_id FROM {cvterm} 
        WHERE name = 'gene_class' 
        AND cv_id = 
        (SELECT cv_id FROM {cv} 
        WHERE name = 'sequence'))
         GROUP BY feature_id,nid,GENE.uniquename, organism
         ORDER BY organism, GENE.uniquename";
  
  $result = chado_query($sql, array(':symbol' => $symbol));
  $rows = array();
  $row_count = 0;
  while ($row = $result->fetchObject()) {
    $class = '';
    if ($row_count % 2 == 0) {
      $class = array('tripal-table-even-row');
    } else {
      $class = array('tripal-table-odd-row');
    }
    $link = mainlab_tripal_link_record('feature', $row->feature_id);
    $rows[] = $link ? array('data' => array("<a href=\"$link\">" . $row->uniquename . "</a>", $row->organism, $row->contact, ), 'class' =>$class) : array('data' => array($row->uniquename, $row->organism, $row->contact, ), 'class' =>$class);
    $row_count ++;
  }
  $header = array('Name', 'Organism', 'Contact');
  $attribute = array('class' => 'tripal-table tripal-table-horz');
  $table = array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => $attribute,
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  $output .= theme_table($table);
  $output .= "<br>";
  
  return $output;
}
