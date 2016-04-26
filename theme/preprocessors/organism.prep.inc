<?php
function mainlab_tripal_preprocess_mainlab_organism_base(&$variables) {
  $node = $variables['node'];
  if (!db_table_exists('chado.organism_relationship')) {
    return;
  }
  // we want to provide a new variable that contains the matched organisms.
  $organism = $node->organism;
  
  // normally we would use chado_expand_vars to expand our
  // organism object and add in the relationships, however whan a large
  // number of relationships are present this significantly slows the
  // query, therefore we will manually perform the query
  $sql = "
    SELECT O.genus, O.species, O.organism_id, CO.nid, CVT.name as rel_type
    FROM {organism_relationship} ORel
      INNER JOIN {organism} O on ORel.object_organism_id = O.organism_id
      INNER JOIN {cvterm} CVT on ORel.type_id = CVT.cvterm_id
      LEFT JOIN chado_organism CO on O.organism_id = CO.organism_id
    WHERE ORel.subject_organism_id = :organism_id
  ";
  $as_subject = chado_query($sql,array(':organism_id' => $organism->organism_id));
  $sql = "
    SELECT O.genus, O.species, O.organism_id, CO.nid, CVT.name as rel_type
    FROM {organism_relationship} ORel
      INNER JOIN {organism} O on ORel.subject_organism_id = O.organism_id
      INNER JOIN {cvterm} CVT on ORel.type_id = CVT.cvterm_id
      LEFT JOIN chado_organism CO on O.organism_id = CO.organism_id
    WHERE ORel.object_organism_id = :organism_id
  ";
  $as_object = chado_query($sql,array(':organism_id' => $organism->organism_id));
  
  // combine both object and subject relationshisp into a single array
  $relationships = array();
  $relationships['object'] = array();
  $relationships['subject'] = array();
  
  // iterate through the object relationships
  while ($relationship = $as_object->fetchObject()) {
  
    // get the relationship and child types
    $rel_type = t(preg_replace('/_/'," ",$relationship->rel_type));
  
    if (!array_key_exists($rel_type, $relationships['object'])) {
      $relationships['object'][$rel_type] = array();
    }
    $relationships['object'][$rel_type][] = $relationship;
  }
  
  while ($relationship = $as_subject->fetchObject()) {
  
    // get the relationship and child types
    $rel_type = t(preg_replace('/_/'," ",$relationship->rel_type));
  
    if (!array_key_exists($rel_type, $relationships['subject'])) {
      $relationships['subject'][$rel_type] = array();
    }
    $relationships['subject'][$rel_type][] = $relationship;
  }
  
  $organism->all_relationships = $relationships;
  $node->organism =$organism;
}