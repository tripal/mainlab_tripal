<?php
/*
 * Create a polymorphism page that lists all genotypes (i.e. polymorphisms) by using a marker's feature_id (i.e. $feature_id)
 */
function mainlab_show_polymorphism ($feature_id) {
	
	// Get  the marker name and nid if there is one
	$mname = db_result(chado_query("SELECT uniquename FROM {feature} WHERE feature_id = %d", $feature_id));
	$mnid = NULL;
	if (db_table_exists('chado_feature')) {
		$mnid = db_result(db_query("SELECT nid FROM {chado_feature} WHERE feature_id = %d", $feature_id));
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
                WHERE feature_id = %d
                GROUP BY G.genotype_id
                ORDER BY G.genotype_id";
	$result = chado_query($sql, $feature_id);
 
	$counter = 0;
	while ($poly = db_fetch_object($result)) {
		$poly->marker_name = $mname;
		$poly->marker_nid = $mnid;
		// Find out the stocks associated with this genotype. However, we only want to show 1 germplasm on the polymorphism page (SQL FIRST() function)
		$ssql = "SELECT count(*) AS num_stocks, first(S.stock_id) AS first_stock_id, first(S.uniquename) AS first_stock_name
                     FROM {nd_experiment_genotype} NDG 
                     INNER JOIN {nd_experiment_stock} NDS ON NDG.nd_experiment_id = NDS.nd_experiment_id
                     INNER JOIN {stock} EXP ON EXP.stock_id = NDS.stock_id
                     INNER JOIN {stock_relationship} SR ON SR.subject_id = EXP.stock_id
                     INNER JOIN {stock} S ON S.stock_id = SR.object_id
                     WHERE 
                       SR.type_id = 
                         (SELECT cvterm_id FROM cvterm WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM cv WHERE name = 'MAIN'))
                     AND genotype_id = %d GROUP BY genotype_id";
		$stock = db_fetch_object(chado_query($ssql, $poly->genotype_id));
   	// Find out the stock nid if there is one
		if (db_table_exists('chado_stock')) {
		   $nsql = "SELECT nid FROM chado_stock WHERE stock_id = %d";
		   $nid = db_result(db_query($nsql, $stock->first_stock_id));
		   $poly->first_stock_nid = $nid;
		}
		$poly->first_stock_id = $stock->first_stock_id;
		$poly->first_stock_name = $stock->first_stock_name;
		$poly->num_stocks = $stock->num_stocks;
		$polymorphisms [$counter] = $poly;
		$counter ++;
	}
	
	// Reorder alleles. We want to show 'Allele' instead of the real 'Genotype' separated by a bar '|', So we split the allleles and combine the list
	$hashed_poly = array(); // hash the allele by key
	if ($polymorphisms) {
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
					$hashed_poly [$key] = $new_p;
				}
			}
		}
	}
	ksort($hashed_poly);
	return theme('mainlab_polymorphism', $hashed_poly);
}