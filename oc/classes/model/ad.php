<?php defined('SYSPATH') or die('No direct script access.');
/**
 * description...
 *
 * @author		Chema <chema@open-classifieds.com>
 * @package		OC
 * @copyright	(c) 2009-2013 Open Classifieds Team
 * @license		GPL v3
 * 
 */
class Model_Ad extends ORM {

    /**
     * Table name to use
     *
     * @access	protected
     * @var		string	$_table_name default [singular model name]
     */
    protected $_table_name = 'ads';

    /**
     * Column to use as primary key
     *
     * @access	protected
     * @var		string	$_primary_key default [id_ad]
     */
    protected $_primary_key = 'id_ad';

    protected $_belongs_to = array(
        'user'		 => array('foreign_key' => 'id_user'),
        'category'	 => array('foreign_key' => 'id_category'),
    	'location'	 => array('foreign_key' => 'id_location'),
    );


    /**
     * @var  array  ORM Dependency/hirerachy
     */
    protected $_has_many = array(
        'visits' => array(
            'model'       => 'visit',
            'foreign_key' => 'id_ad',
        ),
    );

    /**
     * status constants
     */
    const STATUS_NOPUBLISHED = 0; //first status of the item, not published. This status send ad to moderation always. Until it gets his status changed 
    const STATUS_PUBLISHED   = 1; // ad it's available and published
    const STATUS_UNCONFIRMED = 20; // this status is for advertisements that need to be confirmed by email,
    const STATUS_SPAM        = 30; // mark as spam
    const STATUS_UNAVAILABLE = 50; // item unavailable but previously was
    

    /**
     * moderation status
     */
    const POST_DIRECTLY         = 0; // create new ad directly 
    const MODERATION_ON         = 1; // new ad after creation goes to moderation
    const PAYMENT_ON            = 2; // redirects to payment and after paying there is no moderation
    const EMAIL_CONFIRMATION    = 3; // sends email to confirm ad, until then is in moderation 
    const EMAIL_MODERATION      = 4; // sends email to confirm, but admin needs also to validate
    const PAYMENT_MODERATION    = 5; // even after payment, admin still needs to validate
    
    //this are the moderation statuses that makes moderation link appear
    public static $moderation_status = array(self::MODERATION_ON,
                                            self::EMAIL_MODERATION , 
                                            self::PAYMENT_MODERATION);
    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
    	return array(
				        'id_ad'		=> array(array('numeric')),
				        'id_user'		=> array(array('numeric')),
				        'id_category'	=> array(array('numeric')),
				        'id_location'	=> array(),
				        'type'			=> array(),
				        'title'			=> array(array('not_empty'), array('max_length', array(':value', 145)), ),
				        'seotitle'		=> array(array('not_empty'), array('max_length', array(':value', 145)), ),
				        'description'	=> array(array('not_empty'), array('max_length', array(':value', 65535)), ),
				        'address'		=> array(array('max_length', array(':value', 145)), ),
				        'phone'			=> array(array('max_length', array(':value', 30)), ),
				        'status'		=> array(array('numeric')),
				        'has_images'	=> array(array('numeric')),
				    );
    }

    /**
     * Label definitions for validation
     *
     * @return array
     */
    public function labels()
    {
    	return array(
			        'id_ad'		=> 'Id ad',
			        'id_user'		=> __('User'),
			        'id_category'	=> __('Category'),
			        'id_location'	=> __('Location'),
			        'type'			=> __('Type'),
			        'title'			=> __('Title'),
			        'seotitle'		=> __('SEO title'),
			        'description'	=> __('Description'),
			        'address'		=> __('Address'),
			        'price'			=> __('Price'),
			        'phone'			=> __('Phone'),
			        'ip_address'	=> __('Ip address'),
			        'created'		=> __('Created'),
			        'published'		=> __('Published'),
			        'status'		=> __('Status'),
			        'has_images'	=> __('Has images'),
			    );
    }
    
    /**
     * 
     * formmanager definitions
     * @param $form
     * @return $insert
     */
    public function form_setup($form)
    {
        $insert = DB::insert('ads', array('title', 'description'))
                            ->values(array($form['title'], $form['description']))
                            ->execute();
                            return $insert;
    }


    /**
     * generate seo title. return the title formatted for the URL
     *
     * @param string title
     * @return $seotitle (unique string)  
     */
    
    public function gen_seo_title($title)
    {
        $ad = new self;

        $title = URL::title($title);
        $seotitle = $title;

        //find a ad same seotitle
        $a = $ad->where('seotitle', '=', $seotitle)->and_where('id_ad', '!=', $this->id_ad)->limit(1)->find();
        
        if($a->loaded())
        {
            $cont = 1;
            $loop = TRUE;
            do {
                $attempt = $title.'-'.$cont;
                $ad = new self;
                unset($a);
                $a = $ad->where('seotitle', '=', $attempt)->limit(1)->find();

                if(!$a->loaded())
                {
                    $loop = FALSE;
                    $seotitle = $attempt;
                }
                else $cont++;
            } while ( $loop );
        }

        return $seotitle;
    }


    /**
     *  Create single table for each advertisement hit
     * 
     */
    public function count_ad_hit()
    {
        $hits = 0;
        if (!Model_Visit::is_bot() AND $this->loaded() AND core::config('advertisement.count_visits')==1)
        {
            if(!Auth::instance()->logged_in())
                $visitor_id = NULL;
            else
                $visitor_id = Auth::instance()->get_user()->id_user;

            //insert new visit
            if ($this->id_user!=$visitor_id)
                $new_hit = DB::insert('visits', array('id_ad', 'id_user', 'ip_address'))
                                    ->values(array($this->id_ad, $visitor_id, ip2long(Request::$client_ip)))
                                    ->execute();

            //count how many matches are found 
            $hits = new Model_Visit();
            $hits = $hits->where('id_ad','=', $this->id_ad)->count_all();
        }
        return $hits;
        
    }

    /**
     * Gets all images
     * @return [array] [array with image names]
     */
    public function get_images()
    {
        $image_path = array();
       
        if($this->loaded() AND $this->has_images == TRUE)
        {  
            $route = $this->image_path();
            $folder = DOCROOT.$route;

            if(is_dir($folder))
            { 
                foreach (new DirectoryIterator($folder) as $file) 
                {   
                    if(!$file->isDot())
                    {   
                        $key = explode('_', $file->getFilename());
                        $key = end($key);
                        $key = explode('.', $key);
                        $key = (isset($key[0])) ? $key[0] : NULL ;

                        if(is_numeric($key))
                        {
                            $type = (strpos($file->getFilename(), 'thumb_') === 0) ? 'thumb' : 'image' ;
                            $image_path[$key][$type] = $route.$file->getFilename();
                        }
                    }
                }
            }
        }

        ksort($image_path);

        return $image_path;
    }

    /**
     * Gets the first image, and checks type of $type
     * @param  string $type [type of image (image or thumb) ]
     * @return string       [image path]
     */
    public function get_first_image($type = 'thumb')
    {
        $images = $this->get_images();

        if(count($images) >= 1)
            $first_image = reset($images);

        return (isset($first_image[$type])) ? $first_image[$type] : NULL ;
    }


    /**
     * image_path make unique dir path with a given date and id
     * @return string path
     */
    public function image_path()
    { 
        if (!$this->loaded())
            return FALSE;

        $obj_date = date_parse($this->created); // convert date to array 
        
        $year = $obj_date['year'];
        
        // check for length, because original path is with 2 integers 
        if(strlen($obj_date['month']) != 2)
            $month = '0'.$obj_date['month'];
        else
            $month = $obj_date['month'];
        
        if(strlen($obj_date['day']) != 2)
            $day = '0'.$obj_date['day'];
        else
            $day = $obj_date['day'];

        $path = 'images/'.$year.'/'.$month.'/'.$day.'/'.$this->id_ad.'/';       
        
        //check if path is a directory
        if ( ! is_dir($path) )
        {
            //not a directory, try to create it
            if (! @mkdir($path, 0755, TRUE))
                return FALSE;//failed creation :()
        }

        return $path;
    }

    /**
     * save_image upload images with given path
     * 
     * @param array image
     * @return bool
     */
    public function save_image($image)
    {
        if (!$this->loaded())
            return FALSE;

        $seotitle = $this->seotitle;

        if ( 
        ! Upload::valid($image) OR
        ! Upload::not_empty($image) OR
        ! Upload::type($image, explode(',',core::config('image.allowed_formats'))) OR
        ! Upload::size($image, core::config('image.max_image_size').'M'))
        {
            if (Upload::not_empty($image) && ! Upload::type($image, explode(',',core::config('image.allowed_formats')))){
                Alert::set(Alert::ALERT, $image['name'].' '.sprintf(__('Is not valid format, please use one of this formats "%s"'),core::config('image.allowed_formats')));
                return;
            }
            if( ! Upload::size($image, core::config('image.max_image_size').'M')){
                Alert::set(Alert::ALERT, $image['name'].' '.sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('general.max_image_size')));
                return;
            }
            if( ! Upload::not_empty($image))
                return;
        }
          
        if ($image !== NULL)
        {
            $path           = $this->image_path();
            if ($path === FALSE)
            {
                Alert::set(Alert::ERROR, 'model\ad.php:save_image(): '.__('Image folder is missing and cannot be created with mkdir. Please correct to be able to upload images.'));
                return FALSE;
            }
            $directory      = DOCROOT.$path;
            $image_quality  = core::config('image.quality');
            $width          = core::config('image.width');
            $width_thumb    = core::config('image.width_thumb');
            $height_thumb   = core::config('image.height_thumb');
            $height         = core::config('image.height');

            if( ! is_numeric($height)) // when installing this field is empty, to avoid crash we check here
                $height         = NULL;
            if( ! is_numeric($height_thumb))
                $height_thumb   = NULL;    
            
            // count how many files are saved 
            if (glob($directory . "*.jpg") != false)
            {
                $filecount = count(glob($directory . "*.jpg"));

                $counter = ($filecount / 2) + 1;
                
                if(file_exists($directory.$seotitle.'_'.$counter.'.jpg')) // in case we update image, we have to find available number to replace
                {
                    for($i=1; $i<=core::config('advertisement.num_images'); $i++)
                    {
                        $counter = $i;
                        if(!file_exists($directory.$seotitle.'_'.$counter.'.jpg'))
                        {
                            break;
                        }
                    }
                }
            }
            else
                $counter = 1;
            
            if ($file = Upload::save($image, NULL, $directory))
            {
                $filename_thumb     = 'thumb_'.$seotitle.'_'.$counter.'.jpg';
                $filename_original  = $seotitle.'_'.$counter.'.jpg';
                 
                /*WATERMARK*/
                if(core::config('image.watermark')==TRUE AND is_readable(core::config('image.watermark_path')))
                {
                    $mark = Image::factory(core::config('image.watermark_path')); // watermark image object
                    $size_watermark = getimagesize(core::config('image.watermark_path')); // size of watermark
                  
                    if(core::config('image.watermark_position') == 0) // position center
                    {
                        $wm_left_x = $width/2-$size_watermark[0]/2; // x axis , from left
                        $wm_top_y = $height/2-$size_watermark[1]/2; // y axis , from top
                    }
                    elseif (core::config('image.watermark_position') == 1) // position bottom
                    {
                        $wm_left_x = $width/2-$size_watermark[0]/2; // x axis , from left
                        $wm_top_y = $height-10; // y axis , from top
                    }
                    elseif(core::config('image.watermark_position') == 2) // position top
                    {
                        $wm_left_x = $width/2-$size_watermark[0]/2; // x axis , from left
                        $wm_top_y = 10; // y axis , from top
                    }
                }   
                /*end WATERMARK variables*/

                //if original image is bigger that our constants we resize
                $image_size_orig = getimagesize($file);
                
                    if($image_size_orig[0] > $width || $image_size_orig[1] > $height)
                    {
                        if(core::config('image.watermark') AND is_readable(core::config('image.watermark_path'))) // watermark ON
                            Image::factory($file)
                                ->resize($width, $height, Image::AUTO)
                                ->watermark( $mark, $wm_left_x, $wm_top_y) // CUSTOM FUNCTION (kohana)
                                ->save($directory.$filename_original,$image_quality); 
                        else 
                            Image::factory($file)
                                ->resize($width, $height, Image::AUTO)
                                ->save($directory.$filename_original,$image_quality);    
                    }
                    //we just save the image changing the quality and different name
                    else
                    {
                        if(core::config('image.watermark') AND is_readable(core::config('image.watermark_path')))
                            Image::factory($file)
                                ->watermark( $mark, $wm_left_x, $wm_top_y) // CUSTOM FUNCTION (kohana)
                                ->save($directory.$filename_original,$image_quality);
                        else
                            Image::factory($file)
                                ->save($directory.$filename_original,$image_quality); 
                    }
                

                //creating the thumb and resizing using the the biggest side INVERSE
                Image::factory($file)
                    ->resize($width_thumb, $height_thumb, Image::INVERSE)
                    ->save($directory.$filename_thumb,$image_quality);

                //check if the height or width of the thumb is bigger than default then crop
                if ($height_thumb!==NULL)
                {
                    $image_size_orig = getimagesize($directory.$filename_thumb);
                    if ($image_size_orig[1] > $height_thumb || $image_size_orig[0] > $width_thumb)
                    Image::factory($directory.$filename_thumb)
                                ->crop($width_thumb, $height_thumb)
                                ->save($directory.$filename_thumb); 
                }
                // Delete the temporary file
                @unlink($file);
                return TRUE;
            }
            else 
            {
                Alert::set(Alert::ALERT, __('Something went wrong with uploading pictures, please check format'));
                return FALSE;
            }
        }   
    }

    /**
     * Deletes image from edit ad
     * @return bool
     */

    public function delete_images()
    {
        if (!$this->loaded())
            return FALSE;

        $img_path = DOCROOT.$this->image_path();

        if (!is_dir($img_path))
            return FALSE;

        File::delete($img_path);

        return TRUE;
    }

    /**
     * tells us if this ad can be contacted
     * @return bool 
     */
    public function can_contact()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND core::config('advertisement.contact') != FALSE )
            {
                return TRUE;
            }
        }
    
        return FALSE;
    }

    /**
     * prints the map script from the view
     * @return string HTML or false in case not loaded
     */
    public function map()
    {
        if($this->loaded())
        {
            if (strlen($this->address)>5 AND core::config('advertisement.map')==1 )
            {
                return View::factory('pages/ad/map',array('id_ad'=>$this->id_ad))->render();
            }
        }
    
        return FALSE;
    }
    /**
     * prints the QR code script from the view
     * @return string HTML or false in case not loaded
     */
    public function qr()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND core::config('advertisement.qr_code')==1 )
            {
                return core::generate_qr(Route::url('ad', array('controller'=>'ad','category'=>$this->category->seoname,'seotitle'=>$this->seotitle)));
            }
        }
    
        return FALSE;
    }


    /**
     * prints the comments script from the view
     * @return string HTML or false in case not loaded
     */
    public function comments()
    {
        if($this->loaded())
        {
            return $this->fbcomments().$this->disqus();
        }
    
        return FALSE;
    }

    /**
     * prints the disqus script from the view
     * @return string HTML or false in case not loaded
     */
    public function fbcomments()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND strlen(core::config('advertisement.fbcomments'))>0 )
            {
                return View::factory('pages/ad/fbcomments',
                                array('fbcomments'=>core::config('advertisement.fbcomments')))
                        ->render();
            }
        }
    
        return FALSE;
    }

    /**
     * prints the disqus script from the view
     * @return string HTML or false in case not loaded
     */
    public function disqus()
    {
        if($this->loaded())
        {
            if ($this->status == self::STATUS_PUBLISHED AND strlen(core::config('advertisement.disqus'))>0 )
            {
                return View::factory('pages/disqus',
                                array('disqus'=>core::config('advertisement.disqus')))
                        ->render();
            }
        }
    
        return FALSE;
    }
    

   /**
    * returns a list with custom field values of this ad
    * @param  boolean $show_listing only those fields that needs to be displayed on the list of ads show_listing===TRUE
    * @return array else false 
    */
    public function custom_columns($show_listing = FALSE)
    {
        if($this->loaded())
        {
            //is the admin getting the CF fields?
            $is_admin = FALSE;
            if (Auth::instance()->logged_in())
                if (Auth::instance()->get_user()->id_role == Model_Role::ROLE_ADMIN)
                    $is_admin = TRUE;

            //custom fields config, label, name and order
            $cf_config = Model_Field::get_all(FALSE);

            if(!isset($cf_config))
                return array();
            
            //getting the custom fields this advertisement has and his value          
            $active_custom_fields = array();
            foreach($this->_table_columns as $value)
            {   
                //we want only those that are custom fields
                if(strpos($value['column_name'],'cf_') !== FALSE) 
                {
                    $cf_name  = str_replace('cf_', '', $value['column_name']);
                    $cf_value = $this->$value['column_name'];

                    //if the CF has value need to be only seen by admin
                    $display = FALSE;

                    if ($is_admin === TRUE)
                        $display = TRUE;
                    elseif (isset($cf_config->$cf_name->admin_privilege))
                    {
                        if ($cf_config->$cf_name->admin_privilege==FALSE)
                            $display = TRUE;
                    }

                    if(isset($cf_value) AND $display )
                    {   
                        //formating the value depending on the type
                        switch ($cf_config->$cf_name->type) 
                        {   
                            case 'checkbox':
                                $cf_value = ($cf_value)?$cf_value:NULL;
                                break;
                            case 'radio':
                                $cf_value = $cf_config->$cf_name->values[$cf_value-1];
                                break;
                            case 'date':
                                $cf_value = Date::format($cf_value, core::config('general.date_format'));
                                break;
                        }      
                        
                        //should it be added to the listing? //I added the isset since those who update may not have this field ;)
                        if ($show_listing == TRUE AND isset($cf_config->$cf_name->show_listing)) 
                        {
                            //only to the listing
                            if ($cf_config->$cf_name->show_listing===TRUE)
                            {
                                $active_custom_fields[$cf_name] = $cf_value;
                            }                            
                        }
                        else
                            $active_custom_fields[$cf_name] = $cf_value;
                    }
       
                }
            }

            // sorting using json order
            $ad_custom_vals = array();
            foreach ($cf_config as $name => $value) 
            {
                if(isset($active_custom_fields[$name]))
                    $ad_custom_vals[$value->label] = $active_custom_fields[$name];
            }


            return $ad_custom_vals;
            
        }
        return array();
    }


    /**
     * returns related ads
     * @return view
     */
    public function related()
    {
        if($this->loaded() AND core::config('advertisement.related')>0 )
        {    
            $ads = new self();
            $ads
            ->where_open()
            ->or_where('id_category','=',$this->id_category)
            ->or_where('id_location','=',$this->id_location)
            ->where_close()
            ->where('id_ad','!=',$this->id_ad)
            ->where('status','=',self::STATUS_PUBLISHED);
            
            //if ad have passed expiration time dont show 
            if(core::config('advertisement.expire_date') > 0)
            {
                $ads->where(DB::expr('DATE_ADD( published, INTERVAL '.core::config('advertisement.expire_date').' DAY)'), '>', Date::unix2mysql());
            }

            $ads = $ads->limit(core::config('advertisement.related'))->find_all();
            //->order_by(DB::expr('RAND()'))

            return View::factory('pages/ad/related',array('ads'=>$ads))->render();
        }
    
        return FALSE;
    }


  
    public function sale( Model_User $user_buyer )
    {
        if($this->loaded())
        {    
            // decrease limit of ads, if 0 deactivate
            if($this->stock >0)
            {
                $stock = $this->stock-1;

                //deactivate the ad
                if($stock == 0)
                {
                    $this->status = Model_Ad::STATUS_UNAVAILABLE;
                    
                    //send email to owner that the he run out of stock
                    $url_edit = $user->ql('oc-panel',array( 'controller'=> 'profile', 
                                                            'action'    => 'update',
                                                            'id'        => $this->id_ad),TRUE);

                    $email_content = array( '[URL.EDIT]'        =>$url_edit,
                                            '[AD.TITLE]'        =>$this->title);

                    // send email to ad OWNER
                    $this->user->email('out-of-stock',$email_content);
                }
            
            }

            try {
                $this->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }

                
            $url_ad = Route::url('ad', array('category'=>$this->category->seoname,'seotitle'=>$this->seotitle));

            $email_content = array('[URL.AD]'      =>$url_ad,
                                    '[AD.TITLE]'     =>$this->title,
                                    '[ORDER.ID]'      =>$this->id_order,
                                    '[PRODUCT.ID]'    =>$this->id_product);
            // send email to BUYER
            $user_buyer->email('ads-purchased',$email_content);

            // send email to ad OWNER
            $this->user->email('ads-sold',$email_content);

            
        }
    
 
    }

    /**
     * tops up an advertisement
     * @return void 
     */
    public function to_top()
    {
        if($this->loaded())
        {    
            $this->published = Date::unix2mysql();
            try {
                $this->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }
    }

    /**
     * features an advertisement
     * @return void 
     */
    public function to_feature()
    {
        if($this->loaded())
        {    
            $this->featured = Date::unix2mysql(time() + (core::config('payment.featured_days') * 24 * 60 * 60));
            try {
                $this->save();
            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());
            }
        }
    }

    /**
     * paid for a category, notify user and publish ad if needed
     * @return void 
     */
    public function paid_category()
    {
        if($this->loaded())
        {    
            $moderation = core::config('general.moderation');

            $edit_url   = Route::url('oc-panel', array('controller'=>'profile','action'=>'update','id'=>$this->id_ad));
            $delete_url = Route::url('oc-panel', array('controller'=>'ad','action'=>'delete','id'=>$this->id_ad));

            if($moderation == Model_Ad::PAYMENT_ON)
            {
                $this->published = Date::unix2mysql();
                $this->status    = Model_Ad::STATUS_PUBLISHED;

                try {
                    $this->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                //notify ad is published
                $url_cont = $this->user->ql('contact', array());
                $url_ad = $this->user->ql('ad', array('category'=>$this->category->seoname,
                                                    'seotitle'=>$this->seotitle));

                $ret = $this->user->email('ads-user-check',array('[URL.CONTACT]'  =>$url_cont,
                                                            '[URL.AD]'      =>$url_ad,
                                                            '[AD.NAME]'     =>$this->title,
                                                            '[URL.EDITAD]'  =>$edit_url,
                                                            '[URL.DELETEAD]'=>$delete_url));
                
            }
            elseif($moderation == Model_Ad::PAYMENT_MODERATION)
            {
                //he paid but stays in moderation
                $url_ql = $this->user->ql('oc-panel',array( 'controller'=> 'profile', 
                                                      'action'    => 'update',
                                                      'id'        => $this->id_ad));

                $ret = $this->user->email('ads-notify',array('[URL.QL]'=>$url_ql,
                                                       '[AD.NAME]'=>$this->title,
                                                       '[URL.EDITAD]'=>$edit_url,
                                                       '[URL.DELETEAD]'=>$delete_url));     
            }
        }
    }

    /**
     * returns and order for the given product, great to check if was paid or not
     * @param  int  $id_product Model_Order::PRODUCT_
     * @return boolean/Model_Order             false if not found, Model_Order if found
     */
    public function get_order($id_product = Model_Order::PRODUCT_CATEGORY)
    {
        if ($this->loaded())
        {
            //get if theres an unpaid order for this product and this ad
            $order = new Model_Order();
            $order->where('id_ad',      '=', $this->id_ad)
                  ->where('id_user',    '=', $this->user->id_user)
                  ->where('id_product', '=', $id_product)
                  ->limit(1)->find();

            return ($order->loaded())?$order:FALSE;
        }
        return FALSE;
    }

    /**
     * saves the ads review rates recalculating it
     * @return [type] [description]
     */
    public function recalculate_rate()
    {
        if($this->loaded())
        {
            //get all the rates and divide by them
            $this->rate = Model_Review::get_ad_rate($this);
            $this->save();
            return $this->rate;
        }
        return FALSE;
    }

} // END Model_ad
