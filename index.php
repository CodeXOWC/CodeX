<?php
/*
  Include functions.php file; assumes location in includes/ off of 
  root Web folder (e.g., htdocs/includes/)
*/
include("includes/functions.php");
/*
  Unique page content; rename items on the right between single quotes. Escape any single quotes
  or apostrophes you use with a slash (\). For example, 'Eric\'s Contact Information'
*/
$rpkpage = array(
  'page_title'        => 'Team Codex 2012 OWC Sandbox Site', /*Page title*/
  'share_description' => 'Dropbox-based prototyping', /*Share description*/
  'body_class'        => '' /*Add a class like 'home' to body; leave empty for no class*/
);

rpk_header($rpkpage);

?>
<!--This part is just plain XHTML:-->
  <div id="content">

    <div id="main">
      <h2>Content</h2>
      
    </div>

    <div id="supporting">
		<h3>Supporting</h3>
    </div>

  </div>

<!--
  Now PHP opens back up to output the navigation and footer
  as defined in /includes/functions.php
-->
<?php
rpk_navigation();
rpk_footer();
?>
