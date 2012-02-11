<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mobile Controller
 * Generates KML with PlaceMarkers and Category Styles
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Mobile Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Map_Controller extends Template_Controller {

	public $auto_render = TRUE;
	public $mobile = TRUE;
	
	// Cacheable Controller
	public $is_cachable = TRUE;
	
	// Main template
    public $template = 'mobile/map_layout';

	// Table Prefix
	protected $table_prefix;

  public function __construct()
  {
    parent::__construct();
  }
	
	/**
	 * Displays a list of map
	 * @param boolean $category_id If category_id is supplied filter by
   * that category
   *
   * TODO: make the argument a list of categories
	 */
	public function index($category_id = false)
  {
		// Load Header & Footer
    $this->template->content = new View('mobile/map');
    $this->template->content->site_name = Kohana::config('settings.site_name');
		$this->template->content->api_url = Kohana::config('settings.api_url');

    $this->template->content->js = new View('mobile/map_js');
    $this->template->content->js->url_params = json_encode($_GET);

		// Set the latitude and longitude
		$this->template->content->js->latitude = Kohana::config('settings.default_lat');
		$this->template->content->js->longitude = Kohana::config('settings.default_lon');
		$this->template->content->js->default_map = Kohana::config('settings.default_map');
    $this->template->content->js->default_zoom = Kohana::config('settings.default_zoom');
    /*
     */
	}
	
}
