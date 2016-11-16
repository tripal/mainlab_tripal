# Mainlab Tripal Data Display
Mainlab Tripal Data Display contains a set of Drupal/PHP templates that organize and
 extend the default display of the biological data hosted on a Tripal-enabled site 
 (i.e. http://tripal.info). Supported data type includes orgainsm, marker, QTL, germplasm 
 (stock), map (featuremap), project, heritable phenotypic marker (MTL), environment 
 (ND geolocation), haplotype block, polymorphism, eimage, generic gene (genes created 
 by parsing Genbank files using the Mainlab 'tripal_genbank_parser' module), feature, and 
 pub. Each of the templates can be turned on/off as desired. The module supports 
 overriding built-in templates so site-specific customization is also supported (see 
 Overriding templates under the Administration section). To take full advantage of these 
 templates, data collecton templates and a loader (Mainlab Chado Loader, see 
https://github.com/tripal/mainlab_chado_loader/releases/latest) are also available 
as a separate module.

The Mainlab Tripal Data Display is created by Main Bioinformatics Lab (Main Lab) at 
Washington State University. Information about the Main Lab can be found at: 
https://www.bioinfo.wsu.edu
 
## Requirement
 - Drupal 7.x
 - Tripal 7.x-2.x

## Version
1.0.0

## Download
The Mainlab Tripal Data Display module can be downloaded from GitHub:

https://www.github.com/tripal/mainlab_tripal/releases/latest

## Installation
After downloading the module, extract it into your site's module directory 
(e.g. sites/all/modules) then follow the instructions below:

1. Enable the module either by 

  using the Drupal administrative interface: 
      Go to: Modules, check Mainlab Tripal (under the Mainlab category) and save 
  or using the 'drush' command:
  
  ```
  drush pm-enable mainlab_tripal
  ```

2. Enable the templates as desired by visiting 

      http://your.site/admin/mainlab/mainlab_tripal

Note: To get most out of these templates, please use the chado loader provided by the
Main Lab to load data.

## Administration
 - Enabling/Disabling a template:
   Go to: Mainlab > Mainlab Tripal and check on the templates you want to enable 
   i.e. http://your.site/admin/mainlab/mainlab_tripal
   
 - Overriding templates
   1. Create a subfolder in the module directory 'mainlab_tripal/theme'. For example,
       'mainlab_tripal/theme/custom_templates'
       
   2. Copy over any module-provided template (located in theme/template) or preprocessor
       (located in theme/preprocessors) you would like to modify to the 
       folder you created and make changes accordingly.
       
   3. Go to the Mainlab Tripal administrative interface 
       (i.e. http://your.site/admin/mainlab/mainlab_tripal) and enable overriding built-in 
       templates by selecting the custom folder under the 'Override Default Templates' 
       section. Save the configuration. Due to the Drupal's cache system, you will need to 
       visit this page to save the configuration every time you copy over a new template. 
       Otherwise the new template may not be used.
       
## Showcase
This module is used by many web sites hosted at Main Bioinformatics Lab. You can visit 
[GDR] (http://www.rosaceae.org), [CottonGen] (http://www.cottongen.org), or [CSFL]
(http://www.coolseasonfoodlegume.org) to see many of these templates in action. Please
note that the templates will only show data that are available so they may look different 
from pages on your site.

- [Organism]
   (https://www.rosaceae.org/organism/Rubus/occidentalis)

- [Marker]
   (https://www.rosaceae.org/node/3898850)

- [Polymorphism]
   (https://www.rosaceae.org/polymorphism/3409955)
  
- [Allele]
   (https://www.rosaceae.org/allele/GD147/117/28)

- [QTL]
   (https://www.rosaceae.org/node/3883616)
  
- [MTL]
   (https://www.rosaceae.org/node/1532116)

- [Germplasm]
   (https://www.rosaceae.org/node/1794344)
  
- [Map]
   (https://www.rosaceae.org/node/1539087)
 
- [Project]
   (https://www.rosaceae.org/node/4230961)

## Problems/Suggestions
Mainlab Tripal Data Display module is still under active development. For questions or bug 
report, please contact the developers at the Main Bioinformatics Lab by emailing to: 
dev@bioinfo.wsu.edu
