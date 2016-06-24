<?php
function mainlab_tripal_preprocess_mainlab_allele(&$variables) {
  $marker = $variables['marker'];
  $oid = $variables['organism_id'];
  $alleleid = $variables['allele_id'];
  $project = $variables['project'];
  $stock_uniquename = $variables['stock_uniquename'];
  $species = $variables['species'];
  // Find the genotypes for this marker_allele
  $sql = "SELECT
                  G.genotype_id
                FROM {genotype} G
                INNER JOIN {feature_genotype} FG ON G.genotype_id = FG.genotype_id
                INNER JOIN {nd_experiment_genotype} NEG ON G.genotype_id = NEG.genotype_id
                WHERE feature_id = 
                (SELECT feature_id 
                 FROM {feature} 
                 WHERE uniquename = :uniquename AND type_id =
                    (SELECT cvterm_id FROM {cvterm} WHERE name = 'genetic_marker')
                 AND organism_id = :organism_id
                )
             AND string_to_array(description, '|') @> ARRAY[:allele_id]
                GROUP BY G.genotype_id
                ORDER BY G.genotype_id";
  $results = chado_query($sql, array(':uniquename' => $marker, ':organism_id' => $oid, 'allele_id' => $alleleid));
  
  $genotypes  = array();
  $counter = 0;
  while ($g = $results->fetchObject()) {
    $genotypes[$counter] = $g->genotype_id;
    $counter ++;
  }
  $stocks = array();
  foreach($genotypes AS $gtype) {
    $stock = mainlab_get_allele_associated_stocks($gtype, $project, $stock_uniquename, $species);
     $stocks = array_merge($stocks, $stock);
  }
  $allele->stock = $stocks;
  
  // Get  the genotype name
  $allele->name = $marker;
  $allele->id = $alleleid;
  $variables['node'] = $allele;
}

function mainlab_get_allele_associated_stocks ($genotype_id, $project = 0, $stock_uniquename = 0, $species = 0) {
  // Find out the stocks associated with this genotype (allele)
  $ssql = "SELECT DISTINCT S.stock_id, S.uniquename, CP.nid AS project_nid, P.name AS project
                    FROM {nd_experiment_genotype} NDG
                    INNER JOIN {nd_experiment_stock} NDS ON NDG.nd_experiment_id = NDS.nd_experiment_id
                    INNER JOIN {stock} EXP ON EXP.stock_id = NDS.stock_id
                    INNER JOIN {stock_relationship} SR ON SR.subject_id = EXP.stock_id
                    INNER JOIN {stock} S ON S.stock_id = SR.object_id
              INNER JOIN {nd_experiment_project} NEP ON NEP.nd_experiment_id = NDG.nd_experiment_id
                    LEFT JOIN {project} P ON P.project_id = NEP.project_id
                    LEFT JOIN public.chado_project CP ON P.project_id = CP.project_id
              LEFT JOIN (SELECT * FROM {stockprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'permission' AND cv_id = (SELECT cv_id FROM {cv} WHERE name ='MAIN'))) SP ON S.stock_id = SP.stock_id
              LEFT JOIN (SELECT * FROM {projectprop} PP WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'project_type' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))) PTYPE ON PTYPE.project_id = P.project_id 
              LEFT JOIN (SELECT * FROM {projectprop} PP WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'sub_type' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))) SUBTYPE ON SUBTYPE.project_id = P.project_id
              LEFT JOIN (SELECT * FROM {projectprop} PP WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'permission' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))) PERM ON PERM.project_id = P.project_id 
                    WHERE SR.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
                    AND 
                        PTYPE.value = 'genotyping'
                    AND
                        SUBTYPE.value = 'SSR'
                    AND
                        PERM.value = 'public'
                    AND genotype_id = :genotype_id";
  if ($project) {$ssql .= " AND P.name = '$project'";}
  if ($stock_uniquename) {$ssql .= " AND S.uniquename = '$stock_uniquename'";}
  if ($species) {$ssql .= " AND (SELECT genus || ' ' || species FROM {organism} WHERE organism_id =S.organism_id) = '$species'";}
  $sresult = chado_query($ssql, array(':genotype_id' => $genotype_id));
  $stocks = array();
  $scounter = 0;
  while ($stock = $sresult->fetchObject()) {
    // Find out the stock nid if there is one
    if (db_table_exists('chado_stock')) {
      $nsql = "SELECT nid FROM {chado_stock} WHERE stock_id = :stock_id";
      $nid = db_query($nsql, array(':stock_id' => $stock->stock_id))->fetchField();
      $stock->nid = $nid;
    }
    $stocks [$scounter] = $stock;
    $scounter ++;
  }
  return $stocks;
}
