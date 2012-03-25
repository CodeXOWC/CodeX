<?php
/**
 * Constant Contact PHP Class
 *
 * @package        wordpress
 * @subackage    constant-contact-api
 */
// $Id$
/**
 * @file
 */
class cc {


    /**
     * The user-agent header to send with all API requests
     *
     * @access     public
     */
    var $http_user_agent = 'justphp 2.0';

    /**
     * The developers API key which is associated with the application
     * PLEASE DO NOT CHANGE THIS API KEY
     */
    var $api_key = '7cdd0bae-371d-4603-85f4-90be4757a7c7';

    /**
     * The API username which is passed to the constructor, always required
     *
     * @access     public
     */
    var $api_username = '';

    /**
     * The API password which is passed to the constructor, always required
     *
     * @access     public
     */
    var $api_password = '';

    /**
     * The URL to use for all API calls, DO NOT INCLUDE A TRAILING SLASH!
     *
     * @access     public
     */
    var $api_url = 'https://api.constantcontact.com';

    /**
     * This will be constructed automatically, same as above without the full URL
     *
     * @access     protected
     */
    var $api_uri = '';

    /**
     * The last error message, can be used to provide a descriptive error if something goes wrong
     *
     * @access     public
     */
    var $last_error = '';

    /**
     * The action type used for API calls, action by customer or contact, important!
     * If you abuse this setting it violates the terms of the API
     * Do not edit this property directly, instead use the @see set_action_type() method
     *
     * @access     protected
     */
    var $action_type = 'ACTION_BY_CUSTOMER';

    /**
     * Meta data relating to the last call to the get_lists method
     *
     * @access     public
     */
    var $list_meta_data;

    /**
     * Meta data relating to the last call to the get_list_members method
     *
     * @access     public
     */
    var $member_meta_data;

    /**
     * The HTTP host used for the API
     * This will be just the hostname of the API
     *
     * @access protected
     */
    var $http_host;

    /**
     * The HTTP port used for the API
     * This will be port 443 if using HTTPS or 80 is using HTTP
     *
     * @access protected
     */
    var $http_port;

    /**
     * The results from a call to the PHP function @see parse_url()
     * Contains an array of all the URL bits parsed by @see parse_url()
     *
     * @access protected
     */
    var $http_url_bits;

    /**
     * HTTP request timeout in seconds
     * This can be changed to any number you want
     *
     * @access public
     */
    var $http_request_timeout = 120;

    /**
     * Username used for HTTP authentication
     * It contains the string used to authenticate
     * This will be built automatically from the API key and username
     *
     * @access protected
     */
    var $http_user;

    /**
     * Password used for HTTP authentication
     *
     * @access protected
     */
    var $http_pass;

    /**
     * The Content-Type header to use for all HTTP requests
     * Do not edit this directly instead use the @see set_content_type() method
     *
     * @access protected
     */
    var $http_content_type;

    /**
     * The default Content-Type header to use for all HTTP requests
     * If no Content-Type header is set this is the default
     *
     * @access protected
     */
    var $http_default_content_type = 'text/html';

    /**
     * The HTTP response code of the last HTTP request
     *
     * @access protected
     */
    var $http_response_code;

    /**
     * The full HTTP response of the last HTTP request
     *
     * @access protected
     */
    var $http_response;

    /**
     * The HTTP response body of the last HTTP request
     *
     * @access protected
     */
    var $http_response_body;

    /**
     * The full HTTP request body of the last HTTP request
     *
     * @access protected
     */
    var $http_request;

    /**
     * The method to use for the HTTP request
     *
     * @access protected
     */
    var $http_method;

    /**
     * The line break used to separate HTTP headers
     *
     * @access public
     */
    var $http_linebreak = "\r\n";

    /**
     * The HTTP requests headers, use @see http_headers_add() to add individual headers
     *
     * @access protected
     */
    var $http_request_headers = array();

    /**
     * The HTTP response headers
     *
     * @access protected
     */
    var $http_response_headers = array();

    /**
     * A list of encodings we support for the XML file
     *
     * @access public
     */
    var $xml_known_encodings = array('UTF-8', 'US-ASCII', 'ISO-8859-1');



    /**
     * Constructor method
     * Sets default params
     * Constructs the correct API URL
     * Sets variables for the http_* methods
     * If you want to change the APi key use the @see set_api_key() method
     *
     * @param string     The username for your Constant Contact account
     * @param string     The password for your Constant Contact account
     * @param string     The API key for your Constant Contact developer account (deprecated)
     *
     * @access     public
     */
    function cc($api_username, $api_password)
    {
    	$this->api_username = $api_username;
        $this->api_password = $api_password;

        $this->api_url .= '/ws/customers/' . rawurlencode($api_username) . '/';
        $this->api_uri .= '/ws/customers/' . urlencode($api_username) . '/';

        $this->http_user = $this->api_key . "%" . $api_username;
        $this->http_pass = $api_password;
        $this->http_set_content_type($this->http_default_content_type);
    }


    /**
     * IMPORTANT!
     * This method sets the action type
     * If a user performs the action themselves you should call this method with the param 'contact'
     * This triggers the welcome email if a new subscriber
     * You may get banned if you use this setting incorrectly!
     * The default action is ACTION_BY_CUSTOMER meaning the constant contact account owner made the action themselves, this can typically be associated with an admin subscribers users or updating a users details
     * If you have a signup form on your website you should set this method to ACTION_BY_CONTACT
     * Call this method before you subscribe a user or perform any other action they make and again to reset it
     * eg. $cc->set_action_type('contact');
     *
     *
     * @access     public
     */
    function set_action_type($action_type='customer')
    {
        $this->action_type = (strtolower($action_type)=='customer') ? 'ACTION_BY_CUSTOMER' : 'ACTION_BY_CONTACT';
    }

    /**
     * This method does a print_r on the http_request and http_response variables
     * Useful for debugging the HTTP request
     *
     * @access     public
     */
    function show_last_connection()
    {
        print_r($this->http_request);
        print_r($this->http_response);
    }

    /**
     * Shows the last request
     *
     * @access     public
     */
    function show_request()
    {
        print_r($this->http_request);
    }

    /**
     * Shows the last response
     *
     * @access     public
     */
    function show_response()
    {
        print_r($this->http_response);
    }


    /**
     * This sets the API key to the given string
     * You do not need to use this method unless your bundling the code into your own application
     * If your application will be used by multiple users please create your own API key and call this
     *
     * @access     public
     */
    function set_api_key($api_key)
    {
        $this->api_key = $api_key;
    }


    /**
     * This gets the service description file from CC
     *
     *
     * @access     public
     */
    function get_service_description()
    {
        return $this->load_url();
    }


    /**
     * Gets all the contact lists for the CC account
     * If more than one page exists we grab them too
     * Second argument can be used to show/hide the do-not-mail etc lists
     * This method also sorts the lists based on the SortOrder field
     *
     * Avoid using this function directly, instead use constant_contact_get_lists()
     * which has built-in caching via WordPress transients to avoid constant queries
     * to the API that slow down the frontend of a site.
     *
     * @access     public
     */
    function get_all_lists($action = 'lists', $exclude = 3, $callback = '')
    {
        $lists = $this->get_lists($action, $exclude);

        if(count($lists) > 0):
            if(isset($this->list_meta_data->next_page)):
                // grab all the other pages if they exist
                while($this->list_meta_data->next_page != ''):
                $lists = array_merge($lists, $this->get_lists($this->list_meta_data->next_page, 0));
                endwhile;
            endif;

            $callback = ($callback) ? $callback : array("cc", "sort_lists");
            if(is_array($lists)):
                usort($lists, $callback);
            endif;

        endif;

        return $lists;
    }

    /**
     * sort the lists based on the SortOrder field
     *
     * @access     private
     */
    function sort_lists($a, $b)
    {
        if(!isset($a['SortOrder'], $b['SortOrder']) || $a['SortOrder'] == $b['SortOrder']):
            return 0;
        endif;
        return ($a['SortOrder'] < $b['SortOrder']) ? -1 : 1;
    }


    /**
     * Gets the contact lists for the CC account
     * The results are pageable
     * Second argument can be used to show/hide the do-not-mail etc lists
     *
     *
     * @access     public
     */
    function get_lists($action = 'lists', $exclude = 3)
    {
        $xml = $this->load_url($action);

        if(!$xml):
            return false;
        endif;

        $lists = array();

        // parse into nicer array
        $_lists = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;

        if(isset($xml['feed']['link']['2_attr']['rel']) && $xml['feed']['link']['2_attr']['rel'] == 'first'):
            $this->list_meta_data->first_page = $this->get_id_from_link($xml['feed']['link']['2_attr']['href']);
            $this->list_meta_data->current_page = $this->get_id_from_link($xml['feed']['link']['3_attr']['href']);
            $this->list_meta_data->next_page = '';
        elseif(isset($xml['feed']['link']['2_attr']['rel']) && $xml['feed']['link']['2_attr']['rel'] == 'next'):
            $this->list_meta_data->next_page = $this->get_id_from_link($xml['feed']['link']['2_attr']['href']);
            $this->list_meta_data->current_page = $this->get_id_from_link($xml['feed']['link']['3_attr']['href']);
            $this->list_meta_data->first_page = $this->get_id_from_link($xml['feed']['link']['4_attr']['href']);
        endif;


        if(is_array($_lists) && count($_lists) > 3):

            if($exclude):
                // skip first x amount of lists - remove, do not mail etc
                $_lists = array_slice($_lists, $exclude);
            endif;

            if(isset($_lists[0]['link_attr']['href'])):
                foreach($_lists as $k => $v):
                    $id = $this->get_id_from_link($v['link_attr']['href']);

                    $list = array(
                        'id' => $id,
                        'Name' => $v['content']['ContactList']['Name'],
                        'ShortName' => $v['content']['ContactList']['ShortName'],
                    );

                    if(isset($v['content']['ContactList']['OptInDefault'])):
                        $list['OptInDefault'] = $v['content']['ContactList']['OptInDefault'];
                    endif;

                    if(isset($v['content']['ContactList']['SortOrder'])):
                        $list['SortOrder'] = $v['content']['ContactList']['SortOrder'];
                    endif;

                    $lists[] = $list;
                endforeach;
            else:
                $id = $this->get_id_from_link($_lists['link_attr']['href']);

                $list = array(
                    'id' => $id,
                    'Name' => $_lists['content']['ContactList']['Name'],
                    'ShortName' => $_lists['content']['ContactList']['ShortName'],
                );

                if(isset($_lists['content']['ContactList']['OptInDefault'])):
                    $list['OptInDefault'] = $_lists['content']['ContactList']['OptInDefault'];
                endif;

                if(isset($_lists['content']['ContactList']['SortOrder'])):
                    $list['SortOrder'] = $_lists['content']['ContactList']['SortOrder'];
                endif;

                $lists[] = $list;
            endif;
        endif;

        return $lists;
    }


    /**
     * Gets the details of a specific constant list
     *
     *
     * @access     public
     */
    function get_list($listid)
    {
        $xml = $this->load_url("lists/$listid");

        if(!$xml):
            return false;
        endif;

        $list = false;
        $_list = (isset($xml['entry'])) ? $xml['entry'] : false;

        // parse into nicer array
        if(is_array($_list)):
            $id = $this->get_id_from_link($_list['link_attr']['href']);

            $list = array(
                'id' => $id,
                'Name' => $_list['content']['ContactList']['Name'],
                'ShortName' => $_list['content']['ContactList']['ShortName'],
                'OptInDefault' => $_list['content']['ContactList']['OptInDefault'],
                'SortOrder' => $_list['content']['ContactList']['SortOrder'],
            );
        endif;

        return $list;
    }


    /**
     * Deletes a contact list
     *
     *
     * @access     public
     */
    function delete_list($listid = 0)
    {
        $this->http_set_content_type('text/html');
        $this->load_url("lists/$listid", 'delete', array(), 204);
        if(intval($this->http_response_code) === 204):
            return true;
        endif;
        return false;
    }


    /**
     * Updates an existing contact list
     *
     *
     * @access     public
     */
    function update_list($id, $name, $default = "false", $sort_order = 99)
    {
        // build the XML put data
        $url = $this->get_list_url($id);

        $xml_data = '
<entry xmlns="http://www.w3.org/2005/Atom">
  <id>'.$url.'</id>
  <title type="text">'.$name.'</title>
  <author />
  <updated>2008-04-16T18:39:35.710Z</updated>
  <content type="application/vnd.ctct+xml">
    <ContactList xmlns="http://ws.constantcontact.com/ns/1.0/"
        id="'.$url.'">
      <OptInDefault>'.$default.'</OptInDefault>
      <Name>'.$name.'</Name>
      <ShortName>'.$name.'</ShortName>
      <SortOrder>'.$sort_order.'</SortOrder>
    </ContactList>
  </content>
  <link href="/ws/customers/'.$this->api_username.'/lists/'.$id.'" rel="update" />
</entry>
';

        $this->http_set_content_type('application/atom+xml');
        $xml = $this->load_url("lists/$id", 'put', $xml_data, 204);

        if(intval($this->http_response_code) === 204):
            return true;
        endif;
        return false;
    }


    /**
     * Creates a new contact list
     *
     *
     * @access     public
     */
    function create_list($name, $default = "false", $sort_order = 99)
    {
        // build the XML post data
        $xml_post = '<entry xmlns="http://www.w3.org/2005/Atom">
  <id>data:,</id>
  <title/>
  <author/>
  <updated>2008-04-16</updated>
  <content type="application/vnd.ctct+xml">
    <ContactList xmlns="http://ws.constantcontact.com/ns/1.0/">
      <OptInDefault>'.$default.'</OptInDefault>
      <Name>'.$name.'</Name>
      <SortOrder>'.$sort_order.'</SortOrder>
    </ContactList>
  </content>
</entry>';

        $this->http_set_content_type('application/atom+xml');

        $xml = $this->load_url("lists", 'post', $xml_post, 201);

        if(isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != ''):
            return $this->get_id_from_link($this->http_response_headers['Location']);
        endif;

        return false;
    }

    /**
     * Returns the full URL for list operations
     * NOTE: this is a HTTP URL unike the one we call
     *
     *
     * @access     private
     */
    function get_list_url($id, $full_address = true)
    {
        if($full_address):
            $_url = str_replace('https:', 'http:', $this->api_url . "lists");
        else:
            $_url = $this->api_uri . "lists";
        endif;

        return "$_url/$id";
    }


    /**
     * This returns the HTTP URL for the API
     *
     * @access     private
     */
    function get_http_api_url()
    {
        return str_replace('https:', 'http:', $this->api_url);
    }


    /**
     * Gets the members (contacts) in a specific contact list
     * Supports paging of the results
     *
     *
     * @access     public
     */
    function get_list_members($listid, $action = 'members')
    {
        $xml = $this->load_url("lists/$listid/$action");

        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $contacts = array();
        $_members = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;


        if(isset($xml['feed']['link']['2_attr']['rel']) && $xml['feed']['link']['2_attr']['rel'] == 'first'):
            $this->member_meta_data->first_page = $this->get_id_from_link($xml['feed']['link']['2_attr']['href']);
            $this->member_meta_data->current_page = $this->get_id_from_link($xml['feed']['link']['3_attr']['href']);
            $this->member_meta_data->next_page = '';
        elseif(isset($xml['feed']['link']['2_attr']['rel']) && $xml['feed']['link']['2_attr']['rel'] == 'next'):
            $this->member_meta_data->next_page = $this->get_id_from_link($xml['feed']['link']['2_attr']['href']);
            $this->member_meta_data->current_page = $this->get_id_from_link($xml['feed']['link']['3_attr']['href']);
            $this->member_meta_data->first_page = $this->get_id_from_link($xml['feed']['link']['4_attr']['href']);
        endif;

        if(is_array($_members)):
            if(isset($_members[0]['link_attr']['href'])):
                foreach($_members as $k => $v):
                    $EmailAddress = $v['content']['ContactListMember']['EmailAddress'];
                    $Name = $v['content']['ContactListMember']['Name'];
                    $id = $this->get_id_from_link($v['link_attr']['href']);

                    $contact = array(
                        'id' => $id,
                        'EmailAddress' => $EmailAddress,
                        'Name' => $Name,
                    );
                    $contacts[] = $contact;
                endforeach;
            else:
                $EmailAddress = $_members['content']['ContactListMember']['EmailAddress'];
                $Name = $_members['content']['ContactListMember']['Name'];
                $id = $this->get_id_from_link($_members['link_attr']['href']);

                $contact = array(
                    'id' => $id,
                    'EmailAddress' => $EmailAddress,
                    'Name' => $Name,
                );
                $contacts[] = $contact;
            endif;
        endif;

        return $contacts;
    }


	function get_all_list_members($listid) {
		$contacts = get_transient('listmembers'.$listid);
		if($contacts && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'list')) { return maybe_unserialize($contacts); }
		
		$contacts = array();
		$_contacts = array($this->get_list_members($listid));
		if(!empty($_contacts)) {
			
			while(!empty($this->member_meta_data->next_page)) {
				$_contacts[] = $this->get_list_members($listid, $this->member_meta_data->next_page);	
			}
			foreach($_contacts as $contactList) {
				foreach($contactList as $k => $v) {
					$contacts[$v['id']] = $v;
				}
			}
			#r($contacts);
			
		}
		
		set_transient('listmembers'.$listid, maybe_serialize($contacts));
		return $contacts;
	}


    /**
     * Creates a new contact
     *
     * @access     public
     */
    function create_contact($email, $lists = array(), $additional_fields = array())
    {
        $lists_url = str_replace('https:', 'http:', $this->api_url . "lists");

        // build the XML post data
        $xml_post = '
<entry xmlns="http://www.w3.org/2005/Atom">
  <title type="text"> </title>
  <updated>2008-07-23T14:21:06.407Z</updated>
  <author></author>
  <id>data:,none</id>
  <summary type="text">Contact</summary>
  <content type="application/vnd.ctct+xml">
    <Contact xmlns="http://ws.constantcontact.com/ns/1.0/">
      <EmailAddress>'.$email.'</EmailAddress>
      <OptInSource>'.$this->action_type.'</OptInSource>
';

    if($additional_fields):
    foreach($additional_fields as $field => $value):
        $xml_post .= "<$field>$value</$field>\n";
    endforeach;
    endif;

    $xml_post .= '<ContactLists>';
    if($lists):
    if(is_array($lists)):
        foreach($lists as $k => $id):
            $xml_post .= '<ContactList id="'.$lists_url.'/'.$id.'" />';
        endforeach;
    else:
        $xml_post .= '<ContactList id="'.$lists_url.'/'.$lists.'" />';
    endif;
    endif;
    $xml_post .= '</ContactLists>';

$xml_post .= '
    </Contact>
  </content>
</entry>';
        $this->http_set_content_type('application/atom+xml');

        $xml = $this->load_url("contacts", 'post', $xml_post, 201);

        if(isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != ''):
            return $this->get_id_from_link($this->http_response_headers['Location']);
        endif;

        return $this->http_response_body; // was simply `return`
    }


    /**
     * Updates a contact
     *
     * @access     public
     */
    function update_contact($id, $email, $lists = array(), $additional_fields = array())
    {
        // build the XML put data
        $_url = str_replace('https:', 'http:', $this->api_url . "contacts");
        $url = "$_url/$id";

        $xml_data = '<entry xmlns="http://www.w3.org/2005/Atom">
  <id>'.$url.'</id>
  <title type="text">Contact: '.$email.'</title>
  <updated>2008-04-25T19:29:06.096Z</updated>
  <author></author>
  <content type="application/vnd.ctct+xml">
    <Contact xmlns="http://ws.constantcontact.com/ns/1.0/" id="'.$url.'">
      <EmailAddress>'.$email.'</EmailAddress>
      <OptInSource>'.$this->action_type.'</OptInSource>
      <OptInTime>2009-11-19T14:48:41.761Z</OptInTime>
';
        if($additional_fields):
        foreach($additional_fields as $field => $value):
            $xml_data .= "<$field>$value</$field>\n";
        endforeach;
        endif;

        $xml_data .= "<ContactLists>\n";
        if($lists):
        if(is_array($lists)):
            foreach($lists as $k => $list_id):
                $xml_data .= '<ContactList id="'.$this->get_list_url($list_id).'"></ContactList>';
            endforeach;
        else:
            $xml_data .= '<ContactList id="'.$this->get_list_url($list_id).'"></ContactList>';
        endif;
        endif;
        $xml_data .= "</ContactLists>\n";

$xml_data .= '
    </Contact>
  </content>
</entry>
';

        $this->http_set_content_type('application/atom+xml');
        $this->load_url("contacts/$id", 'put', $xml_data, 204);

        if(intval($this->http_response_code) === 204):
            return true;
        endif;
        return false;
    }


	function get_all_contacts($timeout = 21600) {
		$contacts = get_transient('cc_contacts');
		if($contacts && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'contacts')) { return maybe_unserialize($contacts); }

		$contacts = array();
		$_contacts = array($this->get_contacts());
		if(!empty($_contacts)) {
			
			while(!empty($this->contact_meta_data->next_page)) {
				$_contacts[] = $this->get_contacts($this->contact_meta_data->next_page);	
			}
			foreach($_contacts as $contactList) {
				foreach($contactList as $k => $v) {
					$contacts[$v['id']] = $v;
				}
			}
			#r($contacts);
			
		}
		
		set_transient('cc_contacts', maybe_serialize($contacts), $timeout);
		return $contacts;
	}


    /**
     * Gets all contacts and allows paging of the results
     *
     * @access     public
     */
    function get_contacts($action = 'contacts', $timeout = 3600)
    {
    	$xml = $this->load_url($action);

        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $contacts = array();
        $_contacts = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;

        if(isset($xml['feed']['link']['2_attr']['rel']) && $xml['feed']['link']['2_attr']['rel'] == 'first'):
            $this->contact_meta_data->first_page = $this->get_id_from_link($xml['feed']['link']['2_attr']['href']);
            $this->contact_meta_data->current_page = $this->get_id_from_link($xml['feed']['link']['3_attr']['href']);
            $this->contact_meta_data->next_page = '';
        elseif(isset($xml['feed']['link']['2_attr']['rel']) && $xml['feed']['link']['2_attr']['rel'] == 'next'):
            $this->contact_meta_data->next_page = $this->get_id_from_link($xml['feed']['link']['2_attr']['href']);
            $this->contact_meta_data->current_page = $this->get_id_from_link($xml['feed']['link']['3_attr']['href']);
            $this->contact_meta_data->first_page = $this->get_id_from_link($xml['feed']['link']['4_attr']['href']);
        endif;

        if(is_array($_contacts)):
            if(isset($_contacts[0]['link_attr']['href'])):
                foreach($_contacts as $k => $v):
                    $id = $this->get_id_from_link($v['link_attr']['href']);
                    $contact = $v['content']['Contact'];
                    $contact['id'] = $id;
                    $contacts[] = $contact;
                endforeach;
            else:
                $id = $this->get_id_from_link($_contacts['link_attr']['href']);
                $contact = $_contacts['content']['Contact'];
                $contact['id'] = $id;
                $contacts[] = $contact;
            endif;
        endif;
		
		return $contacts;
    }


    /**
     * Gets a specific contacts details
     *
     * @access     public
     */
    function get_contact($id = null, $timeout = 3600)
    {
    	$transient_title = 'cccon'.sha1($id);
		$contact = get_transient($transient_title);
		if(!empty($timeout) && $contact && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'contact')) { return maybe_unserialize($contact); }
		
        $xml = $this->load_url("contacts/$id");

        if(!$xml):
            return false;
        endif;

        $contact = false;
        $_contact = (isset($xml['entry'])) ? $xml['entry'] : false;

        // parse into nicer array
        if(is_array($_contact)):
            $id = $this->get_id_from_link($_contact['link_attr']['href']);

            $contact = $_contact['content']['Contact'];

            if(isset($_contact['content']['Contact']['ContactLists']['ContactList'])):
                $_lists = $_contact['content']['Contact']['ContactLists']['ContactList'];
                unset($_lists['0_attr']);
                unset($_lists['ContactList_attr']);
            else:
                $_lists = false;
            endif;

            // get lists
            $lists = array();
            if(is_array($_lists) && count($_lists) > 0):
                unset($_lists['id']);

                if(isset($_lists['link_attr']['href'])):
                    $list_id = $this->get_id_from_link($_lists['link_attr']['href']);
                    $lists[$list_id] = $list_id;
                else:
                    foreach($_lists as $k => $v):
                        if(isset($v['link_attr']['href'])):
                            $list_id = $this->get_id_from_link($v['link_attr']['href']);
                            $lists[$list_id] = $list_id;
                        endif;
                    endforeach;
                endif;

                unset($contact['ContactLists']);
            endif;

            $contact['lists'] = $lists;
            $contact['id'] = $id;
        endif;

		set_transient($transient_title, maybe_serialize($contact), $timeout);

        return $contact;
    }


    /**
     * This queries the API for contacts with a similar email address to the one you supply
     *
     * @access     public
     */
    function query_contacts($email)
    {
        $xml = $this->load_url('contacts?email=' . strtolower(urlencode($email)));

        if(!$xml):
            return false;
        endif;

        $contact = false;
        $_contact = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;

        // parse into nicer array
        if(is_array($_contact)):
            $id = $this->get_id_from_link($_contact['link_attr']['href']);

            $contact = $_contact['content']['Contact'];
            $contact['id'] = $id;
        endif;

        return $contact;
    }

    /**
     * Deletes a contact
     *
     * @access     public
     */
    function delete_contact($id)
    {
        $this->http_set_content_type('text/html');
        $this->load_url("contacts/$id", 'delete', array(), 204);
        if(intval($this->http_response_code) === 204):
            return true;
        endif;
        return false;
    }


    /**
     * Activities (bulk) operations
     * The Activities resource is designed to be used only with large sets of contacts (ie. 25 or more). To manage individual contacts or small sets of contacts, use the  Contacts Collection API resource. (As discussed in the  Constant Contact API Terms and Conditions, intentional and unintentional misuse of this bulk API to frequently manage individual contacts or small sets of contacts is subject to API access or account termination).
     *
     */


    /**
     * Downloads an activity file
     *
     * @access     public
     */
    function download_activity_file($filename)
    {
        $this->http_set_content_type(NULL);
        $this->load_url("activities/$filename", 'get');
        return $this->http_response_body;
    }


    /**
     * Gets an individual activity
     *
     * @access     public
     */
    function get_activity($id)
    {
        $xml = $this->load_url("activities/$id");

        if(!$xml):
            return false;
        endif;
			
        // parse into nicer array
        $_activity = (isset($xml['entry'])) ? $xml['entry'] : false;
        $activity = $_activity['content']['Activity'];
        $activity['id'] = $id;
		
        if(isset($activity['FileName'])):
            $activity['FileName'] = $this->get_id_from_link($activity['FileName']);
        endif;

        return $activity;
    }


    /**
     * Gets all activities
     *
     * @access     public
     */
    function get_activities($action = 'activities')
    {
        $xml = $this->load_url($action);

        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $activities = array();
        $_activities = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;

        if(is_array($_activities)):
            if(isset($_activities[0]['link_attr']['href'])):
                foreach($_activities as $k => $v):
                    $id = $this->get_id_from_link($v['link_attr']['href']);
                    $activity = $v['content']['Activity'];
                    $activity['id'] = $id;
                    $activities[] = $activity;
                endforeach;
            else:
                $id = $this->get_id_from_link($_activities['link_attr']['href']);
                $activity = $_activities['content']['Activity'];
                $activity['id'] = $id;
                $activities[] = $activity;
            endif;
        endif;

        return $activities;
    }


	/**
     * Gets all activities
     *
     * @access     public
     */
    function get_events($action = 'events', $timeout = 3600)
    {
    	$events = get_transient('cc_events');
		if($events && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'events')) { return maybe_unserialize($events); }
		
        $xml = $this->load_url($action);

        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $events = array();
        $_events = (isset($xml['atom:feed']['atom:entry'])) ? $xml['atom:feed']['atom:entry'] : false;
        if(is_array($_events)):
        	if(isset($_events[0]['atom:link_attr']['href'])):
                foreach($_events as $k => $v):
                    $id = $this->get_id_from_link($v['atom:link_attr']['href']);
                    $event = $v['atom:content']['Event'];
                    $event['id'] = $id;
                    $events[] = $event;
                endforeach;
            else:
                $id = $this->get_id_from_link($_events['atom:link_attr']['href']);
                $event = $_events['atom:content']['Event'];
                $event['id'] = $id;
                $events[] = $event;
            endif;
        endif;
        
        if(!empty($events)) {
	        set_transient('cc_events', maybe_serialize($events), $timeout);
	    }

        return $events;
    }
    
     /**
     * Gets an individual activity
     *
     * @access     public
     */
    function get_event($id = null, $timeout = 3600)
    {
    	$transient_title = 'ccev_'.sha1($id);
		$events = get_transient($transient_title);
		if($events && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'event')) { return maybe_unserialize($events); }
    	
        $xml = $this->load_url("events/$id");
        
        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $_events = (isset($xml['atom:entry'])) ? $xml['atom:entry'] : false;
        $events = $_events['atom:content']['Event'];
        $events['id'] = $id;

        if(isset($events['FileName'])):
            $events['FileName'] = $this->get_id_from_link($events['FileName']);
        endif;
		
		if(!empty($events)) {
			set_transient($transient_title, maybe_serialize($events), $timeout);
		}
        
        return $events;
    }
    
    function get_event_registrants($id) {
    
    	$transient_title = 'ccers'.sha1($id);
		$registrants = get_transient($transient_title);
		if($registrants && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'registrants')) { return maybe_unserialize($registrants); }
		
    	 $xml = $this->load_url("events/$id/registrants");
        
        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $registrants = array();
        $_registrants = (isset($xml['atom:feed']['atom:entry'])) ? $xml['atom:feed']['atom:entry'] : false;

        if(is_array($_registrants)):
            if(isset($_registrants[0]['atom:link_attr']['href'])):
                foreach($_registrants as $k => $v):
                    $id = $this->get_id_from_link($v['atom:link_attr']['href']);
                    $registrant = $v['atom:content']['Registrant'];
                    $registrant['id'] = $id;
                    $registrants[] = $registrant;
                endforeach;
            else:
                $id = $this->get_id_from_link($_registrants['atom:link_attr']['href']);
                $registrant = $_registrants['atom:content']['Registrant'];
                $registrant['id'] = $id;
                $registrants[] = $registrant;
            endif;

        endif;
        
        if(!empty($registrants)) {
	        set_transient($transient_title, maybe_serialize($registrants), 60*60);
	    }

        return $registrants;
    }
    
    function get_event_registrant($eventid = '',$registrantid = '')
    {
    	$transient_title = 'ccer'.sha1($eventid.$registrantid);
		$registrant = get_transient($transient_title);
		if($registrant && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'registrant')) { return maybe_unserialize($registrant); }
		
        $xml = $this->load_url("events/$eventid/registrants/$registrantid");

        if(!$xml):
            return false;
        endif;


        // parse into nicer array
        $registrant = false;
        $_registrant = (isset($xml['atom:entry'])) ? $xml['atom:entry'] : false;

        // parse into nicer array
        if(is_array($_registrant)):
            $id = $this->get_id_from_link($_registrant['atom:link_attr']['href']);
            $registrant = $_registrant['atom:content']['Registrant'];
            $registrant['id'] = $id;
        endif;
		
		if(!empty($registrant)) {
			set_transient($transient_title, maybe_serialize($registrant), 60*60);
		}
		
        return $registrant;
    }
    
    
    /**
     * Be careful with this method :)
     * You can use this to clear all contacts from a specific set of contact lists
     *
     *
     * @param    array    An array of contact list ID's to clear of contacts
     * @access     public
     */
    function clear_contacts($lists)
    {
        $params['activityType'] = 'CLEAR_CONTACTS_FROM_LISTS';

        $lists_string = '';
        if(is_array($lists)):
            foreach($lists as $id):
                $params['lists'][] = $this->get_list_url($id);
            endforeach;
        endif;

        $this->http_set_content_type('application/x-www-form-urlencoded');

        $this->load_url("activities", 'post', $params, 201);

        if(isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != ''):
            return $this->get_id_from_link($this->http_response_headers['Location']);
        endif;

        return false;
    }


    /**
     * Returns a list of the columns used in the export file
     *
     * @access     public
     */
    function get_export_file_columns()
    {
        $columns = array(
            'FIRST NAME',
            'MIDDLE NAME',
            'LAST NAME',
            'JOB TITLE',
            'COMPANY NAME',
            'WORK PHONE',
            'HOME PHONE',
            'ADDRESS LINE 1',
            'ADDRESS LINE 2',
            'ADDRESS LINE 3',
            'CITY',
            'STATE',
            'STATE/PROVINCE (US/CANADA)',
            'COUNTRY',
            'POSTAL CODE',
            'SUB POSTAL CODE',
        );

        $new = array();

        foreach($columns as $column):
            $new[$column] = $column;
        endforeach;

        return $new;
    }


    /**
     * This method creates a new export contacts activity
     * It returns the activity ID to use to check the status
     *
     *
     * @param    array    An array: fieldnames to export: see fieldnames.html
     * @param    int        The ID of the list to export
     * @param    string    The format of the export, either CSV or TXT
     *
     * @access     public
     */
    function export_contacts($list_id, $export_type = 'CSV', $columns = array(), $sort_by = 'DATE_DESC')
    {
        if(!is_array($columns) || !count($columns)):
            $columns = $this->get_export_file_columns();
        endif;

        $params['activityType'] = 'EXPORT_CONTACTS';
        $params['fileType'] = $export_type;
        $params['exportOptDate'] = 'true';
        $params['exportOptSource'] = 'true';
        $params['exportListName'] = 'false';
        $params['sortBy'] = $sort_by;
        $params['columns'] = $columns;
        $params['listId'] = $this->get_list_url($list_id);

        $this->http_set_content_type('application/x-www-form-urlencoded');

        $this->load_url("activities", 'post', $params, 201);

        if((isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != '') || isset($this->http_response_headers['location']) && trim($this->http_response_headers['location']) != '') {
        	if(isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != '') { 
        		return $this->get_id_from_link($this->http_response_headers['Location']);
        	} else {
	        	return $this->get_id_from_link($this->http_response_headers['location']);
        	}     	   
        }

        return false;
    }


    /**
     * This method is used to add 25 or more contacts
     * Pass this method an associative array of contact details
     * Alternatively you can give the path to a local or remote file
     * The file should be text or CSV format:
     * @link http://constantcontact.custhelp.com/cgi-bin/constantcontact.cfg/php/enduser/std_adp.php?p_faqid=2523
     *
     *
     * @param    mixed    This can be an array or a path to a file
     * @param    array    An array of contact list ID's to add the user to
     *
     * @access     public
     */
    function create_contacts($contacts, $lists)
    {
        $params['activityType'] = 'SV_ADD';

        if(is_array($contacts) && count($contacts) > 0):
            // get fieldnames from keys of the first contact array
            $fieldnames = array_keys($contacts[0]);
            $contacts = array_values($contacts);

            // transform the given array into a CSV formatted string
            $contacts_string = '';
            foreach($contacts as $k => $contact):
                foreach($fieldnames as $k => $fieldname):
                    if(isset($contact[$fieldname]) || is_null($contact[$fieldname])):
                        $contacts_string .= $contact[$fieldname].",";
                    else:
                        $this->last_error = 'contacts array is not formatted correctly, please ensure all contact entries have the same fields and values';
                        return false;
                    endif;
                endforeach;
                $contacts_string .= "{$this->http_linebreak}";
            endforeach;

            $params['data'] = implode(',', $fieldnames)."{$this->http_linebreak}" . $contacts_string;

        elseif(file_exists($contacts) && is_readable($contacts)):
            // grab the file and output it directly in the request
            $params['data'] = file_get_contents($contacts);
        endif;

        if(is_array($lists)):
            foreach($lists as $id):
                $params['lists'][] = $this->get_list_url($id);
            endforeach;
        endif;

        $this->http_set_content_type('application/x-www-form-urlencoded');

        $this->load_url("activities", 'post', $params, 201);

        if((isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != '') || (isset($this->http_response_headers['location']) && trim($this->http_response_headers['location']) != '')) {
          	if(isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != '') {
            	return $this->get_id_from_link($this->http_response_headers['Location']);
            } else {
            	return $this->get_id_from_link($this->http_response_headers['location']);
            }
        }

        return false;
    }



    /**
     * Gets all campaigns
     *
     * @access     public
     */
    function get_campaigns($action = 'campaigns')
    {
    	$transient_title = 'cc_campaigns';
		$campaigns = get_transient($transient_title);
		if($campaigns && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'campaigns')) { return maybe_unserialize($campaigns); }

		$xml = $this->load_url($action);

        if(!$xml):
            return false;
        endif;


        // parse into nicer array
        $campaigns = array();
        $_campaigns = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;

        if(is_array($_campaigns)):
            if(isset($_campaigns[0]['link_attr']['href'])):
                foreach($_campaigns as $k => $v):
                    $id = $this->get_id_from_link($v['link_attr']['href']);
                    $campaign = $v['content']['Campaign'];
                    $campaign['id'] = $id;
                    $campaigns[] = $campaign;
                endforeach;
            else:
                $id = $this->get_id_from_link($_campaigns['link_attr']['href']);
                $campaign = $_campaigns['content']['Campaign'];
                $campaign['id'] = $id;
                $campaigns[] = $campaign;
            endif;

        endif;

		set_transient($transient_title, maybe_serialize($campaigns), 60*60);
		
        return $campaigns;
    }


    /**
     * Gets an individual campaign
     *
     * @access     public
     */
    function get_campaign($id)
    {
        $xml = $this->load_url("campaigns/$id");

        if(!$xml):
            return false;
        endif;


        // parse into nicer array
        $campaign = false;
        $_campaign = (isset($xml['entry'])) ? $xml['entry'] : false;

        // parse into nicer array
        if(is_array($_campaign)):
            $id = $this->get_id_from_link($_campaign['link_attr']['href']);
            $campaign = $_campaign['content']['Campaign'];
            $campaign['id'] = $id;
        endif;

        return $campaign;
    }


    /**
     * Deletes a campaign
     *
     * @access     public
     */
    function delete_campaign($id)
    {
        $this->http_set_content_type('text/html');
        $this->load_url("campaigns/$id", 'delete', array(), 204);
        if(intval($this->http_response_code) === 204):
            return true;
        endif;
        return false;
    }


    /**
     * Creates a new campaign
     *
     * @access     public
     */
/*
    function create_campaign($title, $contact_lists = array(), $options = array())
    {
        $email = (isset($options['EmailAddress'])) ? $options['EmailAddress'] : '';
        unset($options['EmailAddress']);

        $xml_post = '<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
  <link href="/ws/customers/'.$this->api_username.'/campaigns" rel="edit" />
  <id>'.$this->get_http_api_url().'campaigns</id>
  <title type="text">'.$title.'</title>
  <updated>2009-10-19T18:34:53.105Z</updated>
  <author>
    <name>Constant Contact</name>
  </author>
  <content type="application/vnd.ctct+xml">
    <Campaign xmlns="http://ws.constantcontact.com/ns/1.0/"
id="'.$this->get_http_api_url().'campaigns/1100546096289">
      <Name>'.$title.'</Name>
      <Status>Draft</Status>
      <Date>2009-10-19T18:34:53.105Z</Date>
';
        if(!is_array($options)):
            trigger_error('Third argument to create_campaign() should be an array', E_USER_ERROR);
        endif;

        foreach($options as $fieldname => $fieldvalue):
        if(isset($options[$fieldname])):
            $xml_post .= "<$fieldname>$fieldvalue</$fieldname>\n";
        endif;
        endforeach;

        if(is_array($contact_lists)):
            $xml_post  .= '<ContactLists>';
            foreach($contact_lists as $id):
                $xml_post  .= '
                <ContactList id="'.$this->get_list_url($id).'">
                <link xmlns="http://www.w3.org/2005/Atom" href="'.$this->get_list_url($id,0).'" rel="self" />
                </ContactList>
              ';
            endforeach;
            $xml_post .= '</ContactLists>';
        endif;

        if($email):
            $email = $this->get_emails($email);

            if(!isset($email['id'])):
                $this->last_error = 'Invalid Email Address, the email address must exist in your constant contact account to be able to send an email from this address';
                return false;
            endif;

            $xml_post .= '
            <FromEmail>
            <Email id="'.$this->get_http_api_url().'settings/emailaddresses/'.$email['id'].'">
            <link xmlns="http://www.w3.org/2005/Atom" href="'.$this->api_uri.'settings/emailaddresses/'.$email['id'].'" rel="self" />
            </Email>
            <EmailAddress>'.$email['EmailAddress'].'</EmailAddress>
            </FromEmail>
            <ReplyToEmail>
            <Email id="'.$this->get_http_api_url().'settings/emailaddresses/'.$email['id'].'">
            <link xmlns="http://www.w3.org/2005/Atom" href="'.$this->api_uri.'settings/emailaddresses/'.$email['id'].'" rel="self" />
            </Email>
            <EmailAddress>'.$email['EmailAddress'].'</EmailAddress>
            </ReplyToEmail>
            ';
        endif;

        $xml_post .= '
    </Campaign>
    </content>
    <source>
        <id>'.$this->get_http_api_url().'campaigns</id>
        <title type="text">Campaigns for customer: '.$this->api_username.'</title>
        <link href="campaigns" />
        <link href="campaigns" rel="self" />
        <author>
            <name>'.$this->api_username.'</name>
        </author>
        <updated>2009-10-19T19:36:12.622Z</updated>
    </source>
</entry>';


        $this->http_set_content_type('application/atom+xml');
        $xml = $this->load_url("campaigns", 'post', $xml_post, 201);

        if(isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != ''):
            return $this->get_id_from_link($this->http_response_headers['Location']);
        endif;

        return false;
    }
*/
    
    
    	/**
	 * Creates a new campaign
	 *
	 * @access 	public
	 */
	function create_campaign($title = '', $email_subject = '', $email_head = '', $email_html = '', $email_text = '', $contact_lists = array(),$options = array(), $content_type = 'HTML')
	{
		$defaults = array(
			'FromName' => get_option('blogname'),
			#'EmailAddress' => '', //get_option('admin_email'),
			'ViewAsWebpage' => 'NO',
			'ViewAsWebpageText' => '',
			'ViewAsWebpageLinkText' => '',
			'IncludeForwardEmail' => 'NO',
			'IncludeSubscribeLink' => 'NO',
			'GreetingSalutation' => 'Dear',
			'GreetingName' => 'FirstName',
			'GreetingString' => 'Greetings!',
			'OrganizationName' => '',
			'OrganizationAddress1' => '',
			'OrganizationAddress2' => '',
			'OrganizationAddress3' => '',
			'OrganizationCity' => '',
			'OrganizationState' => '',
			'OrganizationInternationalState' => '',
			'OrganizationCountry' => '',
			'OrganizationPostalCode' => '',
			'StyleSheet' => '',
			'Status' => 'Draft',
			'ForwardEmailLinkText' => '',
			'PermissionReminderText' => '',
			'SubscribeLinkText' => '',
			'EmailContentFormat' => 'XHTML'
		);
	
		$options = wp_parse_args($options, $defaults);
	  
	  if(get_option('gmt_offset')) {
	  	$offset = get_option('gmt_offset') * 60;
	  } else {
	  	$offset = (60 * -5);
	  }
	  
		// build the XML post data
		$xml_post = '
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
  <link href="/ws/customers/'.$this->api_username.'/campaigns" rel="edit" />
  <id>'.$this->get_http_api_url().'campaigns</id>
  <title type="text">'.esc_html($title).'</title>
  <updated>'.date('Y').'-'.date('m').'-'.date('d').'T'.date('G').':'.date('i').':'.date('s').'.'.$offset.'Z</updated>
  <author>
  	<name>Constant Contact for WordPress</name>
  </author>
  <content type="application/vnd.ctct+xml">
    <Campaign xmlns="http://ws.constantcontact.com/ns/1.0/" id="'.$this->get_http_api_url().'campaigns">
      <Name>'.esc_html($title).'</Name>
      <Date>'.date('Y').'-'.date('m').'-'.date('d').'T'.date('G').':'.date('i').':'.date('s').'.'.$offset.'Z</Date>
      <Subject>'.esc_html($email_subject).'</Subject>
	  ';
	  
	  
	if($options['ViewAsWebpage'] == 'YES') {
		$xml_post .= '
		<ViewAsWebpage>YES</ViewAsWebpage>
		<ViewAsWebpageLinkText>'.esc_html($options['ViewAsWebpageLinkText']).'</ViewAsWebpageLinkText>
		<ViewAsWebpageText>'.esc_html($options['ViewAsWebpageText']).'</ViewAsWebpageText>
		';
	}
	unset($options['ViewAsWebpage'], $options['ViewAsWebpageLinkText'], $options['ViewAsWebpageText']);
  
	if(isset($options['PermissionReminder']) && $options['PermissionReminder'] == 'YES') {
		$xml_post .= '
		<PermissionReminder>YES</PermissionReminder>
		<PermissionReminderText>'.esc_html($options['PermissionReminderText']).'</PermissionReminderText>';
	}
	unset($options['PermissionReminder'], $options['PermissionReminderText']);
	
	if($options['IncludeForwardEmail'] == 'YES') {
		$xml_post .= '
		<IncludeForwardEmail>YES</IncludeForwardEmail>
		<ForwardEmailLinkText>'.esc_html($options['ForwardEmailLinkText']).'</ForwardEmailLinkText>
		';
	} else {
		$xml_post .= '
		<IncludeForwardEmail>NO</IncludeForwardEmail>
		<ForwardEmailLinkText></ForwardEmailLinkText>
		';
	}
	unset($options['IncludeForwardEmail'], $options['ForwardEmailLinkText']);
	
	if($options['IncludeSubscribeLink'] == 'YES') {
		$xml_post .= '
		<IncludeSubscribeLink>YES</IncludeSubscribeLink>
		<SubscribeLinkText>'.esc_html($options['SubscribeLinkText']).'</SubscribeLinkText>
		';
	} else {
		$xml_post .= '
		<IncludeSubscribeLink>NO</IncludeSubscribeLink>
		<SubscribeLinkText></SubscribeLinkText>
		';
	}
	unset($options['IncludeSubscribeLink'], $options['SubscribeLinkText']);
		
		if(!empty($options['EmailAddress'])) {
            $email = $this->get_emails($options['EmailAddress']);

            if(!isset($email['id'])) {
                $this->last_error = 'Invalid Email Address, the email address must exist in your constant contact account to be able to send an email from this address';
                return false;
            }
        
       		unset($options['EmailAddress']);
	
		
		$xml_post .= '
		<FromEmail>
			<Email id="'.$this->get_http_api_url().'settings/emailaddresses/'.$email['id'].'">
				<link xmlns="http://www.w3.org/2005/Atom" href="'.$this->api_uri.'settings/emailaddresses/'.$email['id'].'" rel="self" />
			</Email>
			<EmailAddress>'.$email['EmailAddress'].'</EmailAddress>
		</FromEmail>
		<ReplyToEmail>
			<Email id="'.$this->get_http_api_url().'settings/emailaddresses/'.$email['id'].'">
				<link xmlns="http://www.w3.org/2005/Atom" href="'.$this->api_uri.'settings/emailaddresses/'.$email['id'].'" rel="self" />
			</Email>
			<EmailAddress>'.$email['EmailAddress'].'</EmailAddress>
		</ReplyToEmail>
		';
		foreach($options as $field => $value) {
			$xml_post .= '<'.$field.'>'.esc_html($value).'</'.$field.'>'."\n\t\t";
			unset($options[$field]);
		}
		
			$xml_post .= '
		<EmailContent>'.esc_html('<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" 
xmlns:cctd="http://www.constantcontact.com/cctd">
			'.$email_head.'
			<body><CopyRight>Copyright (c) 1996-2009 Constant Contact. All rights reserved.  Except as permitted under a separate written agreement with Constant Contact, neither the Constant Contact software, nor any content that appears on any Constant Contact site, including but not limited to, web pages, newsletters, or templates may be reproduced, republished, repurposed, or distributed without the prior written permission of Constant Contact.  For inquiries regarding reproduction or distribution of any Constant Contact material, please contact '.$email['EmailAddress'].'</CopyRight>
			<OpenTracking/>
			<CustomBlock name="letter.intro" title="Personalization">
			    <Greeting/>
			</CustomBlock>
			'.$email_html.'
			</body>
			</html>').'
		</EmailContent>
		<EmailTextContent>&lt;Text&gt;'.esc_html($email_text).'&lt;/Text&gt;</EmailTextContent>
	';
		if(is_array($contact_lists)):
			$xml_post  .= '<ContactLists>';
			foreach($contact_lists as $id):
				$xml_post  .= '
				<ContactList id="'.$this->get_list_url($id).'">
					<link xmlns="http://www.w3.org/2005/Atom" href="'.$this->get_list_url($id,0).'" rel="self" />
				</ContactList>
			  ';
			endforeach;
			$xml_post .= '</ContactLists>';
		endif;
           
        } else {
        	return false;
        }
			  
		$xml_post .= '
		</Campaign>
	</content>
	<source>
	    <id>'.$this->get_http_api_url().'campaigns</id>
	    <title type="text">Campaigns for customer: '.$this->api_username.'</title>
	    <link href="campaigns" />
	    <link href="campaigns" rel="self" />
	    <author>
	      <name>'.$this->api_username.'</name>
	    </author>
	    <updated>'.date('Y').'-'.date('m').'-'.date('d').'T'.date('G').':'.date('i').':'.date('s').'.'.$offset.'Z</updated>
	</source>
</entry>';
		
		$this->http_set_content_type('application/atom+xml');
		
		#die(htmlentities(print_r($xml_post, true)));
		
		$xml = $this->load_url("campaigns", 'post', trim(rtrim($xml_post)), 201);
		
	#	if(!is_admin()) {
	#		print_r($xml_post);
	#		print_r($xml);
	#		echo '</pre>';
	#	}
		
		if(isset($this->http_response_headers['Location']) && trim($this->http_response_headers['Location']) != ''):
			return $this->get_id_from_link($this->http_response_headers['Location']);
		endif;
		
		return false;
	}



    /**
     * This queries the API for campaigns with a certain status
     * Supported status codes are:
     * SENT          All campaigns that have been sent and not currently scheduled for resend
     * SCHEDULED     All campaigns that are currently scheduled to be sent some time in the future
     * DRAFT         All campaigns that have not yet been scheduled for delivery
     * RUNNING         All campaigns that are currently being processed and delivered
     *
     * @access     public
     */
    function query_campaigns($status = 'SENT')
    {
    	$transient_title = 'cccam'.$status;
		$campaigns = get_transient($transient_title);
		if($campaigns && (!isset($_GET['refresh']) || $_GET['refresh'] !== 'campaigns')) { return maybe_unserialize($campaigns); }
    	
        $xml = $this->load_url('campaigns?status=' . urlencode($status));

        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $campaigns = array();
        $_campaigns = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;

        if(is_array($_campaigns)):
            if(isset($_campaigns[0]['link_attr']['href'])):
                foreach($_campaigns as $k => $v):
                    $id = $this->get_id_from_link($v['link_attr']['href']);
                    $campaign = $v['content']['Campaign'];
                    $campaign['id'] = $id;
                    $campaigns[] = $campaign;
                endforeach;
            else:
                $id = $this->get_id_from_link($_campaigns['link_attr']['href']);
                $campaign = $_campaigns['content']['Campaign'];
                $campaign['id'] = $id;
                $campaigns[] = $campaign;
            endif;
        endif;

		set_transient($transient_title, maybe_serialize($campaigns), 60*60*6);

        return $campaigns;
    }


    /**
     * Gets all account email addresses or a single email
     * These are used with the campaigns collection
     *
     * @access     public
     */
    function get_emails($return_email = '')
    {
        $xml = $this->load_url("settings/emailaddresses");

        if(!$xml):
            return false;
        endif;

        // parse into nicer array
        $emails = array();
        $_emails = (isset($xml['feed']['entry'])) ? $xml['feed']['entry'] : false;

        if(is_array($_emails)):
            if(isset($_emails[0]['link_attr']['href'])):
                foreach($_emails as $k => $v):
                    $id = $this->get_id_from_link($v['link_attr']['href']);
                    $email = $v['content']['Email'];
                    $email['id'] = $id;

                    if($return_email && $return_email == $v['content']['Email']['EmailAddress']):
                        // return single email
                        return $email;
                    endif;

                    $emails[] = $email;
                endforeach;
            else:
                $id = $this->get_id_from_link($_emails['link_attr']['href']);
                $email = $_emails['content']['Email'];
                $email['id'] = $id;
                $emails[] = $email;

                if($return_email && $return_email == $_emails['content']['Email']['EmailAddress']):
                    // return single email
                    return $email;
                endif;
            endif;

        endif;

        return $emails;
    }


    /**
     * Converts a timestamp that is in the format 2008-08-05T16:50:04.534Z to a UNIX timestamp
     *
     *
     * @access     public
     */
    function convert_timestamp($timestamp)
    {
        $timestamp_bits = explode('T', $timestamp);
		
        if(isset($timestamp_bits[0], $timestamp_bits[0])):
            $date_bits = explode('-', $timestamp_bits[0]);
            $time_bits = explode(':', $timestamp_bits[1]);
            
            $year = (int) $date_bits[0];
            if($year < 1900) { $year = date('Y'); } // The year was showing 111
            $month = (int) $date_bits[1];
            $day = (int) $date_bits[2];
            $hour = (int) $time_bits[0];
            $minute = (int) $time_bits[1];
            $second = (int) $time_bits[2];

		  return mktime($hour, $minute, $second, $month, $day, $year);
        endif;

        return false;

    }


    /**
     * Method other methods call to get the unique ID of the resource
     * Unique ID's are used to identify a specific resource such as a contact or contact list and are passed as arguments to some of the methods
     * This is also used to get just the last piece of the URL in other functions eg. get_lists()
     *
     * @access     public
     */
    function get_id_from_link($link)
    {
        $link_bits = explode('/', $link);
        return $link_bits[(count($link_bits)-1)];
    }


    /**
     * This method will convert a string comtaining XML into a nicely formatted PHP array
     *
     * @access     private
     */
    function xml_to_array($contents, $get_attributes=1, $priority = 'tag') {
        if(!$contents) return array();

        if(!function_exists('xml_parser_create')) {
            trigger_error("XML not supported: " .
                          "http://www.php.net/manual/en/ref.xml.php", E_USER_ERROR);
            return array();
        }
        $output_encoding = 'ISO-8859-1';
        $input_encoding = NULL;
        $detect_encoding = true;

        list($parser, $source) = $this->xml_create_parser($contents,
                $output_encoding, $input_encoding, $detect_encoding);


        if (!is_resource($parser))
        {
            trigger_error("Failed to create an instance of PHP's XML parser. " .
                          "http://www.php.net/manual/en/ref.xml.php", E_USER_ERROR);
        }

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if(!$xml_values) return;//Hmm...

        //Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference

        //Go through the tags.
        $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
        foreach($xml_values as $data) {
            unset($attributes,$value);//Remove existing values, or there will be trouble

            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data);//We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if(isset($value)) {
                if($priority == 'tag') $result = $value;
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if(isset($attributes) and $get_attributes) {
                foreach($attributes as $attr => $val) {
                    if($priority == 'tag') $attributes_data[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                	if(!is_array($current)) { $current = array(); } // Added in 2.3
                    $current[$tag] = $result;
                    if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag.'_'.$level] = 1;

                    $current = &$current[$tag];

                } else { //There was another element with the same tag name

                    if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                        $repeated_tag_index[$tag.'_'.$level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag.'_'.$level] = 2;

                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }

                    }
                    $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                    $current = &$current[$tag][$last_item_index];
                }

            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if(!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

                } else { //If taken, put all things inside a list(array)
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                        if($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $get_attributes) {
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }

        return($xml_array);
    }


    /**
     * Return XML parser, and possibly re-encoded source
     *
     * @access     private
     */
    function xml_create_parser($source, $out_enc, $in_enc, $detect)
    {
        if ( substr(phpversion(),0,1) == 5) {
            $parser = $this->xml_php5_create_parser($in_enc, $detect);
        }
        else {
            list($parser, $source) = $this->xml_php4_create_parser($source, $in_enc, $detect);
        }
        if ($out_enc) {
            $this->xml_encoding = $out_enc;
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $out_enc);
        }

        return array($parser, $source);
    }

    /**
     * Instantiate an XML parser under PHP5
     *
     * PHP5 will do a fine job of detecting input encoding
     * if passed an empty string as the encoding.
     *
     * @access     private
     */
    function xml_php5_create_parser($in_enc, $detect)
    {
        // by default php5 does a fine job of detecting input encodings
        if(!$detect && $in_enc) {
            return xml_parser_create($in_enc);
        }
        else {
            return xml_parser_create('');
        }
    }

    /**
     * Instaniate an XML parser under PHP4
     *
     * Unfortunately PHP4's support for character encodings
     * and especially XML and character encodings sucks.  As
     * long as the documents you parse only contain characters
     * from the ISO-8859-1 character set (a superset of ASCII,
     * and a subset of UTF-8) you're fine.  However once you
     * step out of that comfy little world things get mad, bad,
     * and dangerous to know.
     *
     * The following code is based on SJM's work with FoF
     * @link http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
     * if passed an empty string as the encoding.
     *
     * @access     private
     */
    function xml_php4_create_parser($source, $in_enc, $detect) {
        if ( !$detect ) {
            return array(xml_parser_create($in_enc), $source);
        }

        if (!$in_enc) {
            if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $source, $m)) {
                $in_enc = strtoupper($m[1]);
                $this->xml_source_encoding = $in_enc;
            }
            else {
                $in_enc = 'UTF-8';
            }
        }

        if ($this->xml_known_encoding($in_enc)) {
            return array(xml_parser_create($in_enc), $source);
        }

        // the dectected encoding is not one of the simple encodings PHP knows

        // attempt to use the iconv extension to
        // cast the XML to a known encoding
        // @link http://php.net/iconv
        if (function_exists('iconv'))  {
            $encoded_source = iconv($in_enc,'UTF-8', $source);
            if ($encoded_source) {
                return array(xml_parser_create('UTF-8'), $encoded_source);
            }
        }

        // iconv didn't work, try mb_convert_encoding
        // @link http://php.net/mbstring
        if(function_exists('mb_convert_encoding')) {
            $encoded_source = mb_convert_encoding($source, 'UTF-8', $in_enc );
            if ($encoded_source) {
                return array(xml_parser_create('UTF-8'), $encoded_source);
            }
        }

        // else
        trigger_error("Feed is in an unsupported character encoding. ($in_enc) " .
                     "You may see strange artifacts, and mangled characters.", E_USER_ERROR);
    }

    /**
     * Checks if the given encoding is one of the known encodings
     *
     *
     * @access     private
     */
    function xml_known_encoding($enc)
    {
        $enc = strtoupper($enc);
        if ( in_array($enc, $this->xml_known_encodings) ) {
            return $enc;
        }
        else {
            return false;
        }
    }


    /**
     * Loads a specific URL, this method is used by the user friendly methods
     *
     */
    function load_url($action = '', $method = 'get', $params = array(), $expected_http_code = 200)
    {
//	echor("cc:load_url($action)");

//	echor(debug_backtrace());

	
	$this->http_send($this->api_url . $action, $method, $params);

        // handle status codes
        if(intval($expected_http_code) === $this->http_response_code):
            if($this->http_content_type):
                return $this->xml_to_array($this->http_response_body);
            else:
                return $this->http_response_body; /* downloads the file */
            endif;
        else:
            $this->last_error  = "Invalid status code {$this->http_response_code}";

            // if their was an error sometimes the body contains useful info
            return false;
        endif;
    }


    /**
     * All methods below are prefixed with http_
     * These are all used to communicate with the CC server over HTTPS
     *
     */


    /**
     * Sets the Content-Type header value used for all HTTP requests
     *
     * @access     private
     */
    function http_set_content_type($content_type)
    {
        $this->http_content_type = $content_type;
    }

    /**
     * Simple method which calls PHP's @see parse_url function and saves the result to a variable
     *
     *
     * @access     private
     */
    function http_parse_request_url($url) {
        $this->http_url_bits = parse_url($url);
    }


    /**
     * This method adds the necessary HTTP auth headers to communicate with the API
     *
     *
     * @access     private
     */
    function http_auth_headers() {
        if($this->http_user || $this->http_pass):
            $this->http_headers_add('Authorization', " Basic ".base64_encode($this->http_user . ":" . $this->http_pass));
        endif;
    }


    /**
     * This method takes care of escaping the values sent with the http request
     *
     * @param    array        An array of params to escape
     * @param    array        The HTTP method eg. GET
     *
     * @access     private
     */
    function http_serialize_params($params) {
        $query_string = array();
        if(is_array($params)):
            foreach($params as $key => $value):
                if(is_array($value)):
                    foreach($value as $k => $fieldvalue):
                        $query_string[] = urlencode($key) . '=' . rawurlencode($fieldvalue);
                    endforeach;
                else:
                    $query_string[] = urlencode($key) . '=' . rawurlencode($value);
                endif;
            endforeach;
        else:
            return $params;
        endif;
        return implode('&', $query_string);
    }



    /**
     * This does most the work of creating the HTTP request
     *
     * @param    string        The path of the resource to request eg. /index.php
     * @param    string        The method to use to make the request eg. GET
     * @param    array        An array of params to use for this HTTP request, eg. post data
     * @param    array        An array of additional HTTP headers to send along with the request
     *
     * @access     private
     */
    function http_send($path, $method, $params = array(), $headers = array())
    {
        $this->http_response = '';
        $this->http_response_code = '';
        $this->http_method = $method;
        $this->http_parse_request_url($path);
        $this->http_headers_merge($headers);

        if(is_array($params)):
            $params = $this->http_serialize_params($params);
        endif;

        $method = strtoupper($method);

        $the_host = $this->http_url_bits['host'];
        $the_path = (isset($this->http_url_bits['path'])&&trim($this->http_url_bits['path'])!='') ? $this->http_url_bits['path'] : '';
        $the_path .= (isset($this->http_url_bits['query'])&&trim($this->http_url_bits['query'])!='') ? '?'.$this->http_url_bits['query'] : '';

        $this->http_headers_add('', "$method $the_path HTTP/1.1");
        $this->http_headers_add('Host', $the_host);

        if($this->http_content_type) {
            $this->http_headers_add('Content-Type', $this->http_content_type);
        }

        $this->http_headers_add('User-Agent', $this->http_user_agent);
        $this->http_headers_add('Content-Length', strlen($params));
        $this->http_headers_add('Accept', 'application/atom+xml'); // Added in 2.1 for Events marketing

        $request = $this->http_build_request_headers();

		$body = '';
        if(trim($params) != ''):
            $body = "$params{$this->http_linebreak}";
        endif;

        $this->http_request = $request;
		
    /*
		if($this->http_url_bits['scheme']=='https'):
            $url = "https://$the_host";
        else:
            $url = "http://$the_host";
        endif;
	*/
        
        $url = "https://$the_host";
        
        $args = array(
        	'body' => $body, 
        	'headers'=> $request, 
        	'method' => strtoupper($method), // GET, POST, PUT, DELETE, etc.
        	'sslverify' => false,
        	'timeout' => $this->http_request_timeout, 
        	'httpversion' => '1.1'
        );
    	
        $response = wp_remote_request($url.$the_path, $args);
        
#        r(array('url' => $url.$the_path, 'args' => $args, 'response' => $response));
        
 		if($response && !is_wp_error($response)) {
			$this->http_response = $response;
			$this->http_parse_response();
			return true;
		} elseif(is_wp_error($response)) {
			$this->last_error = $response->get_error_message();
		}
		return false;
    }


    /**
     * determine if a string can represent a number in hexadecimal
     *
     * @param string $hex
     * @return boolean true if the string is a hex, otherwise false
     */
    function is_hex($hex) {
        // regex is for weenies
        $hex = strtolower(trim(ltrim($hex,"0")));
        if (empty($hex)) { $hex = 0; };
        $dec = hexdec($hex);
        return ($hex == dechex($dec));
    }

    /**
     * This method calls other methods
     * It is mainly here so we can do everything in the correct order, according to HTTP spec
     *
     * @return    string        A string containing the entire HTTP request
     *
     * @access     private
     */
    function http_build_request_headers()
    {
        $this->http_auth_headers();
        $this->http_headers_add('Connection', "Close{$this->http_linebreak}");
        $request = $this->http_headers_to_s($this->http_request_headers);
        $this->http_request_headers = array();
        return $request;
    }


    /**
     * This method parses the raw http response into local variables we use later on
     *
     *
     * @access     private
     */
    function http_parse_response()
    {
    	if(empty($this->http_response)) { return false; }
        $headers = wp_remote_retrieve_headers($this->http_response);
        
        foreach($headers as $header => $value) {
        	if(is_string($value)) { $value = trim($value); }
        	$this->http_response_headers[$header] = $value;
        }
        
        $this->http_response_body = $body = wp_remote_retrieve_body($this->http_response);
        $this->http_response_code = wp_remote_retrieve_response_code($this->http_response);

        $this->http_set_content_type($this->http_default_content_type);
    }


    /**
     * This method converts an array of request headers into a correctly formatted HTTP request header
     *
     * @param    array        The array of headers to convert
     * @return    string        A string that can be used within an HTTP request
     *
     * @access     private
     */
    function http_headers_to_s($headers) {
        $string = '';
        if(is_array($headers)):
            foreach ($headers as $header => $value) {
                if(trim($header) != '' && !is_numeric($header)):
                    $string .= "$header: $value{$this->http_linebreak}";
                else:
                    $string .= "$value{$this->http_linebreak}";
                endif;
            }
        endif;
        return $string;
    }


    /**
     * This method allows us to add a specific header to the @see $http_request_headers array
     *
     * @param    string        The name of the header to add
     * @param    string        The value of the header to add
     *
     * @access     private
     */
    function http_headers_add($header, $value) {
        if(trim($header) != '' && !is_numeric($header)):
            $this->http_request_headers[$header] = $value;
        else:
            $this->http_request_headers[] = $value;
        endif;
    }


    /**
     * This merges the given array with the @see $http_request_headers array
     *
     * @param    array        The associative array of headers to merge
     *
     * @access     private
     */
    function http_headers_merge($headers) {
        $this->http_request_headers = array_merge($this->http_request_headers, $headers);
    }



    /**
     * This gets a specific request header from the @see $http_request_headers array
     *
     * @param    string        The name of the header to retrieve
     *
     * @access     private
     */
    function http_headers_get($header) {
        return $this->http_request_headers[$header];
    }


    /**
     * This gets the response code of the last HTTP request
     *
     * @return    int        The response code, eg. 200
     *
     * @access     private
     */
    function http_headers_get_response_code() {
        return $this->http_response_code;
    }


    /**
     * Parses the response headers and response code into a readable format
     *
     * @param    array    An associative array of headers to include in the HTTP request
     *
     * @access     private
     */
    function http_parse_headers($headers) {
        $replace = ($this->http_linebreak == "\n" ? "\r\n" : "\n");
        $headers = str_replace($replace, $this->http_linebreak, trim($headers));
        $headers = explode($this->http_linebreak, $headers);
        $this->http_response_headers = array();
        if (preg_match('/^HTTP\/\d\.\d (\d{3})/', $headers[0], $matches)) {
          $this->http_response_code = intval($matches[1]);
          array_shift($headers);
        }
        if($headers):
            foreach ($headers as $string) {
              list($header, $value) = explode(': ', $string, 2);
              $this->http_response_headers[$header] = trim($value);
            }
        endif;
    }



    /**
     * Returns a friendly error message for the given HTTP status error code
     * This can be used to better understand a status code returned by the API
     *
     *
     * @access     private
     */
    function http_get_response_code_error($code)
    {
    	if(empty($code)) { return ''; }
        $errors = array(
        200 => '<strong>Success</strong> - The request was successful',
        201 => '<strong>Created (Success)</strong> - The request was successful. The requested object/resource was created.',
        400 => '<p><strong>Invalid Request (400)</strong></p><p>There are many possible causes for this error, but most commonly there is a problem with the structure or content of XML your application provided. Please <a href="http://wordpress.org/tags/constant-contact-api?forum_id=10">Let the plugin developers know about the issue</a>, and make sure to mention what you were doing when you got this error.</p>',
        401 => '<p><strong>This plugin is not configured with a valid Constant Contact account.</strong></p>
				<p>Please enter a valid username and password then press the Save Changes button before continuing.</p>',
        404 => '<p><strong>URL Not Found (404)</strong></p> <p>The URI which was provided was incorrect.  Please <a href="http://wordpress.org/tags/constant-contact-api?forum_id=10">Let the plugin developers know about the issue</a>, and make sure to mention what you were doing when you got this error.</p>',
        409 => '<p><strong>Conflict (409)</strong></p><p>There is a problem with the action you are trying to perform. Commonly, you are trying to "Create" (POST) a resource which already exists such as a Contact List or Email Address that already exists. In general, if a resource already exists, an application can "Update" the resource with a "PUT" request for that resource.</p>',
        415 => '<p><strong>Unsupported Media Type (415)</strong></p><p>The Media Type (Content Type) of the data you are sending does not match the expected Content Type for the specific action you are performing on the specific Resource you are acting on. Please <a href="http://wordpress.org/tags/constant-contact-api?forum_id=10">Let the plugin developers know about the issue</a>, and make sure to mention what you were doing when you got this error.</p>',
        500  => '<p><strong>Server Error (500)</strong></p><p>Constant Contact is likely having server issues. Even though it is likely a Constant Contact issue, please <a href="http://wordpress.org/tags/constant-contact-api?forum_id=10">Let the plugin developers know about the issue</a>, and make sure to mention what you were doing when you got this error.</p>',
        );

        if(array_key_exists($code, $errors)):
            return $errors[$code];
        endif;

        return '';
    }
// ENDOF CLASS
}
?>