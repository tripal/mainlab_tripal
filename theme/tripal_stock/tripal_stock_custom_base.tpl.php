<?php
$stock = $node->stock;
$organism = $node->stock->organism_id; 
$main_db_reference = $stock->dbxref_id;

// synonyms are stored in the stockprop table with a type of 'synonym'  or 'alias'
$stock->stock_synonyms = chado_generate_var(
		'stockprop',
		array(
				'stock_id'=> $stock->stock_id,
				'type_id' => array(
						'cv_id' => variable_get('chado_stock_prop_types_cv', 'null'),
						'name'  => 'synonym'
				),
		)
);

// Prepare synomyns data with type 'alias'
$synonyms = $stock->stock_synonyms;
if (!$synonyms) {
	$stock->stock_synonyms = chado_generate_var(
			'stockprop',
			array(
					'stock_id'=> $stock->stock_id,
					'type_id' => array(
							'cv_id' => variable_get('chado_stock_prop_types_cv', 'null'),
							'name'  => 'alias'
					),
			)
	);
	$synonyms = $stock->stock_synonyms;
}
$num_synonyms = count($synonyms);
$syn = "";
if ($num_synonyms == 0) {
	$syn = "N/A";
} else {
	if (is_array($synonyms)) {
	   $syn = $synonyms[0]->value . " [<a href=\"?pane=alias\">view all $num_synonyms</a>]";
	} else {
		$syn = $synonyms->value;
	}
}

// Prepare properties data
$stock = chado_expand_var($stock, 'table', 'stockprop', array('return_array' => 1));
$properties = $stock->stockprop;
$properties = chado_expand_var($properties, 'field', 'stockprop.value');
$desc = "N/A";
$orig = "N/A";
$pedigree = "N/A";
$comment = "N/A";
$reference = "N/A";

if ($properties) {
  foreach ( $properties as $prop ) {
    if ($prop->type_id->name == 'description') {
      if ($desc == "N/A") {
        $desc = $prop->value;
      }
      else {
        $desc .= ". " . $prop->value;
      }
    }
    elseif ($prop->type_id->name == 'origin') {
      $orig = $prop->value;
    }
    elseif ($prop->type_id->name == 'pedigree') {
      $pedigree = $prop->value;
    }
    elseif ($prop->type_id->name == 'comments') {
      $comment = $prop->value;
    }
    elseif ($prop->type_id->name == 'reference') {
      $reference = $prop->value;
    }
  }
}
$num_seq = chado_query("SELECT count (*) FROM {feature_stock} WHERE stock_id = :stock_id", array(':stock_id' => $stock->stock_id))->fetchField();

$stock_type = ucwords(preg_replace('/_/', ' ', $stock->type_id->name));
if($stock_type == 'TBD'){
  $stock_type = 'undefined';
}

// Maternal parents
$maternal_parent = $stock->maternal_parent;
$num_mparent = count($maternal_parent);
$first_mparent = "N/A";
if ($num_mparent > 0) {
  $first_mparent = $maternal_parent[0]->uniquename;
  if ($num_mparent > 1) {
    $first_mparent .= " [<a href=\"?pane=maternal_parent\">view all " . $num_mparent . "</a>]";
  }
}

//Paternal parents
$paternal_parent = $stock->paternal_parent;
$num_pparent = count($paternal_parent);
$first_pparent = "N/A";
if ($num_pparent > 0) {
  $first_pparent = $paternal_parent[0]->uniquename;
  if ($num_pparent > 1) {
    $first_pparent .= " [<a href=\"?pane=paternal_parent\">view all " . $num_pparent . "</a>]";
  }
}

// Population Maps
$num_population_map =  property_exists($stock, 'population_map') ? count($stock->population_map) : 0;

// Phenotypic Data
$num_phenotypic_data =  property_exists($stock, 'phenotypic_data') ? count($stock->phenotypic_data) : 0;

// Genotypic Data
$num_genotypic_data =  property_exists($stock, 'genotypic_data') ? count($stock->genotypic_data) : 0;

// Library
$stock = db_table_exists('library_stock') ? chado_expand_var($stock, 'table', 'library_stock', array('return_array' => 1)) : $stock;
$num_libraries =  property_exists($stock, 'library_stock') ? count($stock->library_stock) : 0;

$headers = array();
$rows = array();
$rows [] = array(array('data' => 'Name', 'header' => TRUE, 'width' => '20%'), $stock->uniquename);
$rows [] = array(array('data' => 'Alias', 'header' => TRUE, 'width' => '20%'), $syn);
$rows [] = array(array('data' => 'Type', 'header' => TRUE, 'width' => '20%'), $stock_type);
$rows [] = array(array('data' => 'Species', 'header' => TRUE, 'width' => '20%'), property_exists($organism, 'nid') ? "<a href=\"".url("node/". $organism->nid)."\">".$organism->genus ." " . $organism->species . "</a>" : $organism->genus ." " . $organism->species);
if (count($stock->in_collection) > 0) {
  $rows [] = array(array('data' => 'In Collection', 'header' => TRUE, 'width' => '20%'), "[<a href=\"?pane=in_collection\">view all</a>]");
}
$rows [] = array(array('data' => 'Description', 'header' => TRUE, 'width' => '20%'), $desc);
$rows [] = array(array('data' => 'Origin', 'header' => TRUE, 'width' => '20%'), $orig);
$rows [] = array(array('data' => 'Pedigree', 'header' => TRUE, 'width' => '20%'), $pedigree);
$rows [] = array(array('data' => 'Maternal Parent of', 'header' => TRUE, 'width' => '20%'), $first_mparent);
$rows [] = array(array('data' => 'Paternal Parent of', 'header' => TRUE, 'width' => '20%'), $first_pparent);
$rows [] = array(array('data' => 'Phenotypic Data', 'header' => TRUE, 'width' => '20%'), $num_phenotypic_data > 0 ? "[<a href='?pane=phenotypic_data'>view all $num_phenotypic_data</a>]" : 'N/A');
$rows [] = array(array('data' => 'Genotypic Data', 'header' => TRUE, 'width' => '20%'), $num_genotypic_data > 0 ? "[<a href='?pane=genotypic_data'>view all $num_genotypic_data</a>]" : 'N/A');
$rows [] = array(array('data' => 'Map', 'header' => TRUE, 'width' => '20%'), $num_population_map > 0 ? "[<a href='?pane=population_map'>view all $num_population_map</a>]" : 'N/A');
$rows [] = array(array('data' => 'DNA Library', 'header' => TRUE, 'width' => '20%'), $num_libraries > 0 ? "[<a href='?pane=dna_library'>view all $num_libraries </a>]" : 'N/A');
$rows [] = array(array('data' => 'Sequence', 'header' => TRUE, 'width' => '20%'), $num_seq > 0 ? "[<a href=\"/feature_listing/_/_/$stock->uniquename\">view all $num_seq </a>]" : 'N/A');
$rows [] = array(array('data' => 'Comments', 'header' => TRUE, 'width' => '20%'), $comment);
$rows [] = array(array('data' => 'Reference', 'header' => TRUE, 'width' => '20%'), $reference);

// allow site admins to see the feature ID
if (user_access('view ids')) {
  $rows[] = array(array('data' => 'Stock ID', 'header' => TRUE, 'class' => 'tripal-site-admin-only-table-row'), array('data' => $stock->stock_id, 'class' => 'tripal-site-admin-only-table-row'));
}
$table = array(
  'header' => $headers,
  'rows' => $rows,
  'attributes' => array(
    'id' => 'tripal_stock-table-custom_base',
  ),
  'sticky' => FALSE,
  'caption' => '',
  'colgroups' => array(),
  'empty' => '',
);
print theme_table($table);
?>

