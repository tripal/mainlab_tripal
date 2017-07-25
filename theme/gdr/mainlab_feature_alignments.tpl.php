<?php
// Alignments are available in four different variables.  The following 
// describes each one:
//
// If the feature for this node is the parent in the alignment relationships,
// then those alignments are available in this variable:
//    $feature->featureloc->srcfeature_id;
//
// If the feature for this node is the child in the alignment relationsips,
// then those alignments are available in this variable:
//   $feature->featureloc->feature_id;
//
// If the feature is aligned to another through an intermediary feature (e.g.
// a feature of type 'match', 'EST_match', 'primer_match', etc) then those
// alignments are stored in this variable:
//   featire->matched_featurelocs
//
// Below is an example of a feature that may be aligned to another through
// an intermediary:
//
//    Feature 1: Contig      ---------------   (left feature)
//    Feature 2: EST_match           -------
//    Feature 3: EST                 --------- (right feature)
//
// The feature for this node is always feature 1.  The purpose of this type 
// alignment is to indicate cases where there is the potential for overhang
// in the alignments, or, the ends of the features are not part of the alignment
// prehaps due to poor quality of the ends.  Blast results and ESTs mapped to
// contigs in Unigenes would fall under this category.
//
// To simplify display of these different types of alignments as fourth variable
// is available that concatenates all types of alignments for this feature into 
// a single object for iteration.

$feature = $variables['node']->feature;
$options = array('return_array' => 1);
$feature = chado_expand_var($feature, 'table', 'analysisfeature', $options);
$alignments = $feature->all_featurelocs;
$gbrowse_imgs = array();

if(count($alignments) > 0){ ?>
  <div id="tripal_feature-alignments-box" class="tripal_feature-info-box tripal-info-box">
    <table id="tripal_feature-featurelocs_as_child-table" class="tripal_feature-table tripal-table tripal-table-horz">
      <tr>
        <th>Feature Name</th>
        <th>Type</th>
        <th>Location</th>
        <th>Phase</th>
      </tr><?php
      $i = 1; 
      $img_count = 0;
      $hit_limit = FALSE;
      foreach ($alignments as $alignment){
        $class = 'tripal_feature-table-odd-row odd';
        if ($i % 2 == 0 ) {
          $class = 'tripal_feature-table-odd-row even';
        }?> 
        <tr class="<?php print $class ?>">
          <td><?php 
              $link = $alignment->name == $alignment->record->feature_id->name ? mainlab_tripal_link_record('feature', $alignment->record->feature_id->feature_id) : mainlab_tripal_link_record('feature', $alignment->record->srcfeature_id->feature_id);
            if ($link) {
              print "<a href=\"" . url($link) . "\">".$alignment->name."</a>";
            } else {
              print $alignment->name;
            }?>
          </td>
          <td><?php print $alignment->type ?></td>
          <td><?php  
            $strand = '.';
            if ($alignment->strand == -1) {
              $strand = '-';
            } 
            elseif ($alignment->strand == 1) {
               $strand = '+';
            } 

            // if this is a match then make the other location 
            if(isset($alignment->right_feature)){
              $rstrand = '.';
              if ($alignment->right_strand == -1) {
                   $rstrand = '-';
              } 
              elseif ($alignment->right_strand == 1) {
                   $rstrand = '+';
              }
              print $feature->name .":". ($alignment->fmin + 1) . ".." . $alignment->fmax . " " . $strand; 
              $location = "<br>" . $alignment->name .":". ($alignment->right_fmin + 1) . ".." . $alignment->right_fmax . " " . $rstrand;
              $floc = $alignment->name .":". ($alignment->right_fmin + 1) . ".." . $alignment->right_fmax;
              
              /************************************
               * Show GBrowse for NCBI genes
               ************************************/
              // All Prunus alignments (all Prunus are aligned to Prunus persica)
              if($feature->organism_id->genus == 'Prunus' && ($alignment->right_feature->type_id->name == 'supercontig' || $alignment->right_feature->type_id->name == 'chromosome')) {
                $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/prunus_persica?name=".$floc."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                if ($img_count < 10) {
                   $gbrowse_imgs['prunus_persica'] .= "<h4>Prunus persica v1.0</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica?name=" . $floc . "\" width=500px>";
                   $img_count ++;
                } 
                else {
                  $hit_limit = true;
                }
              // Show Apple alignments (all Malus are aligned to Malus x domestica)
              } 
              else if($feature->organism_id->genus == 'Malus' && ($alignment->right_feature->type_id->name == 'supercontig' || $alignment->right_feature->type_id->name == 'chromosome')) {
                $match_name = chado_query("SELECT uniquename FROM {feature} WHERE feature_id = :feature_id", array(':feature_id' => $alignment->record->left_feature_id))->fetchField();
                if (!preg_match("/_apple-prime/", $match_name)) {
                  $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/malus_x_domestica?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                  if ($img_count < 10) {
                     $gbrowse_imgs['malus_x_domestica-combine'] .= "<h4>Malus x domestica v1.0</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica?name=". $floc ."\" width=500px>";
                     $img_count ++;
                  } else {
                    $hit_limit = true;
                  }
                } else {
                  $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/malus_x_domestica_v1.0-primary?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                  if ($img_count < 10) {
                     $gbrowse_imgs['malus_x_domestica_v1.0-prime'] .= "<h4>Malus x domestica v1.0 pseudo haplotype (primary assembly)</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica_v1.0-primary?name=". $floc ."\" width=500px>";
                     $img_count ++;
                  } else {
                    $hit_limit = true;
                  }
                }
              // All Fragaria alignments (all Fragaria are aligned to Fragaria vesca)
              } else if($feature->organism_id->genus == 'Fragaria' && ($alignment->right_feature->type_id->name == 'supercontig' || $alignment->right_feature->type_id->name == 'chromosome')) {
                $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/fragaria_vesca_v1.1-lg?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                if ($img_count < 10) {
                   $gbrowse_imgs['fragaria_vesca_v1.1'] .= "<h4>Fragaria vesca v1.1 Pseudomolecule Assembly</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.1-lg?name=". $floc ."\" width=500px>";
                   $img_count ++;
                } else {
                  $hit_limit = true;
                }
              }
              print $location;
            }
            else {
              $floc = $alignment->name .":". ($alignment->fmin + 1) . ".." . $alignment->fmax;
              $location = $floc . " " . $strand; 
              if($feature->type_id->name == 'mRNA' or 
                 $feature->type_id->name == 'contig' or
                 $feature->type_id->name == 'gene'){
                $analysisfeatures = $feature->analysisfeature;
                if (!is_array($feature->analysisfeature)) {
                  $analysisfeatures = array($feature->analysisfeature);
                }
                foreach ($analysisfeatures as $analysisfeature) {
                  // Show GBrowse for genes & mRNA in the whole genome assembly
                  if($analysisfeature->analysis_id->name == 'Malus x domestica Whole Genome v1.0 Assembly & Annotation') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/malus_x_domestica?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['malus_x_domestica_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica?name=". $floc ."&type=cluster+contig+gene+gene2+repeat+IRSC_9K_apple_SNP_array+NCBI_Sequence_Alignments\" width=500px>";
                  }
                  else if($analysisfeature->analysis_id->name == 'Malus x domestica Whole Genome v1.0p Assembly & Annotation') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/malus_x_domestica_v1.0-primary?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['malus_x_domestica_v1.0p'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica_v1.0-primary?name=". $floc ."&type=scaffold+contig+gene+mRNA+RosCOS+NCBI_Sequence_Alignments\" width=500px>";
                  }
                  else if($analysisfeature->analysis_id->name == 'Prunus persica Whole Genome v1.0 Assembly & Annotation') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/prunus_persica?name=". $floc . "\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['prunus_persica_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica?name=". $floc ."&type=Transcripts+Alternative_Transcripts+genes-v1.0-r1+NCBI_Sequence_Alignments+Repeat_Consensus+Markers+RosCOS+snp_Koepke_2012+IRSC_9K_peach_SNP_array+IRSC_6K_cherry_SNP_array+Davis_6K_peach_SNPs+All_Candidate_SNPs\" width=500px>";
                  }
                  else if($analysisfeature->analysis_id->name == 'Fragaria vesca Whole Genome v1.0 (build 8) Assembly & Annotation') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/fragaria_vesca_v1.0-lg?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['fragaria_vesca_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.0-lg?name=". $floc ."&type=genemark_hybrid+genemark_hybrid_transcripts+genemark_abinitio+genemark_abinitio_transcripts+RosCOS\" width=500px>";
                  }
                  else if($analysisfeature->analysis_id->name == 'Fragaria vesca Whole Genome v1.1 Assembly & Annotation') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/fragaria_vesca_v1.1-lg?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['fragaria_vesca_v1.1'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.1-lg?name=". $floc ."&type=scaffold_alignments+genemark_hybrid+genemark_hybrid_transcripts+RosCos+NCBI_Sequence_Alignments\" width=500px>";
                  }
                  else if($analysisfeature->analysis_id->name == 'Pyrus communis Genome v1.0 Draft Assembly & Annotation') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/pyrus_communis_v1.0/?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['pyrus_communis_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/pyrus_communis_v1.0/?name=". $floc ."&type=gene_hybrid+mRNA_hybrid+gene_augustus+mRNA_augustus+NCBI_Sequence_Alignments\" width=500px>";
                  }
                  else if($analysisfeature->analysis_id->name == 'Prunus persica Whole Genome Assembly v2.0 & Annotation v2.1 (v2.0.a1)') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/prunus_persica_v2.0.a1/?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['prunus_persica_v2.0.a1'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica_v2.0.a1/?name=". $floc ."&type=Gene+Primary_Transcripts+Alternative_Transcripts\" width=500px>";
                  }
                  else if($analysisfeature->analysis_id->name == 'Rubus occidentalis Whole Genome Assembly v1.0 & Annotation v1') {
                    $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/rubus_occidentalis_v1.0.a1/?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                    $gbrowse_imgs['rubus_occidentalis_v1.0.a1'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/rubus_occidentalis_v1.0.a1//?name=". $floc ."&type=Gene+Transcripts\" width=500px>";
                  }
                }
                // Show GBrowse for markers aligned to the whole genome assembly
              } else if ($feature->type_id->name == 'genetic_marker'){
                  if ($feature->organism_id->genus == 'Prunus' && 
                       ($feature->organism_id->species == 'avium' or 
                        $feature->organism_id->species == 'cerasus' or
                        $feature->organism_id->species == 'dulcis' or
                        $feature->organism_id->species == 'ferganensis' or
                        $feature->organism_id->species == 'persica' or
                        $feature->organism_id->species == 'salicina' 
                       )) 
                  {
                      $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/prunus_persica?name=". $floc . "\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                      $gbrowse_imgs['prunus_persica_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica?name=". $floc ."&type=Transcripts+Alternative_Transcripts+genes-v1.0-r1+NCBI_Sequence_Alignments+Repeat_Consensus+Markers+RosCOS+snp_Koepke_2012+IRSC_9K_peach_SNP_array+IRSC_6K_cherry_SNP_array+Davis_6K_peach_SNPs+All_Candidate_SNPs\" width=500px>";
                  } else if ($feature->organism_id->genus == 'Malus' && $feature->organism_id->species == 'x domestica') {
                      if ($alignment->type == 'chromosome' or $alignment->type == 'supercontig') {
                         $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/malus_x_domestica_v1.0-primary?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                         $gbrowse_imgs['malus_x_domestica_v1.0p'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica_v1.0-primary?name=". $floc ."&type=scaffold+contig+gene+mRNA+RosCOS+NCBI_Sequence_Alignments\" width=500px>";
                      } 
                      else if ($alignment->type == 'contig') {
                         $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/malus_x_domestica?name=". $alignment->name ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                         $gbrowse_imgs['malus_x_domestica_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica?name=". $alignment->name ."&type=cluster+contig+gene+repeat+IRSC_9K_apple_SNP_array+NCBI_Sequence_Alignments\" width=500px>";
                      }
                  } else if ($feature->organism_id->genus == 'Fragaria' && $feature->organism_id->species == 'vesca') {
                      $location = "<a href=\"https://www.rosaceae.org/gb/gbrowse/fragaria_vesca_v1.1-lg?name=". $floc ."\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                      $gbrowse_imgs['fragaria_vesca_v1.1'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.1-lg?name=". $floc ."&type=scaffold_alignments+genemark_hybrid+genemark_hybrid_transcripts+RosCos+NCBI_Sequence_Alignments\" width=500px>";
                  // Show Fragaria marker alignments
                  } else if ($feature->organism_id->genus == 'Fragaria') {
                      $srcfeature = $alignment->record->srcfeature_id;
                      $srcfeature = chado_expand_var($srcfeature, 'table', 'analysisfeature');
                      if ($srcfeature->analysisfeature->analysis_id->name == 'Fragaria vesca Whole Genome v1.1 Assembly & Annotation') {
                        $location = "<a href=\"https://www.rosaceae.org/jbrowse/index.html?data=data/fragaria/fvesca_v1.1&loc=". $floc ."&tracks=snp90k\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                      }
                      else if ($srcfeature->analysisfeature->analysis_id->name == 'Fragaria vesca Whole Genome v2.0.a1 Assembly & Annotation') {
                        $location = "<a href=\"https://www.rosaceae.org/jbrowse/index.html?data=data/fragaria/fvesca_v2.0.a1&loc=". $floc ."&tracks=strawberry_90k_snp\" target=\"_blank\">$location</a>"; // add hyperlink to the location
                      }
                  }
                  
              }
              print $location;
            }?>
          </td>
          <td><?php print $alignment->phase ?></td>
        </tr> <?php
        $i++;
      } ?>
    </table><?php
    foreach ($gbrowse_imgs as $map => $img){ ?>
      <div id="tripal_feature-gbrowse-images" style="padding-top:20px; width=100%"><?php print $img ?></div> <?php  
    }
    if ($hit_limit) {
       print "<p><b>(Limited to 10 GBrowse images)</b></p>";
    }
  ?>
  </div><?php
}

