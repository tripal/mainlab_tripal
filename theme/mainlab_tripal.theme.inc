<?php 
/**
 *
 *
 * @ingroup tripal_feature
 */
function tripal_feature_preprocess_tripal_feature_genetic_marker_image(&$variables) {
  $feature = $variables['node']->feature;
  // We want to show maps for this stock and maps of its children
  $results = chado_query(
      "SELECT eimage_type, image_uri, value as legend
			 FROM {feature_image} FI
			 INNER JOIN {eimage} I ON FI.eimage_id = I.eimage_id
			 INNER JOIN {eimageprop} IP ON I.eimage_id = IP.eimage_id
			 WHERE IP.type_id = (SELECT cvterm_id
			                                      FROM {cvterm}
			                                      WHERE name = 'legend'
			                                      AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
			                                    )
			AND feature_id = :feature_id",
      array (':feature_id' => $feature->feature_id));
  $images = NULL;
  $counter = 0;
  while ($img = $results->fetctObject()) {
    $images[$counter] = $img;
    $counter ++;
  }
  $feature->images = $images;
}

/**
 * @ingroup tripal_featuremap
 */
function tripal_featuremap_preprocess_tripal_featuremap_custom_base(&$variables) {
	$featuremap = $variables['node']->featuremap;
	$num_loci = chado_query(
			"SELECT count (distinct F.uniquename) 
			  FROM {featurepos} FP 
			  INNER JOIN {feature} F ON F.feature_id = FP.feature_id 
			  WHERE F.type_id = (SELECT cvterm_id 
			                                      FROM {cvterm} 
			                                      WHERE name = 'marker_locus' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence')) 
			  AND featuremap_id = :featuremap_id", array(':featuremap_id' => $featuremap->featuremap_id))->fetchField();
	$num_lg = chado_query(
			"SELECT count (distinct F.uniquename) 
			  FROM {featurepos} FP 
			  INNER JOIN {feature} F ON F.feature_id = FP.map_feature_id 
			  WHERE F.type_id = (SELECT cvterm_id 
			                                      FROM {cvterm} 
			                                      WHERE name = 'linkage_group' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence')) 
			  AND featuremap_id = :featuremap_id", array('featuremap_id' => $featuremap->featuremap_id))->fetchField();
	$cmap_r = chado_query(
			"SELECT db.urlprefix, X.accession 
			 FROM {featuremap_dbxref} FD
			 INNER JOIN {dbxref} X ON FD.dbxref_id = X.dbxref_id
			 INNER JOIN {db} ON db.db_id = X.db_id
			 WHERE featuremap_id = :featuremap_id
			", array(':featuremap_id' => $featuremap->featuremap_id));
	$cmap = $cmap_r->fetchObject();
	$cmap_url = is_object($cmap) ? $cmap->urlprefix . $cmap->accession : NULL;
	
	$featuremap->num_loci = $num_loci;
	$featuremap->num_lg = $num_lg;
	$featuremap->cmap_url = $cmap_url;
}

/**
 * @ingroup tripal_stock
 */
function tripal_stock_preprocess_tripal_stock_custom_base(&$variables) {
	$stock = $variables['node']->stock;
	// Get Maternal Parent
	$results = chado_query(
			"SELECT S2.stock_id, S2.uniquename, S2.description, V.name AS type
			 FROM {stock} S1 
         INNER JOIN {stock_relationship} SR ON SR.subject_id = S1.stock_id 
         INNER JOIN {stock} S2 ON S2.stock_id = SR.object_id 
         INNER JOIN {cvterm} V ON V.cvterm_id = S2.type_id
         WHERE SR.type_id = 
         (SELECT cvterm_id 
          FROM {cvterm} 
          WHERE name = 'is_a_maternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
          AND S1.stock_id = :stock_id",  array(':stock_id' => $stock->stock_id));
	$maternal_parent = NULL;
	$counter = 0;
	while ($parent = $results->fetchObject()) {
		if (db_table_exists("chado_stock")) {
			$parent->nid = db_query("SELECT nid FROM chado_stock WHERE stock_id = :stock_id", array(':stock_id' => $parent->stock_id))->fetchField();
		}
		$maternal_parent[$counter] = $parent;
		$counter ++;
	}
	$stock->maternal_parent = $maternal_parent;
	// Get Paternal Parent
	$results = chado_query(
	    "SELECT S2.stock_id, S2.uniquename, S2.description, V.name AS type
			 FROM {stock} S1
         INNER JOIN {stock_relationship} SR ON SR.subject_id = S1.stock_id
         INNER JOIN {stock} S2 ON S2.stock_id = SR.object_id
         INNER JOIN {cvterm} V ON V.cvterm_id = S2.type_id
         WHERE SR.type_id =
         (SELECT cvterm_id
          FROM {cvterm}
          WHERE name = 'is_a_paternal_parent_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
          AND S1.stock_id = :stock_id", array(':stock_id' => $stock->stock_id));
	$paternal_parent = NULL;
	$counter = 0;
	while ($parent = $results->fetchObject()) {
	  if (db_table_exists("chado_stock")) {
	    $parent->nid = db_query("SELECT nid FROM chado_stock WHERE stock_id = :stock_id", array(':stock_id' => $parent->stock_id))->fetchField();
	  }
	  $paternal_parent[$counter] = $parent;
	  $counter ++;
	}
	$stock->paternal_parent = $paternal_parent;
	
	// Get maps for this stock and maps of its children
	$results = chado_query(
	    "SELECT
           M.featuremap_id,
			  M.name
           FROM {featuremap} M
           INNER JOIN {featuremap_stock} MS ON M.featuremap_id = MS.featuremap_id
           WHERE MS.stock_id = :stock_id1
           OR MS.stock_id in
			     (SELECT SR.object_id FROM {stock} S1
               INNER JOIN {stock_relationship} SR ON S1.stock_id = SR.subject_id
               WHERE SR.type_id IN
                  (SELECT cvterm_id
                   FROM {cvterm}
                   WHERE (name = 'is_a_maternal_parent_of'
                   OR name = 'is_a_paternal_parent_of') AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
               AND S1.stock_id = :stock_id2)",  array(':stock_id1' => $stock->stock_id,  ':stock_id2' => $stock->stock_id));
	$maps = NULL;
	$counter = 0;
	while ($map = $results->fetchObject()) {
	  if (db_table_exists("chado_featuremap")) {
	    $map->nid = db_query("SELECT nid FROM chado_featuremap WHERE featuremap_id = :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
	  }
	  $map->ptype = chado_query("SELECT value FROM {featuremapprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'population_type' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')) AND featuremap_id = :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
	  $map->ggroup = chado_query("SELECT value FROM {featuremapprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'genome_group' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')) AND featuremap_id =  :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
	  $map->mtype = chado_query("SELECT value FROM {featuremapprop} WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'map_type' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')) AND featuremap_id =  :featuremap_id", array(':featuremap_id' => $map->featuremap_id))->fetchField();
	
	  $maps[$counter] = $map;
	  $counter ++;
	}
	$stock->population_map = $maps;
}

/**
 * @ingroup tripal_stock
 */
function tripal_stock_preprocess_tripal_stock_phenotypic_data(&$variables) {
	$stock = $variables['node']->stock;
	$results = chado_query(
			"SELECT
			P.uniquename, P.value, J.name AS project, GEO.environment, SP.value AS replications
			FROM {nd_experiment_phenotype} NEP
			INNER JOIN {phenotype} P ON NEP.phenotype_id = P.phenotype_id
			INNER JOIN {nd_experiment_stock} NES ON NES.nd_experiment_id = NEP.nd_experiment_id
			INNER JOIN {nd_experiment_project} NEJ ON NES.nd_experiment_id = NEJ.nd_experiment_id
			INNER JOIN {project} J ON J.project_id = NEJ.project_id
			INNER JOIN {nd_experiment} NE ON NE.nd_experiment_id = NEP.nd_experiment_id
			LEFT JOIN 
			(SELECT 
			   NG.nd_geolocation_id,
	           NGP.value AS environment
			 FROM {nd_geolocation} NG 
			 INNER JOIN {nd_geolocationprop} NGP ON NG.nd_geolocation_id = NGP.nd_geolocation_id
			 AND NGP.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'site_code' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
			) GEO ON GEO.nd_geolocation_id = NE.nd_geolocation_id
	        LEFT JOIN {stockprop} SP ON NES.stock_id = SP.stock_id AND SP.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'rep' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
			WHERE NES.stock_id = :stock_id OR NES.stock_id IN (SELECT subject_id FROM {stock_relationship} WHERE object_id = :object_id
			AND type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))) 
	        ORDER BY project, environment, replications, uniquename", array(':stock_id' => $stock->stock_id, ':object_id' => $stock->stock_id));
	$phenotypic_data = NULL;
	$counter = 0;
	while ($traits = $results->fetchObject()) {
		$phenotypic_data[$counter] = $traits;
		$counter ++;
	}
	$stock->phenotypic_data = $phenotypic_data;
}

/**
 * @ingroup tripal_stock
 */
function tripal_stock_preprocess_tripal_stock_genotypic_data(&$variables) {
	$stock = $variables['node']->stock;
	$results = chado_query(
			"SELECT
			G.uniquename, G.description, J.name AS project, F.organism_id, (SELECT nid FROM chado_feature WHERE feature_id = F.feature_id) AS marker_nid
			FROM {nd_experiment_genotype} NEG
			INNER JOIN {genotype} G ON NEG.genotype_id = G.genotype_id
			INNER JOIN {feature_genotype} FG ON G.genotype_id = FG.genotype_id 
			INNER JOIN {feature} F ON F.feature_id = FG.feature_id 
			INNER JOIN {nd_experiment_stock} NES ON NES.nd_experiment_id = NEG.nd_experiment_id
			INNER JOIN {nd_experiment_project} NEJ ON NES.nd_experiment_id = NEJ.nd_experiment_id
			INNER JOIN {project} J ON J.project_id = NEJ.project_id
			WHERE stock_id IN 
			  (SELECT stock_id 
			   FROM {stock} S
			   INNER JOIN {stock_relationship} SR ON S.stock_id = SR.subject_id
			   WHERE SR.type_id = 
			      (SELECT cvterm_id FROM {cvterm} WHERE name = 'sample_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
			   AND SR.object_id = :object_id)",  array(':object_id' => $stock->stock_id));
	$gdata = NULL;
	$counter = 0;
	while ($gtype = $results->fetchObject()) {
		$gdata[$counter] = $gtype;
		$counter ++;
	}
	$stock->genotypic_data = $gdata;
}

/**
 * @ingroup tripal_stock
 */
function tripal_stock_preprocess_tripal_stock_in_collection(&$variables) {
	$stock = $variables['node']->stock;
	$results = chado_query(
			"SELECT 
			max(SC.name) AS collection, 
			max(db.name) AS db,
			max(db.urlprefix) AS urlprefix,
			string_agg(X.accession, '; ') AS accession
			FROM {stockcollection} SC
			INNER JOIN {stockcollection_stock} SS ON SS.stockcollection_id = SC.stockcollection_id
			INNER JOIN {stockcollection_db} SD ON SC.stockcollection_id = SD.stockcollection_id
			INNER JOIN {db} ON db.db_id = SD.db_id
			INNER JOIN {stock_dbxref} SX ON SX.stock_id = SS.stock_id
			INNER JOIN {dbxref} X ON SX.dbxref_id = X.dbxref_id AND X.db_id = db.db_id
			WHERE SS.stock_id = :stock_id
			GROUP BY SC.stockcollection_id
			",  array(':stock_id' => $stock->stock_id));
	$coll = NULL;
	$counter = 0;
	while ($c = $results->fetchObject()) {
		$coll[$counter] = $c;
		$counter ++;
	}
	$stock->in_collection = $coll;
}


/**
 * @ingroup tripal_stock
 */
function tripal_stock_preprocess_tripal_stock_image(&$variables) {
	$stock = $variables['node']->stock;
	// We want to show maps for this stock and maps of its children
	if (mainlab_tripal_get_site() == 'cottongen') { // CottonGen has image legend stored
		$results = chado_query(
			"SELECT eimage_type, image_uri, value as legend 
			 FROM {stock_image} SI 
			 INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id 
			 INNER JOIN {eimageprop} IP ON I.eimage_id = IP.eimage_id 
			 WHERE IP.type_id = (SELECT cvterm_id 
			                                      FROM {cvterm} 
			                                      WHERE name = 'legend' 
			                                      AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
			                                    )
			AND stock_id = :stock_id", 
			array(':stock_id' => $stock->stock_id));
	} else { // Do not pull legends by default (e.g. for GDR)
		$results = chado_query(
			"SELECT eimage_type, image_uri
			 FROM {stock_image} SI
			 INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id
			 WHERE stock_id = :stock_id",
				array(':stock_id' => $stock->stock_id));
	}
	$images = NULL;
	$counter = 0;
	while ($img = $results->fetchObject()) {
		$images[$counter] = $img;
		$counter ++;
	}
	$stock->images = $images;
}

/**
 * @ingroup tripal_stock
 */
function tripal_pub_preprocess_tripal_pub_image(&$variables) {
  $pub = $variables['node']->pub;
  // We want to show maps for this pub and maps of its children
  $results = chado_query(
      "SELECT eimage_type, image_uri, value as legend
			 FROM {pub_image} SI
			 INNER JOIN {eimage} I ON SI.eimage_id = I.eimage_id
			 INNER JOIN {eimageprop} IP ON I.eimage_id = IP.eimage_id
			 WHERE IP.type_id = (SELECT cvterm_id
			                                      FROM {cvterm}
			                                      WHERE name = 'legend'
			                                      AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
			                                    )
			AND pub_id = :pub_id",
      array(':pub_id' => $pub->pub_id));
  $images = NULL;
  $counter = 0;
  while ($img = $results->fetchObject()) {
    $images[$counter] = $img;
    $counter ++;
  }
  $pub->images = $images;
}

/**
 * @ingroup tripal_natural_diversity
 */
function tripal_pub_preprocess_tripal_nd_geolocation_base_custom(&$variables) {
  $nd_geolocation = $variables['node']->nd_geolocation;
  $sql = 
      "SELECT nid, name FROM {project} P
       LEFT JOIN chado_project CP ON CP.project_id = P.project_id
       WHERE P.project_id IN 
       (SELECT project_id 
        FROM {nd_geolocation} NG 
        INNER JOIN {nd_experiment} NE on NE.nd_geolocation_id = NG.nd_geolocation_id
        INNER JOIN {nd_experiment_project} NP ON NE.nd_experiment_id = NP.nd_experiment_id
        WHERE NG.nd_geolocation_id = :nd_geolocation_id
        GROUP BY project_id)";
  $results = chado_query($sql, array(':nd_geolocation_id' => $nd_geolocation->nd_geolocation_id));
  $projects = array();
  while ($d = $results->fetchObject()) {
    array_push($projects, $d);
  }
  $nd_geolocation->projects = $projects;
}

/**
 * @ingroup tripal_natural_diversity
 */
function tripal_pub_preprocess_tripal_nd_geolocation_associated_dataset(&$variables) {
  $nd_geolocation = $variables['node']->nd_geolocation;
  $sql =
  "SELECT nid, name, PP.value AS description FROM {project} P
       LEFT JOIN chado_project CP ON CP.project_id = P.project_id
       LEFT JOIN {projectprop} PP ON P.project_id = PP.project_id 
         AND PP.type_id = (SELECT cvterm_id FROM {cvterm} 
         WHERE name = 'description' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN'))
       WHERE P.project_id IN
       (SELECT project_id
        FROM {nd_geolocation} NG
        INNER JOIN {nd_experiment} NE on NE.nd_geolocation_id = NG.nd_geolocation_id
        INNER JOIN {nd_experiment_project} NP ON NE.nd_experiment_id = NP.nd_experiment_id
        WHERE NG.nd_geolocation_id = :nd_geolocation_id
        GROUP BY project_id)";
  $results = chado_query($sql, array(':nd_geolocation_id' => $nd_geolocation->nd_geolocation_id));
  $projects = array();
  while ($d = $results->fetchObject()) {
    array_push($projects, $d);
  }
  $nd_geolocation->projects = $projects;
}

/**
 * @ingroup tripal_eimage
 */
function tripal_pub_preprocess_cottongen_tripal_eimage_base(&$variables) {
  $eimage = $variables['node']->eimage;
  // Get Eimage legend
  $sql =
  "SELECT value 
      FROM {eimage} E 
      LEFT JOIN {eimageprop} EP ON E.eimage_id = EP.eimage_id
      AND type_id = 
        (SELECT cvterm_id FROM {cvterm} WHERE name = 'legend'
         AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
        ) 
      WHERE E.eimage_id = :eimage_id";
  $results = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $legends = array();
  while ($result = $results->fetchObject()) {
    array_push($legends, $result->value);
  }
  $eimage->legends = $legends;
  // Get Eimage contact
  $sql =
  "SELECT C.contact_id, name, description
      FROM {contact} C
      INNER JOIN {contact_image} CI ON CI.contact_id = C.contact_id
      WHERE CI.eimage_id = :eimage_id";
  $results = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $contacts = array();
  while ($result = $results->fetchObject()) {
    $detail_contact = new stdClass();
    $detail_sql = "SELECT (SELECT name FROM {cvterm} WHERE cvterm_id = type_id) AS type, value FROM {contactprop} WHERE contact_id = :contact_Id";
    $detail_results = chado_query($detail_sql, array(':contact_id' => $result->contact_id));
    while ($details = $detail_results->fetchObject()) {
      $type = $details->type;
      $value = $details->value;
      if (property_exists($detail_contact, $type)) {
        $detail_contact->$type .= ". " . $value;
      } else {
        $detail_contact->$type = $value;
      }
    }
    $detail_contact->name = $result->name;
    $detail_contact->description = $result->description;
    array_push($contacts, $detail_contact);
  }
  $eimage->contacts = $contacts;
  
  // Get image pubs
  $sql =
  "SELECT uniquename, nid, P.pub_id
      FROM {pub} P
      INNER JOIN {pub_image} PI ON PI.pub_id = P.pub_id
      LEFT JOIN chado_pub CP ON CP.pub_id = P.pub_id
      WHERE PI.eimage_id = :eimage_id";
  $results = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $pubs = array();
  while ($result = $results->fetchObject()) {
    array_push($pubs, $result);
  }
  $eimage->pubs = $pubs;
  // See if this image belong to a marker
  $sql =
  "SELECT uniquename, nid
      FROM {feature} F
      INNER JOIN {feature_image} FI ON FI.feature_id = F.feature_id
      LEFT JOIN chado_feature CF ON F.feature_id = CF.feature_id
      WHERE FI.eimage_id = :eimage_id
      AND F.type_id = 
        (SELECT cvterm_id FROM {cvterm} WHERE name = 'genetic_marker'
         AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))";
  $marker = chado_query($sql, array(':eimage_id' => $eimage->eimage_id))->fetchObject();
  $eimage->marker = $marker;
  // See if this image belong to a stock
  $sql =
  "SELECT uniquename, nid, S.stock_id
      FROM {stock} S
      INNER JOIN {stock_image} SI ON SI.stock_id = S.stock_id
      LEFT JOIN chado_stock CS ON CS.stock_id = S.stock_id
      WHERE SI.eimage_id = :eimage_id";
  $stock = chado_query($sql, array(':eimage_id' => $eimage->eimage_id))->fetchObject();
  $eimage->stock = $stock;
  
  // Get associated projects
  $project = array();
  $sql = 
    "SELECT value FROM {eimageprop} 
     WHERE type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'project_id' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')) 
     AND eimage_id = :eimage_id";
  $result_pj = chado_query($sql, array(':eimage_id' => $eimage->eimage_id));
  $sql_pj = "SELECT distinct name, nid FROM {project} P LEFT JOIN chado_project CP ON CP.project_id = P.project_id WHERE P.project_id = :project_id";
  while ($pj = $result_pj->fetchObject()) {
  	$result = chado_query($sql_pj, array(':project_id' => $pj->value))->fetchObject();
  	array_push($project, $result);
  }
  $eimage->project = $project;
}

/**
 * @ingroup tripal_feature
 */
function tripal_feature_preprocess_tripal_feature_haplotype_block(&$variables) {
	$feature = $variables['node']->feature;
	
	// Get stocks
	$sql = "SELECT S.*, CS.nid FROM {feature_genotype} FG 
			INNER JOIN {nd_experiment_genotype} NEG ON NEG.genotype_id = FG.genotype_id
			INNER JOIN {nd_experiment_stock} NES ON NEG.nd_experiment_id = NES.nd_experiment_id 
			INNER JOIN {stock} S ON S.stock_id = NES.stock_id
			LEFT JOIN chado_stock CS ON CS.stock_id = S.stock_id
			WHERE feature_id =:feature_id";
	$result = chado_query($sql, array(':feature_id' => $feature->feature_id));
	$stocks = array();
	while ($stock = $result->fetchObject()) {
		array_push($stocks, $stock);
	}
	$feature->stocks = $stocks;
	
	// Get haplotypes
	$sql = 
	"SELECT 
		(SELECT replace (uniquename, HB.uniquename || '_', '') FROM {feature} WHERE feature_id = FR.subject_id) AS haplotype,
		FR2P.value,
		(SELECT feature_id FROM {feature} WHERE feature_id = FR2.object_id) AS marker_feature_id,
		(SELECT nid FROM chado_feature WHERE feature_id = FR2.object_id) AS marker_nid,
		(SELECT uniquename FROM {feature} WHERE feature_id = FR2.object_id) AS marker
	FROM {feature} HB 
	LEFT JOIN {feature_relationship} FR ON HB.feature_id = FR.object_id AND FR.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'variant_of' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))
	LEFT JOIN {feature_relationship} FR2 ON FR2.subject_id = FR.subject_id AND FR2.type_id = (SELECT cvterm_id FROM {cvterm} WHERE name = 'contains' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence'))
	LEFT JOIN {feature_relationshipprop} FR2P ON FR2.feature_relationship_id = FR2P.feature_relationship_id
	WHERE feature_id = :feature_id ORDER BY marker";
	$result = chado_query($sql, array(':feature_id' => $feature->feature_id));
	$markers = array();
	while ($record = $result->fetchObject()) {
		$marker_feature_id = $record->marker_feature_id;
		$marker = $record->marker;
		$marker_nid = $record->marker_nid;
		$h = $record->haplotype;
		$v = $record->value;
		// Exisiting marker, add haplotype
		if (array_key_exists($marker_feature_id, $markers)) {
			$oldm = $markers[$marker_feature_id];
			// If haplotype exists, add new value
			if (array_key_exists($h, $oldm->haplotypes)) {
				$oldv = $oldm->haplotypes[$h];
				$oldm->haplotypes[$h] = $oldv . '; ' . $v;
			} // create new haplotype
			else {
				$oldm->haplotypes[$h] = $v;
			}
			$markers[$marker_feature_id] = $oldm;
		// New marker, create haplotype
		}
		else {
			$m = new stdClass();
			$m->name = $marker;
			$m->nid = $marker_nid;
			$m->haplotypes = array ();
			$m->haplotypes [$h]= $v;
			$markers[$marker_feature_id] = $m;
		}
	}
	$feature->haplotypes = $markers;
}

/**
 *
 *
 * @ingroup tripal_organism
 */
function tripal_organism_preprocess_tripal_organism_relationships(&$variables) {
  // we want to provide a new variable that contains the matched organisms.
  $organism = $variables['node']->organism;

  // normally we would use chado_expand_vars to expand our
  // organism object and add in the relationships, however whan a large
  // number of relationships are present this significantly slows the
  // query, therefore we will manually perform the query
  $sql = "
    SELECT O.genus, O.species, O.organism_id, CO.nid, CVT.name as rel_type
    FROM {organism_relationship} ORel
      INNER JOIN {organism} O on ORel.object_organism_id = O.organism_id
      INNER JOIN{ cvterm} CVT on ORel.type_id = CVT.cvterm_id
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

}

/**
 *
 *
 * @ingroup tripal_feature
 */
function tripal_feature_preprocess_tripal_feature_genetic_marker_map_positions(&$variables) {
  $feature = $variables['node']->feature;
  // get map positions
  $results = chado_query(
      "SELECT
      CF.nid AS nid,
      FM.name AS name,
   	  X.accession,
   	  DB.urlprefix,
      LG.name AS linkage_group,
      BIN.name AS bin,
      LGP.value AS chr,
      FPP.value AS locus_start,
      LOCUS.uniquename AS locus_name
    FROM {feature} LOCUS
    INNER JOIN {feature_relationship} FR ON FR.subject_id = LOCUS.feature_id
    INNER JOIN {featurepos} FP ON LOCUS.feature_id = FP.feature_id
    INNER JOIN {featuremap} FM ON FP.featuremap_id = FM.featuremap_id
   	LEFT JOIN {featuremap_dbxref} FD ON FP.featuremap_id = FD.featuremap_id
   	LEFT JOIN {dbxref} X ON FD.dbxref_id = X.dbxref_id
   	LEFT JOIN {db} ON db.db_id = X.db_id
    INNER JOIN {feature} LG ON FP.map_feature_id = LG.feature_id
    LEFT JOIN
     (SELECT * FROM {featureprop} WHERE type_id =
      (SELECT cvterm_id
       FROM {cvterm}
       WHERE name = 'chr_number'
       AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
      )
     )LGP ON LG.feature_id = LGP.feature_id
    INNER JOIN {featureposprop} FPP ON FP.featurepos_id = FPP.featurepos_id
    LEFT JOIN
     (SELECT F2.name, FR2.subject_id FROM {feature} F2
      INNER JOIN {feature_relationship} FR2 ON FR2.object_id = F2.feature_id
   	WHERE FR2.type_id =
   	  (SELECT cvterm_id FROM {cvterm} WHERE name = 'located_in' AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship'))
     ) BIN ON LOCUS.feature_id = BIN.subject_id
    LEFT JOIN chado_featuremap CF ON FM.featuremap_id = CF.featuremap_id
    WHERE FR.type_id =
     (SELECT cvterm_id
      FROM {cvterm}
      WHERE name = 'instance_of'
   	AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'relationship')
     )
    AND LOCUS.type_id =
     (SELECT cvterm_id
      FROM {cvterm}
      WHERE name = 'marker_locus'
   	AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'sequence')
     )
    AND FPP.type_id =
     (SELECT cvterm_id
      FROM {cvterm}
      WHERE name = 'start'
   	AND cv_id = (SELECT cv_id FROM {cv} WHERE name = 'MAIN')
     )

    AND FR.object_id = :object_id", array(':object_id' => $feature->feature_id));
  $positions = array ();
  $counter = 0;
  while ($pos = $results->fetchObject()) {
   	$positions [$counter] = $pos;
   	$counter ++;
  }
  $feature->map_positions = $positions;
}