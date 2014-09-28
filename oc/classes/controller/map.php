<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Map extends Controller {

	public function action_index()
	{
        $this->before('/pages/maps');

        $this->template->title  = __('Map');

        $this->template->height = Core::get('height','100%');
        $this->template->width  = Core::get('width','100%');
        $this->template->zoom   = Core::get('zoom',core::config('advertisement.map_zoom'));

        $this->template->center_lon = Core::get('lon',core::config('advertisement.center_lon'));
        $this->template->center_lat = Core::get('lat',core::config('advertisement.center_lat'));
        
        //last ads, you can modify this value at: advertisement.feed_elements
        $ads = DB::select('a.seotitle')
                ->select(array('c.seoname','category'),'a.title','a.description','a.published','a.address',array('id_ad','lat'),array('id_ad','lon'))
                ->from(array('ads', 'a'))
                ->join(array('categories', 'c'),'INNER')    
                ->on('a.id_category','=','c.id_category')
                ->where('a.status','=',Model_Ad::STATUS_PUBLISHED)
                ->where('a.address','IS NOT',NULL);

        //if only 1 ad
        if (is_numeric(core::get('id_ad')))
            $ads = $ads->where('id_ad','=',core::get('id_ad'));

        $ads = $ads->order_by('published','desc')
                ->limit(Core::config('advertisement.map_elements'))
                ->as_object()
                ->cached()
                ->execute();

        //ads to return
        $advertisements = array();

        foreach($ads as $a)
        {
            if (strlen($a->address)>5)
            {
                //only add ads we could find the location
                if (($coords  = self::address_coords($a->address))!==FALSE)
                {
                    $a->lat  = $coords['lat'];
                    $a->lon  = $coords['lon'];
                    //adding only those we found a coord
                    $advertisements[] = $a;
                }
            }
        }



        $this->template->ads = $advertisements;
	
	}

    /**
     * get geocode lat/lon points for given address from google
     * 
     * @param string $address
     * @return bool|array false if can't be geocoded, array or geocdoes if successful
     */
    public static function address_coords($address) 
    {
        $url = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address='.rawurlencode($address);

        //try to get the json from the cache
        $coords = Core::cache($url);

            //not cached :(
        if ($coords === NULL)
        {
            $coords = FALSE;

            //get contents from google
            if($result = core::curl_get_contents($url)) 
            {
                $result = json_decode($result);

                //not found :()
                if($result->status!="OK")
                    $coords = FALSE;
                else
                {
                    $coords['lat'] = $result->results[0]->geometry->location->lat;
                    $coords['lon'] = $result->results[0]->geometry->location->lng;
                }
            }

            //save the json
            Core::cache($url,$coords,strtotime('+7 day'));
        }
        
        return $coords;       
    }


    public function action_index2()
    {
        require_once Kohana::find_file('vendor', 'php-googlemap/GoogleMap','php');
        
        $this->before('/pages/maps');

        $this->template->title  = __('Map');

        $height = Core::get('height','100%');
        $width  = Core::get('width','100%');

        $map = new GoogleMapAPI();
        $map->setWidth($width);
        $map->setHeight($height);
        $map->disableSidebar();
        $map->setMapType('map');
        $map->setZoomLevel(Core::get('zoom',core::config('advertisement.map_zoom')));
        
        //$map->mobile = TRUE;
        $atributes = array("target='_blank'");
        if ( core::get('controls')==0 )
        {
            $map->disableMapControls();
            $map->disableTypeControls();
            $map->disableScaleControl();
            $map->disableZoomEncompass();
            $map->disableStreetViewControls();
            $map->disableOverviewControl();
        }
        
        //only 1 marker
        if ( core::get('address')!='' )
        {
            $map->addMarkerByAddress(core::get('address'), core::get('address'));
        }
        else
        {

            //last ads, you can modify this value at: advertisement.feed_elements
            $ads = DB::select('a.seotitle')
                    ->select(array('c.seoname','category'),'a.title','a.published','a.address')
                    ->from(array('ads', 'a'))
                    ->join(array('categories', 'c'),'INNER')
                    ->on('a.id_category','=','c.id_category')
                    ->where('a.status','=',Model_Ad::STATUS_PUBLISHED)
                    ->where('a.address','IS NOT',NULL)
                    ->order_by('published','desc')
                    ->limit(Core::config('advertisement.map_elements'))
                    ->as_object()
                    ->cached()
                    ->execute();

                    
            foreach($ads as $a)
            {
                //d($a);
                if (strlen($a->address)>3)
                {
                    $url= Route::url('ad',  array('category'=>$a->category,'seotitle'=>$a->seotitle));
                    $map->addMarkerByAddress($a->address, $a->title, HTML::anchor($url, $a->title, $atributes) );
                }
            }

            //only center if not a single ad
            $map->setCenterCoords(Core::get('lon',core::config('advertisement.center_lon')),Core::get('lat',core::config('advertisement.center_lat')));
        }

        $this->template->map = $map;
    
    }



} // End map
