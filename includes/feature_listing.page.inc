<?php
/*
 * Create a page that lists all features filtering by the ogramisn (i.e. $organism in 'genus-species' format), 
 * or the feature type (i.e. $type), or the germplasm name ($stock). All these variables are optional if the passed
 * in value = '_' (i.e. underscore). P.S. This function requires tripal_feature module
*/
function mainlab_feature_listing ($organism, $type, $stock) {
	$sql = "";
	$conditions = array();
	if ($organism != '_') {
		$fullname = explode("-", $organism);
		$genus = $fullname [0];
		$species = $fullname [1];
		array_push($conditions, " feature.organism_id = (SELECT organism_id FROM organism WHERE genus = '$genus' AND species = '$species')");
	}
	if ($type != '_') {
		array_push($conditions, " type_id = (SELECT cvterm_id FROM cvterm WHERE name = '$type' AND cv_id = (SELECT cv_id FROM cv WHERE name = 'sequence'))");
	}
	if ($stock != '_') {
		$sql .= " INNER JOIN feature_stock ON feature.feature_id = feature_stock.feature_id";
		array_push($conditions, " stock_id = (SELECT stock_id FROM stock WHERE uniquename = '$stock')");
	}
	
	if (count($conditions) > 0) {
		$sql .= " WHERE";
	}
	$counter = 0;
	foreach($conditions AS $con) {
		$sql .= $con;
		if ($counter != count($conditions) - 1) {
			$sql .= " AND";
		}
		$counter ++;
	}
	$qcount = "SELECT count(*) FROM feature" . $sql;
	$total = db_result(chado_query($qcount));
	$output = "<p>Total <strong>" . $total . "</strong> features.</p>";
	$q = "SELECT nid, uniquename, cvterm.name AS type, genus, species 
			 FROM feature 
			 INNER JOIN cvterm ON cvterm_id = type_id
			 INNER JOIN organism O ON feature.organism_id = O.organism_id
			 LEFT JOIN chado_feature ON chado_feature.feature_id = feature.feature_id" . $sql;
	$result = pager_query($q, 20, 0, $qcount);
	$rows = array();
	$row_count = 0;
	while ($row = db_fetch_object($result)) {
		$class = '';
		if ($row_count % 2 == 0) {
			$class = 'tripal-table-even-row';
		} else {
			$class = 'tripal-table-odd-row';
		}
		if ($row->nid) {
		   $rows[] = array('data' => array("<a href=\"/node/$row->nid\">" . $row->uniquename . "</a>", $row->type, $row->genus . " " . $row->species)
		   		                      , 'class' =>$class);
		} else {
			$rows[] = array('data' => array($row->uniquename, $row->type, $row->genus . " " . $row->species)
					                       , 'class' =>$class);
		}
		$row_count ++;
	}
	$header = array('Uniquename', 'Type', 'Species');
	$attribute = array('class' => 'tripal-table tripal-table-horz');
	$output .= theme_table($header, $rows, $attribute);
	$output .= theme('pager', NULL, 20, 0);
	return $output;
	
}