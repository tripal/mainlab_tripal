<?php
$feature = $variables['node']->feature;
$feature = chado_expand_var($feature, 'field', 'feature.residues');
$residues = $feature->residues; 

if ($residues) { ?>
  <div id="tripal_feature-sequence-box" class="tripal_feature-info-box tripal-info-box">
    <?php 
    $num_bases = 50;
    $sequences_html = '<pre class="tripal_feature-sequence">';
    $sequences_html .= '>' . tripal_get_fasta_defline($feature, '', NULL, '', strlen($residues)) . "<br>";
    $sequences_html .= wordwrap($residues, $num_bases, "<br>", TRUE);
    $sequences_html .= '</pre>';
    print $sequences_html;
    ?>
  </div>
<?php } ?>