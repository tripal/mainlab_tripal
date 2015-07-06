<?php
/*
 * Create a page that lists all features filtering by the ogramisn (i.e. $organism in 'genus-species' format), 
 * or the feature type (i.e. $type), or the germplasm name ($stock). All these variables are optional if the passed
 * in value = '_' (i.e. underscore). P.S. This function requires tripal_feature module
*/
function mainlab_gene_class_listing_page ($letter = '') {
	$qcount = 
	"SELECT count (*) 
			FROM 
				(SELECT uniquename FROM {feature} 
				WHERE uniquename like '%s%%' 
				AND type_id = 
				(SELECT cvterm_id FROM {cvterm} 
				WHERE name = 'gene_class' 
				AND cv_id = 
				(SELECT cv_id FROM {cv} 
				WHERE name = 'sequence'))
				GROUP BY name, uniquename) CNT";
	$total = db_result(chado_query($qcount, $letter));
	$output = "<strong>List of All Gene Symbols</strong>
<div style=\"margin:10px 0px\">The list of gene symbols in Rosaceae have been put together from data in the NCBI nr database and user-submitted data. 
			Please <a href=\"/contact\"><strong>contact us</strong></a> if you would like to submit new gene data or edit the existing gene data. The
			Gene data submission form is available from the <a href=\"/data/submission\"><strong>data submission page</strong></a>.</div>
<div style=\"text-align:center\">";
	$output .= $letter == '' ? "All&nbsp;&nbsp" : "<a href=\"/gene_class_listing\">All&nbsp;&nbsp</a>";
	foreach (range('A', 'Z') AS $page) {
		$output .= $letter == $page ? " $page" : " <a href=\"/gene_class_listing/$page\">$page</a>";
	}
$output .= "</div>";
	$output .= "<p>Total <strong>" . $total . "</strong></p>";
	
	$sql = 
	"SELECT string_agg(distinct C.name, '; ') AS contact, F.name, F.uniquename, string_agg(distinct to_char(timelastmodified, 'YYYY-MM-DD'), '; ') AS timelastmodified FROM {feature} F
			LEFT JOIN {feature_contact} FC ON FC.feature_Id = F.feature_id
			LEFT JOIN {contact} C ON FC.contact_id = C.contact_id 
			WHERE uniquename like '%s%%' 
			AND F.type_id = 
			(SELECT cvterm_id FROM {cvterm} 
			WHERE name = 'gene_class' 
			AND cv_id = 
			(SELECT cv_id FROM {cv} 
			WHERE name = 'sequence'))
			GROUP BY F.name, F.uniquename
			ORDER BY F.uniquename";
	
	$result = chado_query($sql, $letter);
	$rows = array();
	$row_count = 0;
	while ($row = db_fetch_object($result)) {
		$class = '';
		if ($row_count % 2 == 0) {
			$class = 'tripal-table-even-row';
		} else {
			$class = 'tripal-table-odd-row';
		}
		
		$rows[] = array('data' => array("<a href=\"/gene_class/$row->uniquename\">" . $row->uniquename . "</a>", $row->name, $row->contact, $row->timelastmodified), 'class' =>$class);
		$row_count ++;
	}
	$header = array('Symbol', 'Symbol Full Name', 'Contact', 'Submitted');
	$attribute = array('class' => 'tripal-table tripal-table-horz');
	$output .= theme_table($header, $rows, $attribute);
	$output .= "<br>";
	
	return $output;
}