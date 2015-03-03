<?php
/*
 * Create a page that lists all features filtering by the ogramisn (i.e. $organism in 'genus-species' format), 
 * or the feature type (i.e. $type), or the germplasm name ($stock). All these variables are optional if the passed
 * in value = '_' (i.e. underscore). P.S. This function requires tripal_feature module
*/
function mainlab_gene_class_page ($symbol = '') {
	$output = "";
	$output .= "<p>Genes with symbol <strong>$symbol</strong>.</p>";
	
	$sql = 
	"SELECT 
			(SELECT nid FROM chado_feature WHERE feature_id = GENE.feature_id) AS nid,
			GENE.uniquename,
			(SELECT genus || ' ' || species FROM organism WHERE organism_id = F.organism_id) AS organism,
			 C.name AS contact
			FROM {feature} F
			INNER JOIN {feature_relationship} FR ON F.feature_id = subject_id
			INNER JOIN {feature} GENE ON GENE.feature_id = object_id 
			LEFT JOIN {feature_contact} FC ON F.feature_id = FC.feature_id
			LEFT JOIN {contact} C ON C.contact_id = FC.contact_id 
				WHERE F.uniquename = '%s' 
				AND F.type_id = 
				(SELECT cvterm_id FROM {cvterm} 
				WHERE name = 'gene_class' 
				AND cv_id = 
				(SELECT cv_id FROM {cv} 
				WHERE name = 'sequence'))";
	
	$result = chado_query($sql, $symbol);
	$rows = array();
	$row_count = 0;
	while ($row = db_fetch_object($result)) {
		$class = '';
		if ($row_count % 2 == 0) {
			$class = 'tripal-table-even-row';
		} else {
			$class = 'tripal-table-odd-row';
		}
		$rows[] = $row->nid ? array('data' => array("<a href=\"/node/$row->nid\">" . $row->uniquename . "</a>", $row->organism, $row->contact, ), 'class' =>$class) : array('data' => array($row->uniquename, $row->organism, $row->contact, ), 'class' =>$class);
		$row_count ++;
	}
	$header = array('Name', 'Organism', 'Contact');
	$attribute = array('class' => 'tripal-table tripal-table-horz');
	$output .= theme_table($header, $rows, $attribute);
	$output .= "<br>";
	
	return $output;
}