<?php
$feature = $node->feature;
$feature = chado_expand_var($feature, 'table', 'feature_relationship', array ('return_array' => 1));

$object_rels = $feature->feature_relationship->object_id;
if (!is_array($object_rels)) {
  $object_rels = array($object_rels);
}

$function = array();
$product = array();
$note = array();
$evidence = array();
if (!is_array($object_rels)) {
  $object_rels = array($object_rels);
}
foreach ($object_rels as $rels){
  if ($rels->type_id->name == 'associated_with') {
    $rels = chado_expand_var($rels, 'table', 'featureprop');
    $fprop = $rels->subject_id->featureprop;
    if (is_array($fprop)) {
      foreach ($fprop AS $prop) {
        if ($prop->type_id->name == 'product') {
          if (!in_array($prop->value, $product)) {
            array_push ($product, $prop->value);
          }
        } elseif ($prop->type_id->name == 'function') {
          if (!in_array($prop->value, $function)) {
            array_push ($function, $prop->value);
          }
        } elseif ($prop->type_id->name == 'genbank_note') {
          if (!in_array($prop->value, $note)) {
            array_push ($note, $prop->value);
          }
        }
      }
    }
  }
}

// We want to display all synonyms and the product in one field as 'Synonym'
$feature = chado_expand_var($feature,'table','feature_synonym', array ('return_array' => 1));
if ($feature->feature_synonym) {
  foreach ($feature->feature_synonym AS $synonym) {
    if (!in_array($synonym->synonym_id->name,$product)) {
      array_push($product, $synonym->synonym_id->name);
    }
  }
}

// We want to display both 'function' and 'description' in one field as 'Description'
// We also want to display both 'note' and 'comment' in one field as 'Comment'
$feature = chado_expand_var($feature,'table','featureprop', array ('return_array' => 1));
foreach ($feature->featureprop AS $prop) {
  if (!in_array($prop->value,$function) && $prop->type_id->name == 'description') {
    array_push($function, $prop->value);
  }
  if (!in_array($prop->value,$note) && $prop->type_id->name == 'comment') {
    array_push($note, $prop->value);
  }
  if (!in_array($prop->value,$evidence) && $prop->type_id->name == 'evidence_for_feature') {
    $keys = array_keys($evidence); 
    if (count($keys) == 0 || $prop->rank < $keys[0]) { // Show only the evidence with smallest rank
      array_pop($evidence);
      $evidence[$prop->rank] = $prop->value;
    }
  }
}

// Format the values for display
$dis_function = count ($function) == 0 ? "NA" : implode ('<br>', $function);
$dis_product = count ($product) == 0 ? "NA" : implode ('<br>', $product);
$dis_note = count ($note) == 0 ? "NA" : implode ('<br>', $note);
$dis_evidence = count ($evidence) == 0 ? "NA" : implode ('<br>', $evidence);
?>
<div id="tripal_feature-base-box" class="tripal_feature-info-box tripal-info-box">
  <div class="tripal_feature-info-box-desc tripal-info-box-desc"></div>

   <?php if(strcmp($feature->is_obsolete,'t')==0){ ?>
      <div class="tripal_feature-obsolete">This feature is obsolete</div>
   <?php }?>
   <table id="tripal_feature-base-table" class="tripal_feature-table tripal-table tripal-table-vert">
      <tr class="tripal_feature-table-even-row even">
        <th width="250px">Name</th>
        <td><?php print $feature->name; ?></td>
      </tr>
      <tr class="tripal_feature-table-odd-row odd">
        <th nowrap>Gene Symbol</th>
        <td><?php print $feature->uniquename; ?></td>
      </tr>
      <tr class="tripal_feature-table-odd-row even">
        <th>Type</th>
        <td><?php print $feature->type_id->name; ?></td>
      </tr>
      <tr class="tripal_feature-table-even-row odd">
        <th>Organism</th>
        <td>
          <?php
          $link = mainlab_tripal_link_record('organism', $feature->organism_id->organism_id);
          if ($link) { 
           print "<a href=\"$link\">".$feature->organism_id->genus ." " . $feature->organism_id->species . "</a>";
          } else { 
            print $feature->organism_id->genus ." " . $feature->organism_id->species;
          } ?>
        </td>
       </tr>
      <tr class="tripal_feature-table-odd-row even">
        <th>Synonym</th>
        <td><?php print $dis_product; ?></td>
      </tr>
      <tr class="tripal_feature-table-even-row odd">
        <th>Description</th>
        <td><?php print $dis_function; ?></td>
      </tr>
            <tr class="tripal_feature-table-odd-row even">
        <th>Comment</th>
        <td><?php print $dis_note; ?></td>
      </tr>
      <tr class="tripal_feature-table-odd-row odd">
        <th>Evidence for the gene structure</th>
        <td><?php print $dis_evidence; ?></td>
      </tr>   
   </table>
</div>
