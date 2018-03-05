<?php
$feature = $variables['node']->feature;
$feature = chado_expand_var($feature, 'table', 'analysisfeature', array('return_array' => 1));
$alignments = $feature->all_featurelocs;
$gbrowse_imgs = array();

if(count($alignments) > 0){ ?>
  <div id="tripal_feature-alignments-box" class="tripal_feature-info-box tripal-info-box">
    <table id="tripal_feature-featurelocs_as_child-table" class="tripal_feature-table tripal-table tripal-table-horz">
      <tr><th>Feature Name</th><th>Type</th><th>Location</th><th>Analysis</th></tr><?php
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
            $link = '';
            if (isset($alignment->record->feature_id->name) && $alignment->name == $alignment->record->feature_id->name) {
              $link = mainlab_tripal_link_record('feature', $alignment->record->feature_id->feature_id);
            } else if (isset($alignment->record->srcfeature_id->feature_id)) {
              $link = mainlab_tripal_link_record('feature', $alignment->record->srcfeature_id->feature_id);
            }
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

            /***
             * if this is a match type alignment (e.g. NCBI gene alignments)
             */
            if(isset($alignment->right_feature)){
              $rstrand = '.';
              if ($alignment->right_strand == -1) {
                   $rstrand = '-';
              } 
              elseif ($alignment->right_strand == 1) {
                   $rstrand = '+';
              }
              print $feature->name .":". ($alignment->fmin + 1) . ".." . $alignment->fmax . " " . $strand; 
              $location = "<br>" . $alignment->name .":". ($alignment->right_fmin + 1) . ".." . $alignment->right_fmax . $rstrand;
              $floc = $alignment->name .":". ($alignment->right_fmin + 1) . ".." . $alignment->right_fmax;
              
              $landmark = $alignment->right_feature;
              $landmark = chado_expand_var($landmark, 'table', 'analysisfeature', array('return_array' => 1));
              $analysisfeatures = $landmark->analysisfeature;
              $analysisfeature = $analysisfeatures[0]; // We only check the first analysisfeature for alignment (This is most common)
              $analysis = isset($analysisfeature->analysis_id->name) ? trim($analysisfeature->analysis_id->name) : '';

              $srcfeature_id = $landmark->feature_id;
              $sql =
              "SELECT value
                FROM {feature} F
                INNER JOIN {analysisfeature} AF ON F.feature_id = AF.feature_id
                INNER JOIN {analysis} A ON A.analysis_id = AF.analysis_id
                INNER JOIN {analysisprop} AP ON AP.analysis_id = A.analysis_id
                INNER JOIN {cvterm} V ON V.cvterm_id = AP.type_id
                WHERE
                V.name = 'JBrowse URL' AND
                F.feature_id = :srcfeature_id";
              $jbrowse = $srcfeature_id ? chado_query($sql, array('srcfeature_id' => $srcfeature_id))->fetchField() : NULL;
              if ($jbrowse) {
                $location = '<a href="' . $jbrowse . $alignment->name .":". ($alignment->fmin + 1) . ".." . $alignment->fmax . '">' . $location . '</a>';
              }
              // PRUNUS
              if($analysis == 'Prunus persica Whole Genome v1.0 Assembly & Annotation') {
                if ($img_count < 10) {
                   $jb = "<h4>Prunus persica v1.0</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica?name=" . $floc . "\" width=500px>";
                   $gbrowse_imgs['prunus_persica'] = isset($gbrowse_imgs['prunus_persica']) ? $gbrowse_imgs['prunus_persica'] . jb : $jb;
                   $img_count ++;
                } 
                else {
                  $hit_limit = true;
                }
              }
              else if($analysis == 'Prunus persica Whole Genome Assembly v2.0 & Annotation v2.1 (v2.0.a1)') {
                if ($img_count < 10) {
                  $jb = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica_v2.0.a1/?name=". $floc ."&type=Gene+Primary_Transcripts+Alternative_Transcripts\" width=500px>";
                  $gbrowse_imgs['prunus_persica_v2.0.a1']  = isset($gbrowse_imgs['prunus_persica_v2.0.a1']) ? $gbrowse_imgs['prunus_persica_v2.0.a1'] . $jb : $jb;
                  $img_count ++;
                }
                else {
                  $hit_limit = true;
                }
              }
              // MALUS
              else if($analysis == 'Malus x domestica Whole Genome v1.0p Assembly & Annotation') {
                if ($img_count < 10) {
                   $jb = "<h4>Malus x domestica v1.0</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica?name=". $floc ."\" width=500px>";
                   $gbrowse_imgs['malus_x_domestica-combine']= isset($gbrowse_imgs['malus_x_domestica-combine']) ? $gbrowse_imgs['malus_x_domestica-combine'] . $jb : $jb;
                   $img_count ++;
                }
                else {
                  $hit_limit = true;
                }
              }
              else if($analysis == 'Malus x domestica Whole Genome v1.0 Assembly & Annotation') {
                if ($img_count < 10) {
                   $jb = "<h4>Malus x domestica v1.0 pseudo haplotype (primary assembly)</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica_v1.0-primary?name=". $floc ."\" width=500px>";
                   $gbrowse_imgs['malus_x_domestica_v1.0-prime'] = isset($gbrowse_imgs['malus_x_domestica_v1.0-prime']) ? $gbrowse_imgs['malus_x_domestica_v1.0-prime'] . $jb : $jb;
                   $img_count ++;
                }
                else {
                  $hit_limit = true;
                }
              }
              // FRAGARIA
              else if($analysis == 'Fragaria vesca Whole Genome v1.0 (build 8) Assembly & Annotation') {
                if ($img_count < 10) {
                   $jb = "<h4>Fragaria vesca v1.1 Pseudomolecule Assembly</h4><img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.1-lg?name=". $floc ."\" width=500px>";
                   $gbrowse_imgs['fragaria_vesca_v1.1'] = isset($gbrowse_imgs['fragaria_vesca_v1.1']) ? $gbrowse_imgs['fragaria_vesca_v1.1'] . $jb : $jb;
                   $img_count ++;
                }
                else {
                  $hit_limit = true;
                }
              }
              else if($analysis == 'Fragaria vesca Whole Genome v1.1 Assembly & Annotation') {
                if ($img_count < 10) {
                  $jb = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.1-lg?name=". $floc ."&type=scaffold_alignments+genemark_hybrid+genemark_hybrid_transcripts+RosCos+NCBI_Sequence_Alignments\" width=500px>";
                  $gbrowse_imgs['fragaria_vesca_v1.1'] = isset($gbrowse_imgs['fragaria_vesca_v1.1']) ? $gbrowse_imgs['fragaria_vesca_v1.1'] . $jb : $jb;
                  $img_count ++;
                }
                else {
                  $hit_limit = true;
                }
              }
              // PYRUS
              else if($analysis == 'Pyrus communis Genome v1.0 Draft Assembly & Annotation') {
                if ($img_count < 10) {
                  $jb = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/pyrus_communis_v1.0/?name=". $floc ."&type=gene_hybrid+mRNA_hybrid+gene_augustus+mRNA_augustus+NCBI_Sequence_Alignments\" width=500px>";
                  $gbrowse_imgs['pyrus_communis_v1.0'] = isset($gbrowse_imgs['pyrus_communis_v1.0']) ? $gbrowse_imgs['pyrus_communis_v1.0'] . $jb : $jb;
                  $img_count ++;
                }
                else {
                  $hit_limit = true;
                }
              }
              print $location;
            }
            /***
             * This is a direct alignment (e.g. gene models in a whole genome assembly. i.e. not a match)
             */
            else {
              $landmark = $alignment->record->srcfeature_id;
              $landmark = chado_expand_var($landmark, 'table', 'analysisfeature', array('return_array' => 1));
              $analysisfeatures = $landmark->analysisfeature;
              $analysisfeature = $analysisfeatures[0]; // We only check the first analysisfeature for alignment (This is most common)
              $analysis = isset($analysisfeature->analysis_id->name) ? trim($analysisfeature->analysis_id->name) : '';

              $floc = $alignment->name .":". ($alignment->fmin + 1) . ".." . $alignment->fmax;
              $srcfeature_id = $alignment->record->srcfeature_id->feature_id;
              $sql =
              "SELECT value
                FROM {feature} F
                INNER JOIN {analysisfeature} AF ON F.feature_id = AF.feature_id
                INNER JOIN {analysis} A ON A.analysis_id = AF.analysis_id
                INNER JOIN {analysisprop} AP ON AP.analysis_id = A.analysis_id
                INNER JOIN {cvterm} V ON V.cvterm_id = AP.type_id
                WHERE
                V.name = 'JBrowse URL' AND
                F.feature_id = :srcfeature_id";
              $jbrowse = $srcfeature_id ? chado_query($sql, array('srcfeature_id' => $srcfeature_id))->fetchField() : NULL;
              if ($jbrowse) {
                $location = '<a href="' . $jbrowse . $alignment->name .":". ($alignment->fmin + 1) . ".." . $alignment->fmax . '">' . $floc . $strand . '</a>';
              }
              else {
              $location = $floc . $strand; 
              }
              // PRUNUS
              if($analysis == 'Prunus persica Whole Genome v1.0 Assembly & Annotation') {
                $gbrowse_imgs['prunus_persica_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica?name=". $floc ."&type=Transcripts+Alternative_Transcripts+genes-v1.0-r1+NCBI_Sequence_Alignments+Repeat_Consensus+Markers+RosCOS+snp_Koepke_2012+IRSC_9K_peach_SNP_array+IRSC_6K_cherry_SNP_array+Davis_6K_peach_SNPs+All_Candidate_SNPs\" width=500px>";
              }
              else if($analysis == 'Prunus persica Whole Genome Assembly v2.0 & Annotation v2.1 (v2.0.a1)') {
                $gbrowse_imgs['prunus_persica_v2.0.a1'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/prunus_persica_v2.0.a1/?name=". $floc ."&type=Gene+Primary_Transcripts+Alternative_Transcripts\" width=500px>";
              }
              // MALUS
              else if($analysis == 'Malus x domestica Whole Genome v1.0 Assembly & Annotation') {
                $gbrowse_imgs['malus_x_domestica_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica?name=". $floc ."&type=cluster+contig+gene+gene2+repeat+IRSC_9K_apple_SNP_array+NCBI_Sequence_Alignments\" width=500px>";
              }
              else if($analysis == 'Malus x domestica Whole Genome v1.0p Assembly & Annotation') {
                $gbrowse_imgs['malus_x_domestica_v1.0p'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/malus_x_domestica_v1.0-primary?name=". $floc ."&type=scaffold+contig+gene+mRNA+RosCOS+NCBI_Sequence_Alignments\" width=500px>";
              }
              // FRAGARIA
              else if($analysis == 'Fragaria vesca Whole Genome v1.0 (build 8) Assembly & Annotation') {
                $gbrowse_imgs['fragaria_vesca_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.0-lg?name=". $floc ."&type=genemark_hybrid+genemark_hybrid_transcripts+genemark_abinitio+genemark_abinitio_transcripts+RosCOS\" width=500px>";
              }
              else if($analysis == 'Fragaria vesca Whole Genome v1.1 Assembly & Annotation') {
                $gbrowse_imgs['fragaria_vesca_v1.1'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/fragaria_vesca_v1.1-lg?name=". $floc ."&type=scaffold_alignments+genemark_hybrid+genemark_hybrid_transcripts+RosCos+NCBI_Sequence_Alignments\" width=500px>";
              }
              // PYRUS
              else if($analysis == 'Pyrus communis Genome v1.0 Draft Assembly & Annotation') {
                $gbrowse_imgs['pyrus_communis_v1.0'] = "<img style=\"width:100%\" border=0 src=\"https://www.rosaceae.org/gb/gbrowse_img/pyrus_communis_v1.0/?name=". $floc ."&type=gene_hybrid+mRNA_hybrid+gene_augustus+mRNA_augustus+NCBI_Sequence_Alignments\" width=500px>";
              }
              print $location;
            }?>
          </td>
          <td><?php print $analysis ?></td>
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

