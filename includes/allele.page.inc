<?php
/*
 * Create an allele page that lists all germplasms (i.e. stocks) by using a marker's name (i.e. $marker) and the genotype name (i.e. $alleleid)
*/
function mainlab_show_allele ($marker, $alleleid, $oid, $project = 0, $stock_uniquename = 0, $species = 0) {
  $project = urldecode($project);
  $stock_uniquename = urldecode($stock_uniquename);
  $species = urldecode($species);
	return theme('mainlab_allele', array('marker' => $marker, 'allele_id' => $alleleid, 'organism_id' => $oid, 'project' => $project, 'stock_uniquename' => $stock_uniquename, 'species' => $species));
}
