<?php
function mainlab_tripal_preprocess_mainlab_allele(&$variables) {
  $marker = $variables['marker'];
  $oid = $variables['organism_id'];
  $alleleid = $variables['allele_id'];
  // Find the genotypes for this marker_allele
  $sql = "SELECT
                  G.genotype_id
                FROM {genotype} G
                INNER JOIN {feature_genotype} FG ON G.genotype_id = FG.genotype_id
                INNER JOIN {nd_experiment_genotype} NEG ON G.genotype_id = NEG.genotype_id
                WHERE feature_id =
			          (SELECT feature_id
			           FROM {feature}
			           WHERE lower(uniquename) = lower(:uniquename) AND type_id =
			              (SELECT cvterm_id FROM {cvterm} WHERE name = 'genetic_marker')
					   AND organism_id = :organism_id
			          )
			       AND string_to_array(description, '|') @> ARRAY[:allele_id]
                GROUP BY G.genotype_id
                ORDER BY G.genotype_id";
  $results = chado_query($sql, array(':uniquename' => $marker, ':organism_id' => $oid, ':allele_id' => $alleleid));
  
  $genotypes  = array();
  $counter = 0;
  while ($g = $results->fetchObject()) {
    $genotypes[$counter] = $g->genotype_id;
    $counter ++;
  }
  $stocks = array();
  foreach($genotypes AS $gtype) {
    $stock = mainlab_get_allele_associated_stocks($gtype);
    $stocks = array_merge($stocks, $stock);
  }
  $allele = new stdClass();
  $allele->stock = $stocks;
  
  // Get  the genotype name
  $allele->name = $marker;
  $allele->id = $alleleid;  
  $variables['node'] = $allele;
}

function mainlab_get_allele_associated_stocks ($genotype_id) {
  // Find out the stocks associated with this genotype (allele)
  $ssql = "SELECT DISTINCT S.stock_id, S.uniquename, DYE.value AS dye_color, P.name AS project
                    FROM {nd_experiment_genotype} NDG
                    INNER JOIN {nd_experiment_stock} NDS ON NDG.nd_experiment_id = NDS.nd_experiment_id
                    INNER JOIN {stock} EXP ON EXP.stock_id = NDS.stock_id
                    INNER JOIN {stock_relationship} SR ON SR.subject_id = EXP.stock_id
                    INNER JOIN {stock} S ON S.stock_id = SR.object_id
			           LEFT JOIN {nd_experiment_project} NEP ON NEP.nd_experiment_id = NDG.nd_experiment_id
                    LEFT JOIN {project} P ON P.project_id = NEP.project_id
			           LEFT JOIN
                      (SELECT nd_experiment_id, value FROM {nd_experimentprop} NEP
                       WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'dye_color')
                      )DYE ON DYE.nd_experiment_id = NDG.nd_experiment_id
                    WHERE
                      SR.type_id =
                        (SELECT cvterm_id FROM {cvterm} WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
                    AND genotype_id = :genotype_id";
  $sresult = chado_query($ssql, array(':genotype_id' => $genotype_id));
  $stocks = array();
  $scounter = 0;
  while ($stock = $sresult->fetchObject()) {
    // Find out the stock nid if there is one
    if (db_table_exists('chado_stock')) {
      $nsql = "SELECT nid FROM chado_stock WHERE stock_id = :stock_id";
      $nid = db_query($nsql, array(':stock_id' => $stock->stock_id))->fetchField();
      $stock->nid = $nid;
    }
    $stocks [$scounter] = $stock;
    $scounter ++;
  }
  return $stocks;
}