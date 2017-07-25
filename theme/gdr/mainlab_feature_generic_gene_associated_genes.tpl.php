<?php
$feature = $node->feature;
$feature = chado_expand_var($feature, 'table', 'feature_relationship');

$object_rels = $feature->feature_relationship->object_id;
if (!is_array($object_rels)) {
  $object_rels = array($object_rels);
}

if (count($object_rels) > 0) {
  $genus = $feature->organism_id->genus;
?>
  <div id="tripal_feature-associated_genes-box" class="tripal_feature-info-box tripal-info-box">
    <div class="tripal_feature-info-box-title tripal-info-box-title">Associated Genes/Sequences</div>
    <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div> 
       <p>The following feature(s) are associated with this gene:
          <table id="tripal_feature-associated_genes_as_object-table" class="tripal_feature-table tripal-table tripal-table-horz">
            <tr>
              <th>Type</th>
              <th>Gene/mRNA Name</th>
              <th>Genome Location</th>
              <th>NCBI Accession</th>
            </tr><?php      

            // add in the 'associated_with' relationships.  
            $i = 1;
            $genes = array();
            foreach ($object_rels as $rels){
              $class = 'tripal-table-odd-row odd';
              if ($i % 2 == 0 ) {
              $class = 'tripal-table-even-row even';
              }
              
            // Get associated genes for this generic gene
            if ($rels->type_id->name == 'associated_with') {
              $gene = $rels->subject_id;
              $gene = chado_expand_var($gene, 'table', 'feature_dbxref', array('return_array' => 1));
              $table_options =  array(
                'include_fk' => array(
                  'srcfeature_id' => array('type_id' => 1),
                'feature_id' => array('type_id' => 1)
                )
              );
            
              $gene = chado_expand_var($gene, 'table', 'featureloc', $table_options); // associated gene
              $accessions = $gene->feature_dbxref;
              $accs = "";
              foreach ($accessions AS $accession) {
                $dbname = $accession->dbxref_id->db_id->name;
                if ($dbname == 'nuccore') {
                  $prefix = $accession->dbxref_id->db_id->urlprefix;                
                  $acc = $accession->dbxref_id->accession;
                  $accs .= "<a href=$prefix$acc target=_blank>$acc</a><br>";
                }
              }
              $accs = $accs ? $accs : "N/A";
              // Direct alignments to the genome
              $flocs = is_array($gene->featureloc->feature_id) ? $gene->featureloc->feature_id : array ($gene->featureloc->feature_id);
              $loc = "";
              
              foreach ($flocs AS $floc) {
                $counter_alignments = 0;
                $srcfeature = $floc->srcfeature_id; // Alignement for the gene
                $srcfeature = chado_expand_var($srcfeature, 'table', 'analysisfeature');
                $analysis = $srcfeature->analysisfeature->analysis_id;
                $type = $analysis->sourcename == 'NCBI' ? 'parsed from NCBI nr database' : 'predicted genes from whole genome assembly';
                if ($analysis->sourcename == 'NCBI') {
                  $type = $gene->type_id->name == 'mRNA' ? 'mRNA ' . $type : 'gene ' . $type;
                }
                
                if ($srcfeature->type_id->name == 'chromosome' || $srcfeature->type_id->name == 'supercontig' || $srcfeature->type_id->name == 'contig') {
                  if ($counter_alignments > 5) {
                    break; // Limit the number of alignment to 5
                  }
                  $sf = $srcfeature->type_id->name == 'contig' ? $srcfeature->name : $srcfeature->name . ":" . $floc->fmin . ".." . $floc->fmax;
                  // Add hyperlinks to the location
                  if ($analysis->name == 'Prunus persica Whole Genome v1.0 Assembly & Annotation') {
                    $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/prunus_persica?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                  }
                  else if ($analysis->name == 'Malus x domestica Whole Genome v1.0 Assembly & Annotation') {
                    $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/malus_x_domestica?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                  }
                  else if ($analysis->name == 'Malus x domestica Whole Genome v1.0p Assembly & Annotation') {
                    $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/malus_x_domestica_v1.0-primary?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                  }
                  else if ($analysis->name == 'Fragaria vesca Whole Genome v1.0 (build 8) Assembly & Annotation') {
                    $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/fragaria_vesca_v1.1-lg?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                  }
                  else {
                    $loc .= $sf . "<br>";
                  }
                  $counter_alignments ++;
                }
              }
              // Alignments through an intermediate feature
              $flocs_mid = $gene->featureloc->srcfeature_id;
              if ($flocs_mid) {
                // Sometimes Tripal reutrns object while other times array. We need to make sure it's an array
                $flocs_mids = array ();
                if(!is_array($flocs_mid)) {
                  $flocs_mids = array($flocs_mid);
                } else {
                  $flocs_mids = $flocs_mid;
                }
                foreach ($flocs_mids AS $mid) {
                  $counter_alignments = 0;
                  $mid_feature = $mid->feature_id;
                  $mid_feature = chado_expand_var($mid_feature, 'table', 'featureloc',array('return_array' => 1));
                  $mid_locs = $mid_feature->featureloc->feature_id;
                  foreach($mid_locs AS $mid_loc) {
                    $mid_type = $mid_loc->srcfeature_id->type_id->name;
                    if ($mid_type == 'chromosome' || $mid_type == 'supercontig' || $mid_type == 'contig') {
                      if ($counter_alignments > 5) {
                        break; // Limit the number of alignment to 5
                      }
                      $mid_srcfeature = $mid_loc->srcfeature_id; // Alignement for the gene
                      $mid_srcfeature = chado_expand_var($mid_srcfeature, 'table', 'analysisfeature');
                      $mid_analysis = $mid_srcfeature->analysisfeature->analysis_id;
                      $sf = $mid_type == 'contig' ? $mid_srcfeature->name : $mid_srcfeature->name . ":" . $mid_loc->fmin . ".." . $mid_loc->fmax;
                      // Add hyperlinks to the location
                      if ($mid_analysis->name == 'Prunus persica Whole Genome v1.0 Assembly & Annotation') {
                        $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/prunus_persica?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                      }
                      else if ($mid_analysis->name == 'Malus x domestica Whole Genome v1.0 Assembly & Annotation') {
                        $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/malus_x_domestica?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                      }
                      else if ($mid_analysis->name == 'Malus x domestica Whole Genome v1.0p Assembly & Annotation') {
                        $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/malus_x_domestica_v1.0-primary?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                      }
                      else if ($mid_analysis->name == 'Fragaria vesca Whole Genome v1.0 (build 8) Assembly & Annotation') {
                        $loc .= "<a href=\"http://www.rosaceae.org/gb/gbrowse/fragaria_vesca_v1.1-lg?name=".$sf."\" target=\"_blank\">" . $sf . "</a><br>";
                      }
                      else {
                        $loc .= $sf . "<br>";
                      }
                      $counter_alignments ++;
                    }
                  }
                }
              }
              $loc = $loc ? $loc : "N/A";
              $link = mainlab_tripal_link_record('feature', $gene->feature_id);
              ?>
                <tr class="<?php print $class;?>">
                  <td><?php print $type ?></td>
                  <td><a href="<?php print $link;?>"><?php print $gene->uniquename;?></a></td>
                  <td><?php print $loc;?></td>
                  <td><?php print $accs;?></td>
                </tr><?php  
              $i ++;
              array_push($genes, $gene);
          }
      }
 ?>
  </table><?php 
  print "<div><br>Sequence(s): </div>";
  foreach($genes AS $g) {
    chado_expand_var($g, 'field', 'feature.residues');
    if ($g->residues) {
      $sequences_html = '<a name="residues"></a>';
      $sequences_html .= '<div id="residues" class="tripal_feature-sequence-item">';
      $sequences_html .= '<pre class="tripal_feature-sequence" style="max-height:200px;overflow:scroll">';
      $sequences_html .= '>' . $g->uniquename . strlen($g->residues) . "\n";
      $sequences_html .= wordwrap($g->residues, 80, "<br>", TRUE);
      $sequences_html .= '</pre>';
      $sequences_html .= "<a href='#' onclick=\"$('html, body').animate({ scrollTop: $('#tripal_feature-associated_genes-box').offset().top }, 1000);return false;\">back to top</a>";
      $sequences_html .= '</div>';
    print $sequences_html;
    }
  }
  ?>
  </div> <?php

}
