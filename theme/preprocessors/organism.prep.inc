<?php
function mainlab_tripal_preprocess_mainlab_organism_base(&$variables) {
  $node = $variables['node'];
  if (!db_table_exists('chado.organism_relationship')) {
    return;
  }
  // we want to provide a new variable that contains the matched organisms.
  $organism = $node->organism;
  
  $sid = db_field_exists('chado.organism_relationship', 'subject_organism_id') ? 'subject_organism_id' : 'subject_id';
  $oid = db_field_exists('chado.organism_relationship', 'object_organism_id') ? 'object_organism_id' : 'object_id';

  // normally we would use chado_expand_vars to expand our
  // organism object and add in the relationships, however whan a large
  // number of relationships are present this significantly slows the
  // query, therefore we will manually perform the query
  $sql = "
    SELECT O.genus, O.species, O.organism_id, CO.nid, CVT.name as rel_type
    FROM {organism_relationship} ORel
      INNER JOIN {organism} O on ORel.$oid = O.organism_id
      INNER JOIN {cvterm} CVT on ORel.type_id = CVT.cvterm_id
      LEFT JOIN chado_organism CO on O.organism_id = CO.organism_id
    WHERE ORel.$sid = :organism_id
  ";
  $as_subject = chado_query($sql,array(':organism_id' => $organism->organism_id));
  $sql = "
    SELECT O.genus, O.species, O.organism_id, CO.nid, CVT.name as rel_type
    FROM {organism_relationship} ORel
      INNER JOIN {organism} O on ORel.$sid = O.organism_id
      INNER JOIN {cvterm} CVT on ORel.type_id = CVT.cvterm_id
      LEFT JOIN chado_organism CO on O.organism_id = CO.organism_id
    WHERE ORel.$oid = :organism_id
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

/**
 * Implements preprocess hook for the tripal_organism_unigenes template.
 */
function mainlab_tripal_preprocess_mainlab_organism_unigenes(&$variables) {
  $node = $variables['node'];
  $organism = $node->organism;
  
  // get information about this assemblies and add it to the items in this node
  $sql = "
     SELECT *
     FROM {organism_unigene_mview} OUM
       INNER JOIN {analysis} A  ON A.analysis_id = OUM.analysis_id
     WHERE OUM.organism_id = :organism_id
     ORDER BY A.timeexecuted DESC
   ";
  $results = chado_query($sql, array(':organism_id' => $organism->organism_id));
  
  $unigenes = array();
  $i=0;
  $sql = "SELECT nid FROM {chado_analysis} WHERE analysis_id = :analysis_id";
  while ($unigene = $results->fetchObject()) {
    $analysis_id = $unigene->analysis_id;
    $c_node = db_query($sql, array(':analysis_id' => $analysis_id))->fetchObject();
    if ($c_node) {
      $unigene->nid = $c_node->nid;
    }
    // add in the properties
    $unigene_name = chado_get_property(array('table' => 'analysis', 'id' => $analysis_id), array('type_name' => 'analysis_unigene_name', 'cv_name' => 'tripal'));
    $num_contigs  = chado_get_property(array('table' => 'analysis', 'id' => $analysis_id), array('type_name' => 'analysis_unigene_num_contigs', 'cv_name' => 'tripal'));
    $num_reads    = chado_get_property(array('table' => 'analysis', 'id' => $analysis_id), array('type_name' => 'analysis_unigene_num_reads', 'cv_name' => 'tripal'));
    $num_clusters = chado_get_property(array('table' => 'analysis', 'id' => $analysis_id), array('type_name' => 'analysis_unigene_num_clusters', 'cv_name' => 'tripal'));
    $num_singlets = chado_get_property(array('table' => 'analysis', 'id' => $analysis_id), array('type_name' => 'analysis_unigene_num_singlets', 'cv_name' => 'tripal'));
    
    $unigene->unigene_name = is_object($unigene_name) ? $unigene_name->value : '';
    $unigene->num_reads = is_object($num_reads) ? $num_reads->value : '';
    $unigene->num_clusters = is_object($num_clusters) ? $num_clusters->value : '';
    $unigene->num_contigs = is_object($num_contigs) ? $num_contigs->value : '';
    $unigene->num_singlets = is_object($num_singlets) ? $num_singlets->value : '';
    
    $unigenes[$i++] = $unigene;
  }
  $organism_unigene = new stdClass();
  $organism_unigene->unigenes = $unigenes;
  $node->organism->tripal_analysis_unigene = $organism_unigene;
}