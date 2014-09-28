<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ad extends Controller {
	

	/**
	 * Publis all adver.-s without filter
	 */
	public function action_listing()
	{ 
		if(Theme::get('infinite_scroll'))
		{
			$this->template->scripts['footer'][] = '//cdn.jsdelivr.net/jquery.infinitescroll/2.0b2/jquery.infinitescroll.js';
			$this->template->scripts['footer'][] = 'js/listing.js';
		}
        $this->template->scripts['footer'][]= 'js/jquery.toolbar.js';
		$this->template->scripts['footer'][] = 'js/sort.js';
		Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
		
        
        /**
         * we get the model of category and location from controller to filter and generate urls titles etc...
         */
        
        $location = NULL;
        $location_parent = NULL;
        $location_name = NULL;

        if (Model_Location::current()->loaded())
        {
        	$location = Model_Location::current();
            if($location->id_location != 1)
            $location_name = $location->name;
        
            //adding the location parent
            if ($location->id_location_parent!=1 AND $location->parent->loaded())
                $location_parent = $location->parent;
        }  
        

        $category = NULL;
        $category_parent = NULL;
        $category_name = NULL;

        if (Model_Category::current()->loaded())
        {
            $category = Model_Category::current();
            if($category->id_category != 1)
                $category_name = $category->name;
            //adding the category parent
            if ($category->id_category_parent!=1 AND $category->parent->loaded())
                $category_parent = $category->parent;
        }
        
        //base title
        if ($category!==NULL)
        {
            //category image
            if(( $icon_src = $category->get_icon() )!==FALSE )
                Controller::$image = $icon_src;

            $this->template->title = $category_name;

            if ($category->description != '') 
				$this->template->meta_description = $category->description;	            
            else 
				$this->template->meta_description = __('All').' '.$category_name.' '.__('in').' '.core::config('general.site_name');
		}	            
        else
        {
			$this->template->title = __('all');
			if ($location!==NULL)
				if ($location->description != '')
					$this->template->meta_description = $location->description;
				else
					$this->template->meta_description = __('List of all postings in').' '.$location_name;
			else
				$this->template->meta_description = __('List of all postings in').' '.core::config('general.site_name');
        }

        //adding location titles and breadcrumbs
        if ($location!==NULL)
        {
            //in case we dont have the category image we use the location
            if(( $icon_src = $location->get_icon() )!==FALSE AND Controller::$image===NULL)
                Controller::$image = $icon_src;

            $this->template->title .= ' - '.$location->name;

            if ($location_parent!==NULL)
            {
                $this->template->title .=' ('.$location_parent->name.')';
                Breadcrumbs::add(Breadcrumb::factory()->set_title($location_parent->name)->set_url(Route::url('list', array('location'=>$location_parent->seoname))));
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($location->name)->set_url(Route::url('list', array('location'=>$location->seoname))));
                
            if ($category_parent!==NULL)
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->name)
                    ->set_url(Route::url('list', array('category'=>$category_parent->seoname,'location'=>$location->seoname))));
            
            if ($category!==NULL)
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category->name)
                    ->set_url(Route::url('list', array('category'=>$category->seoname,'location'=>$location->seoname))));
        }
        else
        {
            if ($category_parent!==NULL)
            {
                $this->template->title .=' ('.$category_parent->name.')';
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->name)
                    ->set_url(Route::url('list', array('category'=>$category_parent->seoname))));
            }
            
            if ($category!==NULL)
                Breadcrumbs::add(Breadcrumb::factory()->set_title($category->name)
                    ->set_url(Route::url('list', array('category'=>$category->seoname))));
        }


        $data = $this->list_logic($category, $location);
   		
		$this->template->bind('content', $content);
		$this->template->content = View::factory('pages/ad/listing',$data);
 	}

    /**
     * gets data to the view and filters the ads
     * @param  Model_Category $category 
     * @param  Model_Location $location
     * @return array           
     */
	public function list_logic($category = NULL, $location = NULL)
	{

		//user recognition 
		$user = (Auth::instance()->get_user() == NULL) ? NULL : Auth::instance()->get_user();

		$ads = new Model_Ad();

		//filter by category or location
        if ($category!==NULL)
        {
            $ads->where('id_category', 'in', $category->get_siblings_ids());
        }

        if ($location!==NULL)
        {
            $ads->where('id_location', 'in', $location->get_siblings_ids());
        }

		//only published ads
        $ads->where('status', '=', Model_Ad::STATUS_PUBLISHED);
		

        //if ad have passed expiration time dont show 
        if(core::config('advertisement.expire_date') > 0)
        {
            $ads->where(DB::expr('DATE_ADD( published, INTERVAL '.core::config('advertisement.expire_date').' DAY)'), '>', Date::unix2mysql());
        }
        
    
        // featured ads
        $featured = NULL; 
        if(Theme::get('listing_slider') == 2)
        {
                $featured = clone $ads;
                $featured = $featured->where('featured', '>=', Date::unix2mysql())
                                ->order_by('featured','desc')
                                ->limit(Theme::get('num_home_latest_ads', 4))
                                ->find_all();
        }
        //random featured
        elseif(Theme::get('listing_slider') == 3)
        {
                $featured = clone $ads;
                $featured = $featured->where('featured', '>=', Date::unix2mysql())
                                ->order_by(DB::expr('RAND()'))
                                ->limit(Theme::get('num_home_latest_ads', 4))
                                ->find_all();
        }

        $res_count = clone $ads;
		$res_count = $res_count->count_all();

		// check if there are some advet.-s
		if ($res_count > 0)
		{
   
       		// pagination module
       		$pagination = Pagination::factory(array(
                    'view'           	=> 'pagination',
                    'total_items'    	=> $res_count,
                    'items_per_page' 	=> core::config('advertisement.advertisements_per_page'),
     	    ))->route_params(array(
                    'controller' 		=> $this->request->controller(),
                    'action'      		=> $this->request->action(),
                    'category' 			=> ($category!==NULL)?$category->seoname:NULL,
                    'location'			=> ($location!==NULL)?$location->seoname:NULL, 
    	    ));
    	   
     	    Breadcrumbs::add(Breadcrumb::factory()->set_title(__("Page ").$pagination->current_page));

            /**
             * order depending on the sort parameter
             */
            switch (core::request('sort',core::config('advertisement.sort_by'))) 
            {
                //title z->a
                case 'title-asc':
                    $ads->order_by('title','asc')->order_by('published','desc');
                    break;
                //title a->z
                case 'title-desc':
                    $ads->order_by('title','desc')->order_by('published','desc');
                    break;
                //cheaper first
                case 'price-asc':
                    $ads->order_by('price','asc')->order_by('published','desc');
                    break;
                //expensive first
                case 'price-desc':
                    $ads->order_by('price','desc')->order_by('published','desc');
                    break;
                //featured
                case 'featured':
                    $ads->order_by('featured','desc')->order_by('published','desc');
                    break;
                //rating
                case 'rating':
                    $ads->order_by('rate','desc')->order_by('published','desc');
                    break;
                //oldest first
                case 'published-asc':
                    $ads->order_by('published','asc');
                    break;
                //newest first
                case 'published-desc':
                default:
                    $ads->order_by('published','desc');
                    break;
            }


     	    //we sort all ads with few parameters
       		$ads = $ads ->limit($pagination->items_per_page)
        	            ->offset($pagination->offset)
        	            ->find_all();
		}
		else
		{
			// array of categories sorted for view
			return array('ads'			=> NULL,
						 'pagination'	=> NULL, 
						  'user'        => $user, 
                         'category'     => $category,
                         'location'     => $location,
                         'featured'		=> NULL);
		}
		
		// array of categories sorted for view
		return array('ads'			=> $ads,
					 'pagination'	=> $pagination, 
					 'user'			=> $user, 
					 'category'		=> $category,
					 'location'		=> $location,
					 'featured'		=> $featured);
	}

	/**
	 * 
	 * Display single advert. 
	 * @throws HTTP_Exception_404
	 * 
	 */
	public function action_view()
	{
		$seotitle = $this->request->param('seotitle',NULL);
		
		if ($seotitle!==NULL)
		{
			$ad = new Model_Ad();
			$ad->where('seotitle','=', $seotitle)
                ->where('status','!=',Model_Ad::STATUS_SPAM)
				->limit(1)->cached()->find();

			if ($ad->loaded())
			{
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));

                $location = NULL;
                $location_parent = NULL;
                if ($ad->location->loaded() AND $ad->id_location!=1)
                {
                    $location = $ad->location;
                    //adding the location parent
                    if ($location->id_location_parent!=1 AND $location->parent->loaded())
                        $location_parent = $location->parent;
                }  
                

                $category = NULL;
                $category_parent = NULL;
                if ($ad->category->loaded())
                {
                    $category = $ad->category;
                    //adding the category parent
                    if ($category->id_category_parent!=1 AND $category->parent->loaded())
                        $category_parent = $category->parent;

                }

                //base category  title
                if ($category!==NULL)
                    $this->template->title = $category->name;
                else
                    $this->template->title = '';

                //adding location titles and breadcrumbs
                if ($location!==NULL)
                {
                    $this->template->title .= ' - '.$location->name;

                    if ($location_parent!==NULL)
                    {
                        $this->template->title .=' ('.$location_parent->name.')';
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($location_parent->name)->set_url(Route::url('list', array('location'=>$location_parent->seoname))));
                    }

                    Breadcrumbs::add(Breadcrumb::factory()->set_title($location->name)->set_url(Route::url('list', array('location'=>$location->seoname))));
                        
                    if ($category_parent!==NULL)
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->name)
                            ->set_url(Route::url('list', array('category'=>$category_parent->seoname,'location'=>$location->seoname))));
                    
                    if ($category!==NULL)
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category->name)
                            ->set_url(Route::url('list', array('category'=>$category->seoname,'location'=>$location->seoname))));
                }
                else
                {
                    if ($category_parent!==NULL)
                    {
                        $this->template->title .=' ('.$category_parent->name.')';
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category_parent->name)
                            ->set_url(Route::url('list', array('category'=>$category_parent->seoname))));
                    }
                        
                    
                    if ($category!==NULL)
                        Breadcrumbs::add(Breadcrumb::factory()->set_title($category->name)
                            ->set_url(Route::url('list', array('category'=>$category->seoname))));
                }



                $this->template->title = $ad->title.' - '. $this->template->title;
				
				Breadcrumbs::add(Breadcrumb::factory()->set_title($ad->title));   	

				
                $this->template->meta_description = $ad->title.' '.__('in').' '.$category->name.' '.__('on').' '.core::config('general.site_name');

				$permission = TRUE; //permission to add hit to advert and give access rights. 
				$auth_user = Auth::instance();
                if(!$auth_user->logged_in() OR 
					($auth_user->get_user()->id_user != $ad->id_user AND ($auth_user->get_user()->id_role != Model_Role::ROLE_ADMIN AND $auth_user->get_user()->id_role != Model_Role::ROLE_MODERATOR)) OR 
					($auth_user->get_user()->id_role != Model_Role::ROLE_ADMIN AND $auth_user->get_user()->id_role != Model_Role::ROLE_MODERATOR))
				{	

					$permission = FALSE;
					$user = NULL;
				} 
                else 
                    $user = $auth_user->get_user()->id_user;

                $hits = $ad->count_ad_hit();				

				$captcha_show = core::config('advertisement.captcha');	
				                    

                if($ad->get_first_image() !== NULL)
                    Controller::$image = $ad->get_first_image();


				$this->template->bind('content', $content);
				$this->template->content = View::factory('pages/ad/single',array('ad'				=>$ad,
																				   'permission'		=>$permission, 
																				   'hits'			=>$hits, 
																				   'captcha_show'	=>$captcha_show,
																				   'user'			=>$user,
																				   'cf_list'		=>$ad->custom_columns()
																				   ));

			}
			//not found in DB
			else
			{
				//throw 404
				throw HTTP_Exception::factory(404,__('Page not found'));
			}
			
		}
		else//this will never happen
		{
			//throw 404
			throw HTTP_Exception::factory(404,__('Page not found'));
		}
	}

    /**
     * 
     * Display reviews advert. 
     * @throws HTTP_Exception_404
     * 
     */
    public function action_reviews()
    {
        $seotitle = $this->request->param('seotitle',NULL);
        
        if ($seotitle!==NULL AND Core::config('advertisement.reviews')==1)
        {
            $ad = new Model_Ad();
            $ad->where('seotitle','=', $seotitle)
                ->where('status','!=',Model_Ad::STATUS_SPAM)
                ->limit(1)->cached()->find();

            if ($ad->loaded())
            {
                $errors = NULL;

                //adding a new review
                if($this->request->post() AND Auth::instance()->logged_in() )  
                {
                    $user = Auth::instance()->get_user();

                    //only able to review if bought the product
                    if (Core::config('advertisement.reviews_paid')==1)
                    {
                        $order = new Model_Order();
                        $order->where('id_ad','=',$ad->id_ad)
                                ->where('id_user','=',$user->id_user)
                                ->where('id_product','=',Model_Order::PRODUCT_AD_SELL)
                                ->where('status','=',Model_Order::STATUS_PAID)
                                ->find();

                        if (!$order->loaded())
                        {
                            Alert::set(Alert::ERROR, __('You can only add a review if you bought this product'));
                            $this->redirect(Route::url('ad-review',array('seotitle'=>$ad->seotitle)));
                        }                        
                    }

                    //not allowing to review to yourself
                    if ($user->id_user == $ad->id_user)
                    {
                        Alert::set(Alert::ERROR, __('You can not review yourself.'));
                        $this->redirect(Route::url('ad-review',array('seotitle'=>$ad->seotitle)));
                    }

                    $review = new Model_Review();
                    $review->where('id_ad','=',$ad->id_ad)
                            ->where_open()
                            ->or_where('id_user','=',$user->id_user)
                            ->or_where('ip_address','=',ip2long(Request::$client_ip))
                            ->where_close()
                            ->find();
                            //d($review);
                    if (!$review->loaded())
                    {
                        if (captcha::check('review'))
                        {
                            $validation = Validation::factory($this->request->post())->rule('rate', 'numeric')
                                                        ->rule('description', 'not_empty')->rule('description', 'min_length', array(':value', 5))
                                                        ->rule('description', 'max_length', array(':value', 1000));
                            if ($validation->check())
                            {
                                $rate = core::post('rate');
                                if ($rate>Model_Review::RATE_MAX)
                                    $rate = Model_Review::RATE_MAX;
                                elseif ($rate<0)
                                    $rate = 0;

                                $review = new Model_Review();
                                $review->id_user        = $user->id_user;
                                $review->id_ad          = $ad->id_ad;
                                $review->description    = core::post('description');
                                $review->status         = Model_Review::STATUS_ACTIVE;
                                $review->ip_address     = ip2long(Request::$client_ip);
                                $review->rate           = $rate;
                                $review->save();
                                //email product owner?? notify him of new review
                                $ad->user->email('ad-review',
                                             array('[AD.TITLE]'     =>$ad->title,
                                                    '[RATE]'        =>$review->rate,
                                                    '[DESCRIPTION]' =>$review->description,
                                                    '[URL.QL]'      =>$ad->user->ql('ad-review',array('seotitle'=>$ad->seotitle))));

                                $ad->recalculate_rate();
                                $ad->user->recalculate_rate();
                                Alert::set(Alert::SUCCESS, __('Thanks for your review!'));
                            }
                            else
                                $errors = $validation->errors('ad');
                        }
                        else
                            Alert::set(Alert::ERROR, __('Wrong Captcha'));
                    }
                    else
                        Alert::set(Alert::ERROR, __('You already added a review'));
                }     
                
                $this->template->scripts['footer'][] = 'js/jquery.raty.min.js';
                $this->template->scripts['footer'][] = 'js/review.js';

                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
                Breadcrumbs::add(Breadcrumb::factory()->set_title($ad->title)->set_url(Route::url('ad',array('seotitle'=>$ad->seotitle,'category'=>$ad->category->seoname))));

                $this->template->title = $ad->title.' - '. __('Reviews');
                
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reviews')));     

                
                $this->template->meta_description = text::removebbcode($ad->description);

                $permission = TRUE; //permission to add hit to advert and give access rights. 
                $auth_user = Auth::instance();
                if(!$auth_user->logged_in() OR 
                    ($auth_user->get_user()->id_user != $ad->id_user AND ($auth_user->get_user()->id_role != Model_Role::ROLE_ADMIN AND $auth_user->get_user()->id_role != Model_Role::ROLE_MODERATOR)) OR 
                    ($auth_user->get_user()->id_role != Model_Role::ROLE_ADMIN AND $auth_user->get_user()->id_role != Model_Role::ROLE_MODERATOR))
                {   

                    $permission = FALSE;
                    $user = NULL;
                } 
                else 
                    $user = $auth_user->get_user()->id_user;


                $captcha_show = core::config('advertisement.captcha');  
                                    

                if($ad->get_first_image() !== NULL)
                    Controller::$image = $ad->get_first_image();

                $reviews = new Model_Review();
                $reviews = $reviews->where('id_ad','=',$ad->id_ad)
                                ->where('status', '=', Model_Review::STATUS_ACTIVE)->find_all();

                $this->template->bind('content', $content);
                $this->template->content = View::factory('pages/ad/reviews',array('ad'               =>$ad,
                                                                                   'permission'     =>$permission, 
                                                                                   'captcha_show'   =>$captcha_show,
                                                                                   'user'           =>$user,
                                                                                   'reviews'         =>$reviews,
                                                                                   'errors'         =>$errors
                                                                                   ));

            }
            //not found in DB
            else
            {
                //throw 404
                throw HTTP_Exception::factory(404,__('Page not found'));
            }
            
        }
        else//this will never happen
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }
	
	/**
	 * [action_to_top] [pay to go on top, and make order]
	 *
	 */
	public function action_to_top()
	{
        //check pay to go top is enabled
        if(core::config('payment.to_top') == FALSE)
            throw HTTP_Exception::factory(404,__('Page not found'));
        
        $id_product = Model_Order::PRODUCT_TO_TOP;

        //check ad exists
        $id_ad  = $this->request->param('id');
        $ad     = new Model_Ad($id_ad);
        if ($ad->loaded())
        {
            //case when payment is set to 0, it goes to top without payment, no generating order
            if(core::config('payment.pay_to_go_on_top') <= 0)
            {
                $ad->published = Date::unix2mysql();
                try {
                    $ad->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                $this->redirect(Route::url('list')); 
            }

            $amount     = core::config('payment.pay_to_go_on_top');
            $currency   = core::config('payment.paypal_currency');
           
            $order = Model_Order::new_order($ad, $ad->user, $id_product, $amount, $currency);

            // redirect to payment
            $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'checkout' ,'id' => $order->id_order)));
        }
        else
            throw HTTP_Exception::factory(404,__('Page not found'));
	}

    /**
     * [action_to_featured] [pay to go in featured]
     *
     */
    public function action_to_featured()
    {
        //check pay to featured top is enabled
        if(core::config('payment.to_featured') == FALSE)
            throw HTTP_Exception::factory(404,__('Page not found'));
        
        $id_product = Model_Order::PRODUCT_TO_FEATURED;

        //check ad exists
        $id_ad  = $this->request->param('id');
        $ad     = new Model_Ad($id_ad);
        if ($ad->loaded())
        {
            //case when payment is set to 0,gets featured for free...
            if(core::config('payment.pay_to_go_on_feature') <= 0)
            {
                $ad->featured = Date::unix2mysql(time() + (core::config('payment.featured_days') * 24 * 60 * 60));
                try {
                    $ad->save();
                } catch (Exception $e) {
                    throw HTTP_Exception::factory(500,$e->getMessage());
                }

                $this->redirect(Route::url('list')); 
            }

            $amount     = core::config('payment.pay_to_go_on_feature');
            $currency   = core::config('payment.paypal_currency');

            $order = Model_Order::new_order($ad, $ad->user, $id_product, $amount, $currency);

            // redirect to payment
            $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'checkout' ,'id' => $order->id_order)));
        }
        else
            throw HTTP_Exception::factory(404,__('Page not found'));
    }
	
    /**
     * [action_buy] Pay for ad, and set new order 
     *
     */
    public function action_buy()
    {
        //check pay to featured top is enabled
        if(core::config('payment.paypal_seller') == FALSE)
            throw HTTP_Exception::factory(404,__('Page not found'));

        //getting the user that wants to buy now
        if (!Auth::instance()->logged_in())
        {
            Alert::set(Alert::INFO, __('To buy this product you first need to register'));
            $this->redirect(Route::url('oc-panel'));
        }

        $payer_user = Auth::instance()->get_user();
        
        $id_product = Model_Order::PRODUCT_AD_SELL;

        //check ad exists
        $id_ad  = $this->request->param('id');
        $ad     = new Model_Ad($id_ad);
            
        if($ad->loaded())
        {
            //do not allow selling if it already 0
            if(core::config('payment.stock')==0 OR ($ad->stock > 0 AND core::config('payment.stock')==1))
            {
                $amount     = $ad->price;
                $currency   = core::config('payment.paypal_currency');

                $order = Model_Order::new_order($ad, $payer_user, $id_product, $amount, $currency, __('Purchase').': '.$ad->seotitle);

                $this->redirect(Route::url('default', array('controller' =>'ad','action'=>'checkout' ,'id' => $order->id_order)));
            }
        }
        else
            throw HTTP_Exception::factory(404,__('Page not found'));
        
    }


    /**
     * pay an invoice, renders the paymenthods button, anyone with an ID of an order can pay it, we do not have control
     * @return [type] [description]
     */
    public function action_checkout()
    {
        $order = new Model_Order($this->request->param('id'));

        if ($order->loaded())
        {
            //if paid...no way jose
            if ($order->status != Model_Order::STATUS_CREATED)
            {
                Alert::set(Alert::INFO, __('This order was already paid.'));
                $this->redirect(Route::url('default'));
            }

            //template header
            $this->template->title              = __('Checkout').' '.Model_Order::product_desc($order->id_product);
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title ));

            $this->template->bind('content', $content);

            $this->template->content = View::factory('pages/ad/checkout',array('order' => $order)); 
        }
        else
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }

	public function action_advanced_search()
	{
		//template header
		$this->template->title           	= __('Advanced Search');
		$this->template->meta_description	= __('Search in').' '.core::config('general.site_name');

		//breadcrumbs
		Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title ));

		$pagination = NULL;
        $ads   = NULL;
		$user = (Auth::instance()->get_user() == NULL) ? NULL : Auth::instance()->get_user();

		if($this->request->query()) // after query has detected
		{			
        	// variables 
        	$search_advert 	= core::get('title');
        	$search_loc 	= core::get('location');

        	// filter by each variable
        	$ads = new Model_Ad();

        	// early filter
	        $ads = $ads->where('status', '=', Model_Ad::STATUS_PUBLISHED);

        	//if ad have passed expiration time dont show 
	        if(core::config('advertisement.expire_date') > 0)
	        {
	            $ads->where(DB::expr('DATE_ADD( published, INTERVAL '.core::config('advertisement.expire_date').' DAY)'), '>', Date::unix2mysql());
	        }

	        if(!empty($search_advert) OR (core::get('search')!==NULL AND strlen(core::get('search'))>=3))
	        {	
	        	// if user is using search from header
	        	if(core::get('search'))
	        		$search_advert = core::get('search');

	        	$ads
        			->where_open()
                	->where('title', 'like', '%'.$search_advert.'%')
                	->or_where('description', 'like', '%'.$search_advert.'%')
                	->where_close();
	        }

        	$cf_fields = array();
            foreach ($this->request->query() as $name => $field) 
            {
            	// get by prefix
				if (strpos($name,'cf_') !== false) 
				{
					$cf_fields[$name] = $field;
					//checkbox when selected return string 'on' as a value
					if($field == 'on')
						$cf_fields[$name] = 1;
					elseif(empty($field)){
						$cf_fields[$name] = NULL;
					}
				}
        	}

	        $category = NULL;
            //filter by category 
            if (core::get('category')!==NULL)
            {
                $category = new Model_Category();
                $category->where('seoname','=',core::get('category'))->cached()->limit(1)->find();
                if ($category->loaded())
                    $ads->where('id_category', 'IN', $category->get_siblings_ids());
            }

	        $location = NULL;
            //filter by location 
            if (core::get('location')!==NULL)
            {
                $location = new Model_location();
                $location->where('seoname','=',core::get('location'))->cached()->limit(1)->find();
                if ($location->loaded())
                    $ads->where('id_location', 'IN', $location->get_siblings_ids());
            }

            //filter by price(s)
            if (is_numeric($price_min = str_replace(',','.',core::get('price-min')))) // handle comma (,) used in some countries for prices
                $price_min = (float)$price_min; // round((float)$price_min,2)
            if (is_numeric($price_max = str_replace(',','.',core::get('price-max')))) // handle comma (,) used in some countries for prices
                $price_max = (float)$price_max; // round((float)$price_max,2)

            if ($price_min AND $price_max)
            {
                if ($price_min > $price_max) // swap 2 values
                    $price_min = $price_max + $price_min - ( $price_max = $price_min );
                
                $ads->where('price', 'BETWEEN', array($price_min,$price_max));
            }
            elseif ($price_min) // only min price has been provided
            {
                $ads->where('price', '>=', $price_min);
            }
            elseif ($price_max) // only max price has been provided
            {
                $ads->where('price', '<=', $price_max);
            }

	        foreach ($cf_fields as $key => $value) 
	        {	
	        	if(isset($value) AND $value != NULL)
	        	{
		        	if(is_numeric($value))
		        		$ads->where($key, '=', $value);
		        	elseif(is_string($value))
		        		$ads->where($key, 'like', '%'.$value.'%');
		        }
	        }

	        // count them for pagination
			$res_count = $ads->count_all();

			if($res_count>0)
			{
			
				// pagination module
                $pagination = Pagination::factory(array(
                        'view'              => 'pagination',
                        'total_items'       => $res_count,
                        'items_per_page'    => core::config('advertisement.advertisements_per_page'),
                ))->route_params(array(
                        'controller'        => $this->request->controller(),
                        'action'            => $this->request->action(),
                        'category'          => ($category!==NULL)?$category->seoname:NULL,
                ));

		        Breadcrumbs::add(Breadcrumb::factory()->set_title(__("Page ").$pagination->offset));
				
				$ads = $ads->order_by('published','desc')
							   ->limit($pagination->items_per_page)
			        	       ->offset($pagination->offset)
			        	       ->find_all();
			}
				
		}

		$this->template->bind('content', $content);

		$this->template->content = View::factory('pages/ad/advanced_search', array('ads'		      => $ads, 
        																		   'categories'	      => Model_Category::get_as_array(),
        																		   'order_categories' => Model_Category::get_multidimensional(),
        																		   'locations'	      => Model_Location::get_as_array(), 
        																		   'order_locations'  => Model_Location::get_multidimensional(),
        																		   'pagination'	      => $pagination, 
        																		   'user'		      => $user,
        																		   'fields' 		  => Model_Field::get_all(),
        																		   ));
                
		
	}

	
}// End ad controller

