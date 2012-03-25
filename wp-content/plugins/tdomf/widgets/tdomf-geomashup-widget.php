<?php
/*
Name: "Geo Mashup Integration"
URI: http://thedeadone.net/software/tdo-mini-forms-wordpress-plugin/
Description: This widget integrates the Geo Mashup Plugin into TDOMF. Supports version 1.1.2.0 and 1.2beta1x.
Version: 2
Author: Mark Cunningham
Author URI: http://thedeadone.net
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('TDOMF: You are not allowed to call this page directly.'); }

if(class_exists('GeoMashup')) {

 /* BUG: There is a bug here I haven't resolved yet. It seems that in some 
  * themes the map won't show up if you use FF or IE (but will in Chrome). It 
  * seems to work fine all the time in "Your Submissions". With the beta
  * GeoMashup, the map won't appear in non-Mozilla posts but this is a 
  * GeoMashup bug, not a TDOMF one. */
    
 /** 
   * GeoMashup Integration Widget. Integrates with the GeoMashup plugin.
   * http://www.cyberhobo.net/downloads/geo-mashup-plugin/
   * 
   * Based on the work from hitekhomeless 
   * http://hitekhomeless.net/2008/12/tdomf-and-geo-mashup-harmony.html#geomashTDOMF
   * 
   * @author Mark Cunningham <tdomf@thedeadone.net> 
   * @version 2.0 
   * @since 0.13.0
   * @access public 
   * @copyright Mark Cunningham
   * 
   */ 
  class TDOMF_WidgetGeoMashup extends TDOMF_Widget
  {
      /** 
       * Initilise and start widget
       * 
       * @access public
       */ 
      function TDOMF_WidgetGeoMashup() {
          $this->enableHack();
          $this->enableValidate();
          $this->enablePost();
          $this->enableAdminEmail(false);
          $this->enableWidgetTitle();
          $this->enableControl(true,400,200);
          $this->setInternalName('geomashup');
          $this->setDisplayName(__('Geo Mashup','tdomf'));
          $this->setModes(array('new'));
          $this->start();
      }

      /**
       * What to display in form
       * 
       * @access public
       * @return String
       */
      function form($args,$options) {
          extract($args);
          $output = "";
     
          if(class_exists('GeoMashupDB')) {
              global $geo_mashup_options;
          
              /* latest version: 1.2beta1x */
              
              if($geo_mashup_options->get('overall', 'google_key')) {
                  
                  $link_url = get_bloginfo('wpurl').'/wp-content/plugins/geo-mashup';
    
                  # this includes the necessary scripts and other stuff for 
                  # geo-mashup to work
                  $output = '
                    <style type="text/css"> #geo_mashup_map div { margin:0; } </style>
                    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$geo_mashup_options->get('overall', 'google_key').'" type="text/javascript"></script>
                    <script src="'.$link_url.'/geo-mashup-admin.js" type="text/javascript"></script>
                    <script src="'.$link_url.'/JSONscriptRequest.js" type="text/javascript"></script>';
                  
                  if(!isset($geo_mashup_search)) { $geo_mashup_search = ""; }
                    
                  # Start with a blank location as we are not editing a post
                  
                  $location =  GeoMashupDB::blank_location( );
                  $post_location_name = $location->saved_name;
                  
                  # fill up the 'location' object with values set 
                  # pre-preview!
                  if(isset($geo_mashup_location)) {
                        list($lat,$lng) = explode(',',$geo_mashup_location);
                        $current_location = array( );
                        $current_location['lat'] = trim( $lat );
                        $current_location['lng'] = trim( $lng );
                        $current_location['saved_name'] = $geo_mashup_location_name;
                        $current_location['geoname'] = $geo_mashup_geoname;
                        $current_location['address'] = $geo_mashup_address;
                        $current_location['postal_code'] = $geo_mashup_postal_code;
                        $current_location['country_code'] = $geo_mashup_country_code;
                        $current_location['admin_code'] = $geo_mashup_admin_code;
                        $current_location['admin_name'] = $geo_mashup_admin_name;
                        $current_location['sub_admin_code'] = $geo_mashup_sub_admin_code;
                        $current_location['sub_admin_name'] = $geo_mashup_sub_admin_name;
                        $current_location['locality_name'] = $geo_mashup_locality_name;
                        $location = (object) $current_location;
                  }
                                    
                  # You can use kml files to specific location, but again
                  # not a feature we'll support
                  $kml_url = '';
                  
                  # Saved locations using json is copied and pasted from plugin, but
                  # is not supported
                  $saved_locations = GeoMashupDB::get_saved_locations( );
                  $locations_json = '{';
                  /*if ( !empty( $saved_locations ) ) {
                      $comma = '';
                      foreach ($saved_locations as $saved_location) {
                          $escaped_name = addslashes(str_replace(array("\r\n","\r","\n"),'',$saved_location->saved_name));
                          $locations_json .= $comma.'"'.$escaped_name.'":{"location_id":"'.$saved_location->id.'","name":"'.$escaped_name.
                          '","lat":"'.$saved_location->lat.'","lng":"'.$saved_location->lng.'"}';
                          $comma = ',';
                      }
                  }*/
                  $locations_json .= '}';
                  
                  # The code to display the map selection includes several DIVs that are 
                  # not wanted in TDOMF, so I simply hide them. Not the best solution
                  # but easiest way to integrated with GeoMashup
                  $output .= '
                  <img id="geo_mashup_status_icon" src="'.$link_url.'/images/idle_icon.gif" style="float:right" />
                  <label for="geo_mashup_search">'.__('Find location:', 'GeoMashup').'
                  <input id="geo_mashup_search" 
                         name="geo_mashup_search" 
                         type="text" 
                         size="35" 
                         value="'.$geo_mashup_search.'"
                         onfocus="this.select(); GeoMashupAdmin.map.checkResize();"
                         onkeypress="return GeoMashupAdmin.searchKey(event, this.value)" />
                   </label>
                   <select id="geo_mashup_select" name="geo_mashup_select" onchange="GeoMashupAdmin.onSelectChange(this);" style="display:none;">
                        <option>'.__('[Saved Locations]','GeoMashup').'</option>
                   </select>
                   <a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'block\'; return false;">'.__('Help', 'GeoMashup').'</a>
                   <div id="geo_mashup_inline_help" style="padding:5px; border:2px solid blue; background-color:#ffc; display:none;">
                       <p>'.__('Put a green pin at the location for this post.', 'tdomf').' '.__('There are many ways to do it:', 'tdomf').'
                       <ul>
                        <li>'.__('Search for a location name.', 'tdomf').'</li>
                        <li>'.__('For multiple search results, mouse over pins to see location names, and click a result pin to select that location.', 'tdomf').'</li>
                        <li>'.__('Search for a decimal latitude and longitude, like <em>40.123,-105.456</em>.', 'tdomf').'</li> 
                        <li>'.__('Search for a street address, like <em>123 main st, anytown, acity</em>.', 'tdomf').'</li>
                        <li>'.__('Click on the location. Zoom in if necessary so you can refine the location by dragging it or clicking a new location.', 'tdomf').'</li>
                       </ul>
                       '.__('To execute a search, type search text into the Find Location box and hit the enter key.', 'tdomf').'</p>
                       <p>'.__('To remove the location (green pin) for a post, clear the search box and hit the enter key.', 'tdomf').'</p>
                       <p><a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'none\'; return false;">'.__('Close', 'tdomf').'</a>
                   </div>
                   <div id="geo_mashup_map" style="width:400px;height:300px;">
                       '.__('Loading Google map. Check Geo Mashup options if the map fails to load.', 'tdomf').'
                   </div>
                   <script type="text/javascript">
                    //<![CDATA[
                    GeoMashupAdmin.registerMap(document.getElementById("geo_mashup_map"),
                            {"link_url":"'.$link_url.'",
                             "post_lat":"'.$location->lat.'",
                             "post_lng":"'.$location->lng.'",
                             "post_location_name":"'.$post_location_name.'",
                             "saved_locations":'.$locations_json.',
                             "kml_url":"'.$kml_url.'",
                             "status_icon":document.getElementById("geo_mashup_status_icon")});
                     // ]]>
                   </script>
                   <label for="geo_mashup_location_name" style="display:none;">'.__('Save As:', 'GeoMashup').'
                        <input id="geo_mashup_location_name" name="geo_mashup_location_name" type="text" maxlength="50" size="45" style="display:none;"/>
                   </label>
                   <input id="geo_mashup_location" name="geo_mashup_location" type="hidden" value="'.$location->lat.','.$location->lng.'" />
                   <input id="geo_mashup_location_id" name="geo_mashup_location_id" type="hidden" value="'.$location->id.'" />
                   <input id="geo_mashup_geoname" name="geo_mashup_geoname" type="hidden" value="'.$location->geoname.'" />
                   <input id="geo_mashup_address" name="geo_mashup_address" type="hidden" value="'.$location->address.'" />
                   <input id="geo_mashup_postal_code" name="geo_mashup_postal_code" type="hidden" value="'.$location->postal_code.'" />
                   <input id="geo_mashup_country_code" name="geo_mashup_country_code" type="hidden" value="'.$location->country_code.'" />
                   <input id="geo_mashup_admin_code" name="geo_mashup_admin_code" type="hidden" value="'.$location->admin_code.'" />
                   <input id="geo_mashup_admin_name" name="geo_mashup_admin_name" type="hidden" value="'.$location->admin_name.'" />
                   <input id="geo_mashup_sub_admin_code" name="geo_mashup_sub_admin_code" type="hidden" value="'.$location->sub_admin_code.'" />
                   <input id="geo_mashup_sub_admin_name" name="geo_mashup_sub_admin_name" type="hidden" value="'.$location->sub_admin_name.'" />
                   <input id="geo_mashup_locality_name" name="geo_mashup_locality_name" type="hidden" value="'.$location->locality_name.'" />
                   <input id="geo_mashup_changed" name="geo_mashup_changed" type="hidden" value="" />';
                  
              } else {
                  $output = __("<p>You must configure Geo Mashup before you can use it in TDO-Mini-Forms</p>","tdomf");
              }
              
          } else {
              
              /* old version: 1.1.2.0 */
              
              $geomashupOptions = get_settings('geo_mashup_options');
              if(!is_array($geomashupOptions)) {
                $geomashupOption = GeoMashup::default_options();
              }
              if($geomashupOptions['google_key']) {
                
                $link_url = get_bloginfo('wpurl').'/wp-content/plugins/geo-mashup';
                $output = '
                    <style type="text/css"> #geo_mashup_map div { margin:0; } </style>
                    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$geomashupOptions['google_key'].'" type="text/javascript"></script>
                    <script src="'.$link_url.'/geo-mashup-admin.js" type="text/javascript"></script>
                    <script src="'.$link_url.'/JSONscriptRequest.js" type="text/javascript"></script>
                    <!-- tdomf-geomashup-widget 1.0 -->';
                    
                if(!isset($geo_mashup_search)) { $geo_mashup_search = ""; }
                    
                # Current lat/lng                 
                $post_lat =  '';
                $post_lng = '';
                if(!isset($geo_mashup_location)) {
                    $geo_locations = get_settings('geo_locations');
                    list($post_lat,$post_lng) = split(',',$geo_locations['default']);
                } else {
                    list($post_lat,$post_lng) = split(',',$geo_mashup_location);
                }
                
                # This is normally used to "save" a location, but thats not a 
                # feature we'll support
                $post_location_name = '';
    
                # You can use kml files to specific location, but again
                # not a feature we'll support
                $kml_url = '';
    
                # Locations using json is copied and pasted from plugin, but
                # is not supported
                $locations_json = '{';
                if (is_array($geo_locations)) {
                    $comma = '';
                    foreach ($geo_locations as $name => $latlng) {
                        list($lat,$lng) = split(',',$latlng);
                        $escaped_name = addslashes(str_replace(array("\r\n","\r","\n"),'',$name));
                        if ($lat==$post_lat && $lng==$post_lng) {
                            $post_location_name = $escaped_name;
                        }
                        $locations_json .= $comma.'"'.addslashes($name).'":{"name":"'.$escaped_name.'","lat":"'.$lat.'","lng":"'.$lng.'"}';
                        $comma = ',';
                    }
                }
                $locations_json .= '}';
                            
                # The code to display the map selection includes several DIVs that are 
                # not wanted in TDOMF, so I simply hide them. Not the best solution
                # but easiest way to integrated with GeoMashup
                $output .= '
                <img id="geo_mashup_status_icon" src="'.$link_url.'/images/idle_icon.gif" style="float:right" />
                <label for="geo_mashup_search">'.__('Find location:', 'tdomf').'
                <input id="geo_mashup_search" 
                    name="geo_mashup_search" 
                    type="text" 
                    size="35" 
                    value="'.$geo_mashup_search.'"
                    onfocus="this.select(); GeoMashupAdmin.map.checkResize();"
                    onkeypress="return GeoMashupAdmin.searchKey(event, this.value);" />
                </label>
                <select id="geo_mashup_select" name="geo_mashup_select" onchange="GeoMashupAdmin.onSelectChange(this); " style="display:none;" >
                    <option>'.__('[Saved Locations]','GeoMashup').'</option>
                </select>            
                <a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'block\'; return false;">'.__('help', 'tdomf').'</a>
                <div id="geo_mashup_inline_help" style="padding:5px; border:2px solid blue; background-color:#ffc; display:none;">
                    <p>'.__('Put a green pin at the location for this post.', 'tdomf').' '.__('There are many ways to do it:', 'tdomf').'
                    <ul>
                        <li>'.__('Search for a location name.', 'tdomf').'</li>
                        <li>'.__('For multiple search results, mouse over pins to see location names, and click a result pin to select that location.', 'tdomf').'</li>
                        <li>'.__('Search for a decimal latitude and longitude, like <em>40.123,-105.456</em>.', 'tdomf').'</li> 
                        <li>'.__('Search for a street address, like <em>123 main st, anytown, acity</em>.', 'tdomf').'</li>
                        <li>'.__('Click on the location. Zoom in if necessary so you can refine the location by dragging it or clicking a new location.', 'tdomf').'</li>
                    </ul>
                    '.__('To execute a search, type search text into the Find Location box and hit the enter key.', 'tdomf').'</p>
                    <p>'.__('To remove the location (green pin) for a post, clear the search box and hit the enter key.', 'tdomf').'</p>
                    <p><a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'none\'; return false;">'.__('close', 'tdomf').'</a>
                </div>
                <div id="geo_mashup_map" style="width:400px;height:300px;" >
                    '.__('Loading Google map. Check Geo Mashup options if the map fails to load.', 'tdomf').'
                </div>
                <script type="text/javascript">//<![CDATA[
                    GeoMashupAdmin.registerMap(document.getElementById("geo_mashup_map"),
                        {"link_url":"'.$link_url.'",
                        "post_lat":"'.$post_lat.'",
                        "post_lng":"'.$post_lng.'",
                        "post_location_name":"'.$post_location_name.'",
                        "saved_locations":'.$locations_json.',
                        "kml_url":"'.$kml_url.'",
                        "status_icon":document.getElementById("geo_mashup_status_icon")});
                // ]]>
                </script>
                <label for="geo_mashup_location_name" style="display:none;">'.__('Save As:', 'tdomf').'
                    <input id="geo_mashup_location_name" name="geo_mashup_location_name" type="text" size="45" style="display:none;" />
                </label>
                <input id="geo_mashup_location" name="geo_mashup_location" type="hidden" value="'.$post_lat.','.$post_lng.'" />';
                   
              } else {
                  $output = __("<p>You must configure Geo Mashup before you can use it in TDO-Mini-Forms</p>","tdomf");
              }
          }
            
          return $output;
      }
      
      /**
       * Code for hacking form output
       * 
       * @access public
       * @return String
       */
      function formHack($args,$options) {
          extract($args);
          $output = "";
     
          if(class_exists('GeoMashupDB')) {
              global $geo_mashup_options;
          
              /* latest version: 1.2beta1x */
              if($geo_mashup_options->get('overall', 'google_key')) {
                  
                  $link_url = get_bloginfo('wpurl').'/wp-content/plugins/geo-mashup';
    
                  $output = '
                  <style type="text/css"> #geo_mashup_map div { margin:0; } </style>
                  <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$geo_mashup_options->get('overall', 'google_key').'" type="text/javascript"></script>
                  <script src="'.$link_url.'/geo-mashup-admin.js" type="text/javascript"></script>
                  <script src="'.$link_url.'/JSONscriptRequest.js" type="text/javascript"></script>';
                  

                  $output .= '
                  <?php               
                  $location =  GeoMashupDB::blank_location( );
                  $post_location_name = $location->saved_name;

                  if(isset($geo_mashup_location)) {
                        list($lat,$lng) = split(\',\',$geo_mashup_location);
                        $current_location = array( );
                        $current_location[\'lat\'] = trim( $lat );
                        $current_location[\'lng\'] = trim( $lng );
                        $current_location[\'saved_name\'] = $geo_mashup_location_name;
                        $current_location[\'geoname\'] = $geo_mashup_geoname;
                        $current_location[\'address\'] = $geo_mashup_address;
                        $current_location[\'postal_code\'] = $geo_mashup_postal_code;
                        $current_location[\'country_code\'] = $geo_mashup_country_code;
                        $current_location[\'admin_code\'] = $geo_mashup_admin_code;
                        $current_location[\'admin_name\'] = $geo_mashup_admin_name;
                        $current_location[\'sub_admin_code\'] = $geo_mashup_sub_admin_code;
                        $current_location[\'sub_admin_name\'] = $geo_mashup_sub_admin_name;
                        $current_location[\'locality_name\'] = $geo_mashup_locality_name;
                        $location = (object) $current_location;
                  } ?>';
                                    
                  $kml_url = '';
                  $saved_locations = GeoMashupDB::get_saved_locations( );
                  $locations_json = '{';
                  /* don't need */
                  $locations_json .= '}';
                  
                  $output .= '
                  <img id="geo_mashup_status_icon" src="'.$link_url.'/images/idle_icon.gif" style="float:right" />
                  <label for="geo_mashup_search">'.__('Find location:', 'GeoMashup').'
                  <input id="geo_mashup_search" 
                         name="geo_mashup_search" 
                         type="text" 
                         size="35" 
                         value="<?php echo $geo_mashup_search; ?>"
                         onfocus="this.select(); GeoMashupAdmin.map.checkResize();"
                         onkeypress="return GeoMashupAdmin.searchKey(event, this.value)" />
                   </label>
                   <select id="geo_mashup_select" name="geo_mashup_select" onchange="GeoMashupAdmin.onSelectChange(this);" style="display:none;">
                        <option>'.__('[Saved Locations]','GeoMashup').'</option>
                   </select>
                   <a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'block\'; return false;">'.__('Help', 'GeoMashup').'</a>
                   <div id="geo_mashup_inline_help" style="padding:5px; border:2px solid blue; background-color:#ffc; display:none;">
                       <p>'.__('Put a green pin at the location for this post.', 'tdomf').' '.__('There are many ways to do it:', 'tdomf').'
                       <ul>
                        <li>'.__('Search for a location name.', 'tdomf').'</li>
                        <li>'.__('For multiple search results, mouse over pins to see location names, and click a result pin to select that location.', 'tdomf').'</li>
                        <li>'.__('Search for a decimal latitude and longitude, like <em>40.123,-105.456</em>.', 'tdomf').'</li> 
                        <li>'.__('Search for a street address, like <em>123 main st, anytown, acity</em>.', 'tdomf').'</li>
                        <li>'.__('Click on the location. Zoom in if necessary so you can refine the location by dragging it or clicking a new location.', 'tdomf').'</li>
                       </ul>
                       '.__('To execute a search, type search text into the Find Location box and hit the enter key.', 'tdomf').'</p>
                       <p>'.__('To remove the location (green pin) for a post, clear the search box and hit the enter key.', 'tdomf').'</p>
                       <p><a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'none\'; return false;">'.__('Close', 'tdomf').'</a>
                   </div>
                   <div id="geo_mashup_map" style="width:400px;height:300px;">
                       '.__('Loading Google map. Check Geo Mashup options if the map fails to load.', 'tdomf').'
                   </div>
                   <script type="text/javascript">
                    //<![CDATA[
                    GeoMashupAdmin.registerMap(document.getElementById("geo_mashup_map"),
                            {"link_url":"'.$link_url.'",
                             "post_lat":"<?php echo $location->lat; ?>",
                             "post_lng":"<?php echo $location->lng; ?>",
                             "post_location_name":"<?php echo $post_location_name; ?>",
                             "saved_locations":'.$locations_json.',
                             "kml_url":"'.$kml_url.'",
                             "status_icon":document.getElementById("geo_mashup_status_icon")});
                     // ]]>
                   </script>
                   <label for="geo_mashup_location_name" style="display:none;">'.__('Save As:', 'GeoMashup').'
                        <input id="geo_mashup_location_name" name="geo_mashup_location_name" type="text" maxlength="50" size="45" style="display:none;"/>
                   </label>
                   <input id="geo_mashup_location" name="geo_mashup_location" type="hidden" value="<?php echo $location->lat; ?>,<?php echo $location->lng; ?>" />
                   <input id="geo_mashup_location_id" name="geo_mashup_location_id" type="hidden" value="<?php echo $location->id; ?>" />
                   <input id="geo_mashup_geoname" name="geo_mashup_geoname" type="hidden" value="<?php echo $location->geoname; ?>" />
                   <input id="geo_mashup_address" name="geo_mashup_address" type="hidden" value="<?php echo $location->address; ?>" />
                   <input id="geo_mashup_postal_code" name="geo_mashup_postal_code" type="hidden" value="<?php echo $location->postal_code; ?>" />
                   <input id="geo_mashup_country_code" name="geo_mashup_country_code" type="hidden" value="<?php echo $location->country_code; ?>" />
                   <input id="geo_mashup_admin_code" name="geo_mashup_admin_code" type="hidden" value="<?php echo $location->admin_code; ?>" />
                   <input id="geo_mashup_admin_name" name="geo_mashup_admin_name" type="hidden" value="<?php echo $location->admin_name; ?>" />
                   <input id="geo_mashup_sub_admin_code" name="geo_mashup_sub_admin_code" type="hidden" value="<?php echo $location->sub_admin_code; ?>" />
                   <input id="geo_mashup_sub_admin_name" name="geo_mashup_sub_admin_name" type="hidden" value="<?php echo $location->sub_admin_name; ?>" />
                   <input id="geo_mashup_locality_name" name="geo_mashup_locality_name" type="hidden" value="<?php echo $location->locality_name; ?>" />
                   <input id="geo_mashup_changed" name="geo_mashup_changed" type="hidden" value="" />';
                  
              } else {
                  $output = __("<p>You must configure Geo Mashup before you can use it in TDO-Mini-Forms</p>","tdomf");
              }
          } else { 
          
              /* old version: 1.1.2.0 */
              
              $geomashupOptions = get_settings('geo_mashup_options');
              if(!is_array($geomashupOptions)) {
                $geomashupOption = GeoMashup::default_options();
              }
            
              if($geomashupOptions['google_key']) {
                $link_url = get_bloginfo('wpurl').'/wp-content/plugins/geo-mashup';
                $output = '
                    <style type="text/css"> #geo_mashup_map div { margin:0; } </style>
                    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$geomashupOptions['google_key'].'" type="text/javascript"></script>
                    <script src="'.$link_url.'/geo-mashup-admin.js" type="text/javascript"></script>
                    <script src="'.$link_url.'/JSONscriptRequest.js" type="text/javascript"></script>';
    
                if(!isset($geo_mashup_search)) { $geo_mashup_search = ""; }
                
                $output .= '<?php 
                   $post_lat = \'\';
                   $post_lng = \'\';
                   if(!isset($geo_mashup_location)) {
                       $geo_locations = get_settings(\'geo_locations\');
                       list($post_lat,$post_lng) = split(\',\',$geo_locations[\'default\']);
                   } else {
                       list($post_lat,$post_lng) = split(\',\',$geo_mashup_location);
                   }
                ?>';
                
                $post_location_name = '';
                $kml_url = '';
    
                # We'll generate this once for the form hacker...
                $locations_json = '{';
                if (is_array($geo_locations)) {
                    $comma = '';
                    foreach ($geo_locations as $name => $latlng) {
                        list($lat,$lng) = split(',',$latlng);
                        $escaped_name = addslashes(str_replace(array("\r\n","\r","\n"),'',$name));
                        if ($lat==$post_lat && $lng==$post_lng) {
                            $post_location_name = $escaped_name;
                        }
                        $locations_json .= $comma.'"'.addslashes($name).'":{"name":"'.$escaped_name.'","lat":"'.$lat.'","lng":"'.$lng.'"}';
                        $comma = ',';
                    }
                }
                $locations_json .= '}';
                            
                $output .= '
                <img id="geo_mashup_status_icon" src="'.$link_url.'/images/idle_icon.gif" style="float:right" />
                <label for="geo_mashup_search">'.__('Find location:', 'tdomf').'
                <input id="geo_mashup_search" 
                    name="geo_mashup_search" 
                    type="text" 
                    size="35" 
                    value="<?php echo $geo_mashup_search; ?>"
                    onfocus="this.select(); GeoMashupAdmin.map.checkResize();"
                    onkeypress="return GeoMashupAdmin.searchKey(event, this.value);" />
                </label>
                <select id="geo_mashup_select" name="geo_mashup_select" onchange="GeoMashupAdmin.onSelectChange(this); " style="display:none;" >
                    <option>'.__('[Saved Locations]','GeoMashup').'</option>
                </select>            
                <a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'block\'; return false;">'.__('help', 'tdomf').'</a>
                <div id="geo_mashup_inline_help" style="padding:5px; border:2px solid blue; background-color:#ffc; display:none;">
                    <p>'.__('Put a green pin at the location for this post.', 'tdomf').' '.__('There are many ways to do it:', 'tdomf').'
                    <ul>
                        <li>'.__('Search for a location name.', 'tdomf').'</li>
                        <li>'.__('For multiple search results, mouse over pins to see location names, and click a result pin to select that location.', 'tdomf').'</li>
                        <li>'.__('Search for a decimal latitude and longitude, like <em>40.123,-105.456</em>.', 'tdomf').'</li> 
                        <li>'.__('Search for a street address, like <em>123 main st, anytown, acity</em>.', 'tdomf').'</li>
                        <li>'.__('Click on the location. Zoom in if necessary so you can refine the location by dragging it or clicking a new location.', 'tdomf').'</li>
                    </ul>
                    '.__('To execute a search, type search text into the Find Location box and hit the enter key.', 'tdomf').'</p>
                    <p>'.__('To remove the location (green pin) for a post, clear the search box and hit the enter key.', 'tdomf').'</p>
                    <p><a href="#" onclick="document.getElementById(\'geo_mashup_inline_help\').style.display=\'none\'; return false;">'.__('close', 'tdomf').'</a>
                </div>
                <div id="geo_mashup_map" style="width:400px;height:300px;" class="clear">
                    '.__('Loading Google map. Check Geo Mashup options if the map fails to load.', 'tdomf').'
                </div>
                <script type="text/javascript">//<![CDATA[
                    GeoMashupAdmin.registerMap(document.getElementById("geo_mashup_map"),
                        {"link_url":"'.$link_url.'",
                        "post_lat":"<?php echo $post_lat; ?>",
                        "post_lng":"<?php echo $post_lng; ?>",
                        "post_location_name":"'.$post_location_name.'",
                        "saved_locations":'.$locations_json.',
                        "kml_url":"'.$kml_url.'",
                        "status_icon":document.getElementById("geo_mashup_status_icon")});
                // ]]>
                </script>
                <label for="geo_mashup_location_name" style="display:none;">'.__('Save As:', 'tdomf').'
                    <input id="geo_mashup_location_name" name="geo_mashup_location_name" type="text" size="45" style="display:none;" />
                </label>
                <input id="geo_mashup_location" name="geo_mashup_location" type="hidden" value="<?php echo $post_lat; ?>,<?php echo $post_lng; ?>" />';
              } else {
                  $output = __("<p>You must configure Geo Mashup before you can use it in TDO-Mini-Forms</p>","tdomf");
              }
          
          }
          
          return $output;
      }

      /**
       * Process form input for widget
       * 
       * @access public
       * @return Mixed
       */
      function post($args,$options) {
          extract($args);     

          if(isset($geo_mashup_location)) {
               $geo_mashup_location = trim($geo_mashup_location);
          }
          
          if (!isset($geo_mashup_location) || ($options['required'] && (empty($geo_mashup_location) || $geo_mashup_location == ','))) {
                  return __("You must specific a location using the map!","tdomf");
          }
          
          if(!empty($geo_mashup_location) && $geo_mashup_location != ',' ) {
              
              if(class_exists('GeoMashupDB')) {
                        
                  /* latest version: 1.2beta1x */
              
                  if ( !empty( $args['geo_mashup_location_id'] ) ) {
                    $ok = GeoMashupDB::set_post_location( $post_ID, $geo_mashup_location_id );
                  } else {
                    list( $lat, $lng ) = split( ',', $geo_mashup_location );
                    $post_location = array( );
                    $post_location['lat'] = trim( $lat );
                    $post_location['lng'] = trim( $lng );
                    $post_location['saved_name'] = $geo_mashup_location_name;
                    $post_location['geoname'] = $geo_mashup_geoname;
                    $post_location['address'] = $geo_mashup_address;
                    $post_location['postal_code'] = $geo_mashup_postal_code;
                    $post_location['country_code'] = $geo_mashup_country_code;
                    $post_location['admin_code'] = $geo_mashup_admin_code;
                    $post_location['admin_name'] = $geo_mashup_admin_name;
                    $post_location['sub_admin_code'] = $geo_mashup_sub_admin_code;
                    $post_location['sub_admin_name'] = $geo_mashup_sub_admin_name;
                    $post_location['locality_name'] = $geo_mashup_locality_name;
                    $ok = GeoMashupDB::set_post_location( $post_ID, $post_location );
                  }
                  if(!$ok) { return __('GeoMashup reported an error when attempting to process location','tdomf'); }

              } else {
              
                  /* old version: 1.1.2.0 */
                  
                  add_post_meta($post_ID, '_geo_location', $geo_mashup_location);
              }
              
              if($options['append']) {
                  $post = wp_get_single_post($post_ID, ARRAY_A);
                  if(!empty($post['post_content'])) {
                      $post = add_magic_quotes($post);
                  }
                  $postdata = array (
                      "ID"                      => $post_ID,
                      "post_content"            => $post['post_content'].'[geo_mashup_map]',
                      );
                  sanitize_post($postdata,"db");
                  wp_update_post($postdata);
              }
          
          }
          
          return NULL;
      }
      
      /**
       * Validate widget input
       * 
       * @access public
       * @return Mixed
       */
      function validate($args,$options,$preview) {
          extract($args);
          
          if ($options['required'] && (empty($geo_mashup_location) || $geo_mashup_location == ',' ) ) {
                  return __("You must specific a location using the map!","tdomf");
          }
          
          return "";
      }
   
      /**
       * Append summary of widget data in email to admin
       * 
       * @access public
       * @return String
       */
      function adminEmail($args,$options,$post_ID) {
          extract($args);
          $permalink = get_permalink($post_ID);
          if(!empty($permalink)) {
              return __("Permalink: ","tdomf") . $permalink;
          }
          return "";
      }

      /**
       * Configuration panel for widget
       * 
       * @access public
       */      
      function control($options,$form_id) {
          
          // Store settings for this widget
          //
          if ( $_POST[$this->internalName.'-submit'] ) {
              $newoptions['required'] = isset($_POST[$this->internalName.'-required']);
              $newoptions['append'] = isset($_POST[$this->internalName.'-append']);
              $options = wp_parse_args($newoptions, $options);
              $this->updateOptions($options,$form_id);
          }

          // Display control panel for this widget
          //
          extract($options);

          ?>
<div>

          <?php $this->controlCommon($options); ?>

<input type="checkbox" name="geomashup-required" id="geomashup-required" <?php if($options['required']) echo "checked"; ?> >
<label for="permalink-required" style="line-height:35px;"><?php _e("The submitter must supply a location","tdomf"); ?></label>
<br/>

<input type="checkbox" name="geomashup-append" id="geomashup-append" <?php if($options['append']) echo "checked"; ?> >
<label for="permalink-append" style="line-height:35px;"><?php _e("Append a map of the location to the submission","tdomf"); ?></label>
<br/>

</div>

<?php
      }

      /**
       * Overrides "getOptions" with defaults for this widget
       * 
       * @access public
       * @return String
       */
      function getOptions($form_id) {
          $defaults = array( 'required' => false, 'append' => true );
          $options = TDOMF_Widget::getOptions($form_id);
          $options = wp_parse_args($options, $defaults);
          return $options;
      }
  }
  
  // Create and start the widget
  //
  global $tdomf_widget_geomashup;
  $tdomf_widget_geomashup = new TDOMF_WidgetGeoMashup();
  
}
  
?>