<?php
return; // Disable this template
/*
 * Details about genotypes associated with features can be found in the following way:
 * 
 * feature => feature_genotype => genotype
 *   
 * There are two ways that features with genotypes can be associated with stocks.  The first, 
 * more simple method, is by traversion the FK relationships in this manner:
 * 
 *   Simple Method: feature => feature_genotype => genotype => stock_genotype => stock
 *   
 * The second method involves use of the natural diversity tables which allows for association
 * or more ancilliary information. Within the Natural Diversity tables, if a feature has genotypes then 
 * you can find the corresponding stock by traversing the FK relationships 
 * in this manner: 
 * 
 *   ND Method:     feature => feature_genotype => nd_experiment_genotype => nd_experiment => nd_experiment_stock => stock
 * 
 * The tripal_natural_diversity module handles association of stocks using the ND method.  
 * This template handles association of stocks when stored using the simple method.
 * If the tripal_natural_diversity module is enabled then this template will not show.
 * You should instead see the tripal_feature.nd_genotypes.tpl.php template
 * 
 */
$feature = $variables['node']->feature;

// specify the number of genotypes to show by default and the unique pager ID
$num_results_per_page = 25; 

// get the genotypes from the feature_genotype table
$options = array(
  'return_array' => 1,
);
$feature = chado_expand_var($feature, 'table', 'feature_genotype', $options); 
$feature_genotypes = $feature->feature_genotype->feature_id;

// now iterate through the feature genotypes and print a paged table.
if (count($feature_genotypes) > 0) { 
  
  // the $headers array is an array of fields to use as the colum headers.
  // additional documentation can be found here
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $headers = array('Name', 'Type', 'Genotype', 'Details', 'Germplasm');

  // the $rows array contains an array of rows where each row is an array
  // of values for each column of the table in that row.  Additional documentation
  // can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $rows = array();
  
  foreach($feature_genotypes as $feature_genotype) {
    $genotype = $feature_genotype->genotype_id;
    
    // show the uniquename for the genotype unless a name exists
    $name = $genotype->uniquename;
    if ($genotype->name){
      $name = $genotype->name;
    }
    
    // get the genotype type
    $type = 'N/A';
    if ($genotype->type_id) {
      $type = ucwords(preg_replace('/_/', ' ', $genotype->type_id->name));
    }
    
    // get the genotype properties
    $options = array('return_array' => 1);
    $genotype = chado_expand_var($genotype, 'table', 'genotypeprop', $options);
    $properties = $genotype->genotypeprop; 
    $details = '';
    if(count($properties) > 0) {
      foreach ($properties as $property){
        $details .=  ucwords(preg_replace('/_/', ' ', $property->type_id->name)) . ': ' . $property->value . '<br>';
      }
      $details = substr($details, 0, -4); // remove trailing <br>
    }
    
    // add in stocks associated with this genotype if any
    $options = array(
      'return_array' => 1,
      'inlude_fk' => array(
        'stock_id' => array(
          'type_id' => 1
        )
      ),
    );
    $genotype = chado_expand_var($genotype, 'table', 'stock_genotype', $options);
    $stock_genotypes = $genotype->stock_genotype; 

    // build the list of germplasm.
    $stock_names = '';
    if(count($stock_genotypes) > 0) { 
      foreach ($stock_genotypes as $stock_genotype){ 
        $stock = $stock_genotype->stock_id; 
        $stock_name = $stock->name . ' (' . $stock->uniquename . ')'; 
        if(property_exists($stock, 'nid')) {
          $stock_name = l($stock_name, 'node/' . $stock->nid, array('attributes' => array('target' => '_blank')));
        }
        $stock_names .= $stock_name . '<br>';
      }
      $stock_names = substr($stock_names, 0, -4); // remove trailing <br>
    } 
    // add the fields to the table row
    $rows[] = array(
      $name,
      $type,
      $genotype->description,
      $details,
      $stock_names
    );
  }
   
  // the $table array contains the headers and rows array as well as other
  // options for controlling the display of the table.  Additional
  // documentation can be found here:
  // https://api.drupal.org/api/drupal/includes%21theme.inc/function/theme_table/7
  $table = array(
    'header' => $headers,
    'rows' => $rows,
    'attributes' => array(
      'id' => 'tripal_genetic-table-genotypes',
      'class' => 'tripal-data-table'
    ),
    'sticky' => FALSE,
    'caption' => '',
    'colgroups' => array(),
    'empty' => '',
  );
  
  // once we have our table array structure defined, we call Drupal's theme_table()
  // function to generate the table.
  print theme_table($table); 
}
