<?php
$node = $variables['node'];
$project = $variables['node']->project;

// expand the project to include the properties.
$project = chado_expand_var($project,'table', 'projectprop', array('return_array' => 1));
$projectprops = $project->projectprop;
$properties = array();

foreach ($projectprops as $property) {
  $property = chado_expand_var($property,'field','projectprop.value');
  if ($property->value) {
    if ($property->type_id->name == 'filename') {
      $properties[$property->type_id->name] = "<a href=\"/bulk_data/www.rosaceae.org/genotype_snp/$property->value\">". $property->value . "</a>";
    } else if ($property->type_id->name != 'owner' && $property->type_id->name != 'permission') {
      $properties[$property->type_id->name] = $property->value;
    }
  }
}
asort($properties);
$ptype = isset($properties['project_type']) ? $properties['project_type'] : NULL;
$subtype = isset($properties['sub_type']) ? $properties['sub_type'] : NULL;
$data = NULL;
if ($ptype == 'genotyping' && $subtype == 'SSR') {
    $data = " [<a href=\"/search/ssr_genotype/summary?project_name=$project->name\">Browse Data</a>]";
} else if ($ptype == 'genotyping' && $subtype == 'SNP') {
    $data = " [<a href=\"/search/snp_genotype/summary?project_name=$project->name\">Browse Data</a>]";
}

// expand project to include pubs 
//$project = chado_expand_var($project, 'table', 'project_pub');
//$pubs = $project->project_pub;

// expand project to include contacts
//$project = chado_expand_var($project, 'table', 'project_contact');
//$contacts = $project->project_contact;

?>
<div id="tripal_project-base-box" class="tripal_project-info-box tripal-info-box">
  <div class="tripal_project-info-box-desc tripal-info-box-desc"></div>   

  <table id="tripal_project-table-base" class="tripal_project-table tripal-table tripal-table-vert">
    <tr class="tripal_project-table-even-row even">
      <th width="20%">Project Name</th>
      <td><?php print $project->name; if ($data) {print $data;}?></td>
    </tr><?php
      $counter = 0;
      foreach ($properties AS $p_key => $p_value) {
        $cls = $counter % 2 == 0 ? 'odd' : 'even'; ?>
        <tr class="tripal_project-table-<?php print $cls; ?>-row <?php print $cls; ?>">
            <th><?php print ucfirst(preg_replace('/_/', ' ', $p_key)); ?></th>
            <td><?php print $p_value; ?></td>
        </tr><?php
        $counter ++;
      }
    ?>
  </table> 
</div>
