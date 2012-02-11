<?php
/**
 * Reports listing js file.
 *
 * Handles javascript stuff related to reports list function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Reports Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
	<?php @require_once(APPPATH.'views/map_common_js.php'); ?>
	
	// Tracks the current URL parameters
	var urlParameters = <?php echo $url_params; ?>;
	var deSelectedFilters = [];
	
	// Lat/lon and zoom for the map
	var latitude = <?php echo $latitude; ?>;
	var longitude = <?php echo $longitude; ?>;
	var defaultZoom = <?php echo $default_zoom; ?>;
	
	// Track the current latitude and longitude on the alert radius map
	var currLat, currLon;
	
	// Tracks whether the map has already been loaded
	var mapLoaded = 0;
	
	// Map object
	var map = null;
	var radiusMap = null;
	
	if (urlParameters.length == 0)
	{
		urlParameters = {};
	}
	
  $(document).ready(function() { 
    createIncidentMap();
    showIncidentMap();
    /*
    var map = new OpenLayers.Map("map_canvas");

    var ol_wms = new OpenLayers.Layer.WMS(
        "OpenLayers WMS",
        "http://vmap0.tiles.osgeo.org/wms/vmap0",
        {layers: "basic"}
    );

    var dm_wms = new OpenLayers.Layer.WMS(
        "Canadian Data",
        "http://www2.dmsolutions.ca/cgi-bin/mswms_gmap",
        {
            layers: "bathymetry,land_fn,park,drain_fn,drainage," +
                    "prov_bound,fedlimit,rail,road,popplace",
            transparent: "true",
            format: "image/png"
        },
        {isBaseLayer: false, visibility: false}
    );

    map.addLayers([ol_wms, dm_wms]);
    map.addControl(new OpenLayers.Control.LayerSwitcher());
    map.zoomToMaxExtent();
     */
  });
	
	/**
	 * Creates the map and sets the loaded status to 1
	 */
	function createIncidentMap()
	{
		// Creates the map
		map = createMap('map_canvas', latitude, longitude, defaultZoom);
		map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
		
		mapLoaded = 1;
  }

	/**
	 * Makes a url string for the map stuff
	 */
	function makeUrlParamStr(str, params, arrayLevel)	 
	{
		//make sure arrayLevel is initialized
		var arrayLevelStr = "";
		if(arrayLevel != undefined)
		{
			arrayLevelStr = arrayLevel;
		}
		
		var separator = "";
		for(i in params)
		{
			//do we need to insert a separator?
			if(str.length > 0)
			{
				separator = "&";
			}
			
			//get the param
			var param = params[i];
	
			//is it an array or not
			if($.isArray(param))
			{
				if(arrayLevelStr == "")
				{
					str = makeUrlParamStr(str, param, i);
				}
				else
				{
					str = makeUrlParamStr(str, param, arrayLevelStr + "%5B" + i + "%5D");
				}
			}
			else
			{
				if(arrayLevelStr == "")
				{
					str +=  separator + i + "=" + param.toString();
				}
				else
				{
					str +=  separator + arrayLevelStr + "%5B" + i + "%5D=" + param.toString();
				}
			}
		}
		
		return str;
	}
	
	
	/**
	 * Handles display of the incidents current incidents on the map
	 * This method is only called when the map view is selected
	 */
	function showIncidentMap()
	{
		// URL to be used for fetching the incidents
		fetchURL = '<?php echo url::site().'json/index' ;?>';
		
		// Generate the url parameter string
		parameterStr = makeUrlParamStr("", urlParameters)
		
		// Add the parameters to the fetch URL
		fetchURL += '?' + parameterStr;
		
		// Fetch the incidents
		
		// Set the layer name
		var layerName = '<?php echo Kohana::lang('ui_main.reports')?>';
				
		// Get all current layers with the same name and remove them from the map
		currentLayers = map.getLayersByName(layerName);
		for (var i = 0; i < currentLayers.length; i++)
		{
			map.removeLayer(currentLayers[i]);
		}
				
		// Styling for the incidents
		reportStyle = new OpenLayers.Style({
			pointRadius: "8",
			fillColor: "#30E900",
			fillOpacity: "0.8",
			strokeColor: "#197700",
			strokeWidth: 3,
			graphicZIndex: 1
		});
				
		// Apply transform to each feature before adding it to the layer
		preFeatureInsert = function(feature)
		{
			var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
			OpenLayers.Projection.transform(point, proj_4326, proj_900913);
		};
				
		// Create vector layer
		vLayer = new OpenLayers.Layer.Vector(layerName, {
			projection: map.displayProjection,
			extractAttributes: true,
			styleMap: new OpenLayers.StyleMap({'default' : reportStyle}),
			strategies: [new OpenLayers.Strategy.Fixed()],
			protocol: new OpenLayers.Protocol.HTTP({
				url: fetchURL,
				format: new OpenLayers.Format.GeoJSON()
			})
		});
				
		// Add the vector layer to the map
		map.addLayer(vLayer);
		
		// Add feature selection events
		addFeatureSelectionEvents(map, vLayer);
	}
	
	/**
	 * Clears the filter for a particular section
	 * @param {string} parameterKey: Key of the parameter remove from the list of url parameters
	 * @param {string} filterClass: CSS class of the section containing the filters
	 */
	function removeParameterKey(parameterKey, filterClass)
	{
		if (typeof parameterKey == 'undefined' || typeof parameterKey != 'string')
			return;
		
		if (typeof $("."+filterClass) == 'undefined')
			return;
		
		if(parameterKey == "cff") //It's Cutom Form Fields baby
		{
			$.each($("input[id^='custom_field_']"), function(i, item){
				if($(item).attr("type") == "checkbox" || $(item).attr("type") == "radio")
				{
					$(item).removeAttr("checked");
				}
				else
				{
					$(item).val("");
				}
			});			
			$("select[id^='custom_field_']").val("---NOT_SELECTED---");
		}
		else //it's just some simple removing of a class
		{
			// Deselect
			$.each($("." + filterClass +" li a.selected"), function(i, item){
				$(item).removeClass("selected");
			});			
			
			//if it's the location filter be sure to get rid of sw and ne
			if(parameterKey == "start_loc" || parameterKey == "radius")
			{
				delete urlParameters["sw"];
				delete urlParameters["ne"];
			}
		}
		
		// Remove the parameter key from urlParameters
		delete urlParameters[parameterKey];
	}
	
