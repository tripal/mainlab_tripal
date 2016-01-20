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
  while ($img = $results->fetchObject()) {
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
function tripal_pub_preprocess_tripal_pub_image(&$variables) {
  $pub = $variables['node']->pub;
  if (!db_table_exists('chado.pub_image')) {
    $pub->images = array();
    return;
  }
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
    $detail_sql = "SELECT (SELECT name FROM {cvterm} WHERE cvterm_id = type_id) AS type, value FROM {contactprop} WHERE contact_id = :contact_id";
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