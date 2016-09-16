Project
--------------------
Mainlab Tripal is a module 


The Mainlab Tripal is created by Main Bioinformatics Lab (Main Lab) at Washington State 
University. Information about the Main Lab can be found at: https://www.bioinfo.wsu.edu
 
Requirement
--------------------
 - Drupal 7.x
 - Tripal 7.x-2.x

Version
--------------------
1.0.0

Download
--------------------
The MainLab Tripal module can be download from GitHub:
https://www.github.com/tripal/extension/mainlab_tripal

Installation
--------------------
After downloading the module, extract it into your site's module directory 
(e.g. sites/all/modules) then follow the instructions below:

1. Enable the module by usng the Drupal administrative interface: 
      Go to: Modules, check Mainlab Tripal (under the Mainlab category) and save 
    or by using the 'drush' command:
      drush pm-enable mainlab_tripal

Administration
--------------------
 - Enabling/Disabling a search:
   Go to: Mainlab > Mainlab Tripal and check on the templates you want to enable 
   i.e. http://your.site/admin/mainlab/mainlab_tripal
   
 - Overriding templates
    
Customization
--------------------
You can customize the search for your site by modifying the 'settings.conf' file, the search 
interface php file, and/or the materialized view definition php file. You can also create your 
own search by creating these files accordingly. 

Create New Templates (for Developers)
--------------------
             
Problems/Suggestions
--------------------
Mainlab Tripal is still under active development. For questions or bug report, please contact 
the developers at the Main Bioinformatics Lab by emailin to: dev@bioinfo.wsu.edu