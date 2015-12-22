<?php
$node = $variables['node'];
$organism = $variables['node']->organism;

// the comment field is a database text field so we have to expand it so that
// it is included in the organism object
$organism = tripal_core_expand_chado_vars($organism,'field','organism.comment');
$organism = tripal_core_expand_chado_vars($organism, 'table', 'organismprop');
$properties = $node->organism->organismprop;

$prop = array();
$count_cname = 0;
if ($properties) {
	foreach ($properties AS $p) {
		$prop[$p->type_id->name] = $p->value;
		if ($p->type_id->name == 'alias_common') {
			$count_cname ++;
		}
	}
}
$num_seq = chado_query("SELECT count (*) FROM {feature} WHERE organism_id = :organism_id", array(':organism_id' => $organism->organism_id))->fetchField();
$rel = $organism->all_relationships;
$subj_rel = $rel ['subject'];
$fertile = 'N/A';
$sterile = 'N/A';
$incompatible = 'N/A';
if (key_exists('fertile with', $subj_rel)) {
  $fertile_first = $subj_rel['fertile with'][0];
  $fertile_count = count($subj_rel['fertile with']);
  $fertile = $fertile_first->genus . ' ' . $fertile_first->species . ' [<a href="?pane=relationships">view all ' . $fertile_count . '</a>]';
}
if (key_exists('sterile with', $subj_rel)) {
  $sterile_first = $subj_rel['sterile with'][0];
  $sterile_count = count($subj_rel['sterile with']);
  $sterile = $sterile_first->genus . ' ' . $sterile_first->species . ' [<a href="?pane=relationships">view all ' . $sterile_count . '</a>]';
}
if (key_exists('incompatible with', $subj_rel)) {
  $incompatible_first = $subj_rel['incompatible with'][0];
  $incompatible_count = count($subj_rel['incompatible with']);
  $incompatible = $incompatible_first->genus . ' ' . $incompatible_first->species . ' [<a href="?pane=relationships">view all ' . $incompatible_count . '</a>]';
}

$organism = tripal_core_expand_chado_vars($organism,'table','library');


// get the references. if only one reference exists then we want to convert
// the object into an array, otherwise the value is an array
$libraries = $organism->library;
if (!$libraries) {
  $libraries = array();
}
elseif (!is_array($libraries)) {
  $libraries = array($libraries);
}
$num_lib = count($libraries);
$display_lib = $num_lib > 0 ? '[<a href="?pane=libraries">view all ' . $num_lib . '</a>]' : 'N/A'; 
?>
  
  <table id="tripal_organism-table-base" class="tripal_organism-table tripal-table tripal-table-vert" style="width:600px;float:left">
    <tr class="tripal_organism-table-odd-row odd">
      <th style="width:250px;">Species Name</th>
      <td><i><?php print $organism->species; ?></i></td>
    </tr>
    <tr class="tripal_organism-table-even-row even">
      <th>Family</th>
      <td><?php if (key_exists('family', $prop)) {print $prop['family']; } else {print "N/A";}?></td>
    </tr>
    <tr class="tripal_organism-table-odd-row odd">
      <th>Scientific Name</th>
      <td><?php if (key_exists('alias_scientific', $prop)) {print $prop['alias_scientific']; } else {print "N/A";}?></td>
    </tr>
    <tr class="tripal_organism-table-even-row even">
      <th>Synonym</th>
      <td><?php if (key_exists('alias_synonym', $prop)) {print $prop['alias_synonym']; } else {print "N/A";} ?></td>
    </tr>
    <tr class="tripal_organism-table-odd-row odd">
      <th>Common Name</th>
      <td><?php if (key_exists('alias_common', $prop)) {print $prop['alias_common'] . " [<a href=\"?pane=alias\">view all $count_cname</a>]"; } else {print "N/A";} ?></td>
    </tr>
    <tr class="tripal_organism-table-even-row even">
      <th>Geographic Origin</th>
      <td><?php if (key_exists('geographic_origin', $prop)) {print $prop['geographic_origin']; } else {print "N/A";}?></td>
    </tr>
    <tr class="tripal_organism-table-odd-row odd">
      <th>Genome</th>
      <td><?php if (key_exists('genome_group', $prop)) {print $prop['genome_group']; } else {print "N/A";} ?></td>
    </tr>
    <tr class="tripal_organism-table-even-row even">
      <th>Haploid Chromosome Number</th>
      <td><?php if (key_exists('haploid_chromosome_number', $prop)) {print $prop['haploid_chromosome_number']; } else {print "N/A";}?></td>
    </tr>
    <tr class="tripal_organism-table-odd-row odd">
      <th>Fertile with</th><td id="tripal-organism-fertile-species"><?php print $fertile; ?></td>
    </tr>
    <tr class="tripal_organism-table-even-row even">
      <th>Sterile with</th><td id="tripal-organism-sterile-species"><?php print $sterile; ?></td>
    </tr>
    <tr class="tripal_organism-table-odd-row odd">
      <th>Incompatible with</th><td id="tripal-organism-incompatible-species"><?php print $incompatible; ?></td>
    </tr>
        <tr class="tripal_organism-table-even-row even">
      <th>Germplasm</th><td id="tripal-organism-germplasm">N/A</td>
    </tr>
    <tr class="tripal_organism-table-odd-row odd">
      <th>Library</th><td id="tripal-organism-library"><?php print $display_lib; ?></td>
    </tr>
    <tr class="tripal_organism-table-even-row even">
        <th>Sequence</th>
        <?php 
          if ($num_seq > 0) {
            $seq_link = "[<a href=\"/feature_listing/$organism->genus-$organism->species/_/_\">view all $num_seq </a>]";
          } else {
            $seq_link = "N/A";
          } 
        ?>
      <td id="tripal_stock-table-sequence_value"><?php print $seq_link;?></td>
      </tr>
<?php
$image_file = "sites/default/files/tripal/tripal_organism/images/".$node->nid.".jpg";
?>
  </table>
  <?php if (file_exists($image_file)) {?>
  <div style="float:right;padding:10px 4px;"><img width="220px" src ="/<?php print $image_file;?>"></div>
  <?php } ?>   
</div>
