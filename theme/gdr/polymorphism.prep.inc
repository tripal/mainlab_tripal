<?php
function mainlab_tripal_preprocess_mainlab_polymorphism(&$variables) {
  $feature_id = $variables['feature_id'];
  // Get  the marker name and nid if there is one
  $mname = chado_query("SELECT uniquename FROM {feature} WHERE feature_id = :feature_id", array(':feature_id' => $feature_id))->fetchField();
  $moid = chado_query("SELECT organism_id FROM {feature} WHERE feature_id = :feature_id", array(':feature_id' => $feature_id))->fetchField();
  $mnid = NULL;
  if (db_table_exists('chado_feature')) {
    $mnid = db_query("SELECT nid FROM chado_feature WHERE feature_id = :feature_id", array(':feature_id' => $feature_id))->fetchField();
  }

  // Select all genotypes for the marker
  $sql = "SELECT 
                  min(NEG.nd_experiment_id) AS nd_experiment_id,
                  G.genotype_id, 
                  G.uniquename, 
               G.description,
                  min(P.name) AS project
                FROM {genotype} G 
                INNER JOIN {feature_genotype} FG ON G.genotype_id = FG.genotype_id 
                INNER JOIN {nd_experiment_genotype} NEG ON G.genotype_id = NEG.genotype_id
                INNER JOIN {nd_experiment_project} NEP ON NEP.nd_experiment_id = NEG.nd_experiment_id
                INNER JOIN {project} P ON P.project_id = NEP.project_id
          LEFT JOIN (SELECT * FROM projectprop WHERE type_id = (SELECT cvterm_id FROM cvterm WHERE name = 'project_type' AND cv_id = (SELECT cv_id FROM cv WHERE name ='MAIN'))) PP ON P.project_id = PP.project_id
                WHERE feature_id = :feature_id
          AND (PP.value = 'diversity' OR P.name LIKE '%_CRS%' OR (P.name LIKE 'Apple_KE%' AND P.name NOT LIKE 'Apple_KE_PRV_%'))
                GROUP BY G.genotype_id
                ORDER BY G.genotype_id";
  $result = chado_query($sql, array(':feature_id' => $feature_id));
 
  $counter = 0;
  $polymorphisms = array();
  while ($poly = $result->fetchObject()) {
    $poly->marker_name = $mname;
    $poly->marker_nid = $mnid;
    $poly->marker_oid = $moid;
    // Find out the stocks associated with this genotype. However, we only want to show 1 germplasm on the polymorphism page (SQL FIRST() function)
    $ssql = "SELECT count(*) AS num_stocks, first(S.stock_id) AS first_stock_id, first(S.uniquename) AS first_stock_name
                     FROM {nd_experiment_genotype} NDG 
                     INNER JOIN {nd_experiment_stock} NDS ON NDG.nd_experiment_id = NDS.nd_experiment_id
                     INNER JOIN {stock} EXP ON EXP.stock_id = NDS.stock_id
                     INNER JOIN {stock_relationship} SR ON SR.subject_id = EXP.stock_id
                     INNER JOIN {stock} S ON S.stock_id = SR.object_id
             LEFT JOIN (SELECT * FROM {stockprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'permission' AND cv_id = (SELECT cv_id FROM {cv} WHERE name ='MAIN'))) SP ON S.stock_id = SP.stock_id
                     WHERE 
                       SR.type_id = 
                         (SELECT cvterm_id FROM {cvterm} WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
           AND (SP.value <> 'PRIVATE' OR SP.value IS NULL)
                     AND genotype_id = :genotype_id GROUP BY genotype_id";
    $stock = chado_query($ssql, array(':genotype_id' => $poly->genotype_id))->fetchObject();
     // Find out the stock nid if there is one
    if (db_table_exists('chado_stock')) {
       $nsql = "SELECT nid FROM chado_stock WHERE stock_id = :stock_id";
       $nid = db_query($nsql, array(':stock_id' => $stock->first_stock_id))->fetchField();
       $poly->first_stock_nid = $nid;
    }
    $poly->first_stock_id = $stock->first_stock_id;
    $poly->first_stock_name = $stock->first_stock_name;
    $poly->num_stocks = $stock->num_stocks;
    if ($poly->num_stocks) {
      $polymorphisms [$counter] = $poly;
    }
    $counter ++;
  }
  
  // Reorder alleles. We want to show 'Allele' instead of the real 'Genotype' separated by a bar '|', So we split the allleles and combine the list
  $hashed_poly = array(); // hash the allele by key
  if (count($polymorphisms) > 0) {
    foreach ($polymorphisms AS $p) {
      $keys = explode('|', $p->description);

      foreach ($keys AS $key) {
        // If key already exists, concatednate 'genotype_id' and add up 'num_stocks'
        if (key_exists($key, $hashed_poly)) {
          $old_p = NULL;
          $old_p = $hashed_poly[$key];
          $old_p->genotype_id = $old_p->genotype_id . "+" . $p->genotype_id;
          $old_p->num_stocks = $old_p->num_stocks + $p->num_stocks;
          $old_p->uniquename = $p->marker_name . "_" . $key;
          $old_p->description = $key;
          if ($old_p->uniquename == $p->uniquename) {
            $old_p->first_stock_id = $p->first_stock_id;
            $old_p->first_stock_name = $p->first_stock_name;
            $old_p->first_stock_nid = $p->first_stock_nid;
            $old_p->project = $p->project;
          }
          $hashed_poly[$key] = $old_p;
        } else {
          $new_p = NULL;
          $new_p->uniquename = $p->marker_name . "_" . $key;
          $new_p->genotype_id = $p->genotype_id;
          $new_p->num_stocks = $p->num_stocks;
          $new_p->description = $key;
          $new_p->first_stock_id = $p->first_stock_id;
          $new_p->first_stock_name = $p->first_stock_name;
          $new_p->first_stock_nid = $p->first_stock_nid;
          $new_p->project = $p->project;
          $new_p->marker_name = $mname;
          $new_p->marker_nid = $mnid;
          $new_p->marker_oid = $moid;
          $hashed_poly [$key] = $new_p;
        }
      }
    }
  }
  ksort($hashed_poly);
  $variables['node'] = $hashed_poly;
}