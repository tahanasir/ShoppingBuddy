<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Location extends Auth_Crud {



	/**
	 * @var $_index_fields ORM fields shown in index
	 */
	protected $_index_fields = array('id_location','name','id_location_parent');

	/**
	 * @var $_orm_model ORM model name
	 */
	protected $_orm_model = 'location';


    /**
     * overwrites the default crud index
     * @param  string $view nothing since we don't use it
     * @return void      
     */
    public function action_index($view = NULL)
    {
        //template header
        $this->template->title  = __('Locations');

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Locations')));
        $this->template->styles  = array('css/sortable.css' => 'screen', 
                                         '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.css' => 'screen');
        $this->template->scripts['footer'][] = 'js/jquery-sortable-min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/locations.js';
        $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.min.js';

        $locs  = Model_Location::get_as_array();
        $order = Model_Location::get_multidimensional();

        $this->template->content = View::factory('oc-panel/pages/locations',array('locs' => $locs,'order'=>$order));
    }

    /**
     * CRUD controller: CREATE
     */
    public function action_create()
    {

        $this->template->title = __('New').' '.__($this->_orm_model);
        
        $form = new FormOrm($this->_orm_model);
            
        if ($this->request->post())
        {
            if ( $success = $form->submit() )
            {
                $form->object->description = Kohana::$_POST_ORIG['formorm']['description'];
                $form->save_object();
                Core::delete_cache();
                Alert::set(Alert::SUCCESS, __('Item created').'. '.__('Please to see the changes delete the cache')
                    .'<br><a class="btn btn-primary btn-mini ajax-load" href="'.Route::url('oc-panel',array('controller'=>'tools','action'=>'cache')).'?force=1">'
                    .__('Delete All').'</a>');
            
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())));
            }
            else 
            {
                Alert::set(Alert::ERROR, __('Check form for errors'));
            }
        }
    
        return $this->render('oc-panel/crud/create', array('form' => $form));
    }
    /**
     * CRUD controller: UPDATE
     */
    public function action_update()
    {
        $this->template->title = __('Update').' '.__($this->_orm_model).' '.$this->request->param('id');
    
        $form = new FormOrm($this->_orm_model,$this->request->param('id'));
		$location = new Model_Location($this->request->param('id'));
        
        if ($this->request->post())
        {
            if ( $success = $form->submit() )
            {
                if ($form->object->id_location == $form->object->id_location_parent)
                {
                    Alert::set(Alert::INFO, __('You can not set as parent the same location'));
                    $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$form->object->id_location)));
                }

                $form->object->description = Kohana::$_POST_ORIG['formorm']['description'];

                $form->save_object();
                $form->object->parent_deep =  $form->object->get_deep();
                $form->object->save();
                Core::delete_cache();
                Alert::set(Alert::SUCCESS, __('Item updated').'. '.__('Please to see the changes delete the cache')
                    .'<br><a class="btn btn-primary btn-mini ajax-load" href="'.Route::url('oc-panel',array('controller'=>'tools','action'=>'cache')).'?force=1">'
                    .__('Delete All').'</a>');
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller())));
            }
            else
            {
                Alert::set(Alert::ERROR, __('Check form for errors'));
            }
        }
    
        return $this->render('oc-panel/crud/update', array('form' => $form, 'location' => $location));
    }

    /**
     * saves the location in a specific order and change the parent
     * @return void 
     */
    public function action_saveorder()
    {
        $this->auto_render = FALSE;
        $this->template = View::factory('js');

        $loc = new Model_Location(core::get('id_location'));

        if ($loc->loaded())
        {
            //saves the current location
            $loc->id_location_parent = core::get('id_location_parent');
            $loc->parent_deep        = core::get('deep');
            

            //saves the locations in the same parent the new orders
            $order = 0;
            foreach (core::get('brothers') as $id_loc) 
            {
                $id_loc = substr($id_loc,3);//removing the li_ to get the integer

                //not the main location so loading and saving
                if ($id_loc!=core::get('id_location'))
                {
                    $c = new Model_Location($id_loc);
                    $c->parent_deep     = core::get('deep');
                    $c->order           = $order;
                    $c->save();
                }
                else
                {
                    //saves the main location
                    $loc->order  = $order;
                    $loc->save();
                }
                $order++;
            }

            //update deep for all the locations
            $this->action_deep();
            Core::delete_cache();
            $this->template->content = __('Saved');
        }
        else
            $this->template->content = __('Error');
    }

    /**
     * CRUD controller: DELETE
     */
    public function action_delete()
    {
        $this->auto_render = FALSE;

        $location = new Model_Location($this->request->param('id'));

        //update the elements related to that ad
        if ($location->loaded())
        {
            //update all the siblings this location has and set the location parent
            $query = DB::update('locations')
                        ->set(array('id_location_parent' => $location->id_location_parent))
                        ->where('id_location_parent','=',$location->id_location)
                        ->execute();

            //update all the ads this location has and set the location parent
            $query = DB::update('ads')
                        ->set(array('id_location' => $location->id_location_parent))
                        ->where('id_location','=',$location->id_location)
                        ->execute();

            try
            {
                $location->delete();

                $this->template->content = 'OK';

                //recalculating the deep of all the categories
                $this->action_deep();
                Core::delete_cache();
                Alert::set(Alert::SUCCESS, __('Location deleted'));
                
            }
            catch (Exception $e)
            {
                 Alert::set(Alert::ERROR, $e->getMessage());
            }
        }
        else
             Alert::set(Alert::SUCCESS, __('Location not deleted'));

        
        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index')));  

    }

    /**
     * Creates multiple locations just with name
     * @return void      
     */
    public function action_multy_locations()
    {
        $this->auto_render = FALSE;

        //update the elements related to that ad
        if ($_POST)
        {
            if(core::post('multy_locations') !== "")
            {
                $multy_cats = explode(',', core::post('multy_locations'));
                $obj_location = new Model_Location();

                $insert = DB::insert('locations', array('name', 'seoname', 'id_location_parent'));
                foreach ($multy_cats as $name)
                {
                    $insert = $insert->values(array($name,$obj_location->gen_seoname($name),1));
                }
                // Insert everything with one query.
                $insert->execute();

                Core::delete_cache();
            }
            else
                Alert::set(Alert::INFO, __('Select some locations first.'));
        }
        
        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index'))); 
    }

    /**
     * recalculating the deep of all the locations
     * @return [type] [description]
     */
    public function action_deep()
    {
        //clean the cache so we get updated results
        Cache::instance()->delete_all();    

        //getting all the cats as array
        $locs_arr  = Model_Location::get_as_array();

        $locs = new Model_Location();
        $locs = $locs->order_by('order','asc')->find_all()->cached()->as_array('id_location');
        foreach ($locs as $loc) 
        {
            $deep = 0;

            //getin the parent of this location
            $id_location_parent = $locs_arr[$loc->id_location]['id_location_parent'];

            //counting till we find the begining
            while ($id_location_parent != 1 AND $id_location_parent != 0 AND $deep<10) 
            {
                $id_location_parent = $locs_arr[$id_location_parent]['id_location_parent'];
                $deep++;
            }

            //saving the location only if different deep
            if ($loc->parent_deep != $deep)
            {
                $loc->parent_deep = $deep;
                $loc->save();
            }
        }
        //Alert::set(Alert::INFO, __('Success'));
        //HTTP::redirect(Route::url('oc-panel',array('controller'  => 'location','action'=>'index'))); 
    }

	public function action_icon()
	{
		//get icon
		$icon = $_FILES['location_icon']; //file post
		
		$location = new Model_Location($this->request->param('id'));
        
        if(core::post('icon_delete'))
        {            
            $root = DOCROOT.'images/locations/'; //root folder
            
            if (!is_dir($root)) 
            {
                return FALSE;
            }
            else
            {	
                //delete icon
                unlink($root.$location->seoname.'.png');
                
                Alert::set(Alert::SUCCESS, __('Icon deleted.'));
                $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
            }
        }// end of icon delete

        if ( 
            ! Upload::valid($icon) OR
            ! Upload::not_empty($icon) OR
            ! Upload::type($icon, explode(',',core::config('image.allowed_formats'))) OR
            ! Upload::size($icon, core::config('image.max_image_size').'M'))
        {
        	if ( Upload::not_empty($icon) && ! Upload::type($icon, explode(',',core::config('image.allowed_formats'))))
            {
                Alert::set(Alert::ALERT, $icon['name'].' '.sprintf(__('Is not valid format, please use one of this formats "%s"'),core::config('image.allowed_formats')));
				$this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
            } 
            if( ! Upload::size($icon, core::config('image.max_image_size').'M'))
            {
                Alert::set(Alert::ALERT, $icon['name'].' '.sprintf(__('Is not of valid size. Size is limited to %s MB per image'),core::config('general.max_image_size')));
				$this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
            }
            Alert::set(Alert::ALERT, $icon['name'].' '.__('Image is not valid. Please try again.'));
            $this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
        }
        else
        {
            if($icon != NULL) // sanity check 
            {   
                // saving/uploading img file to dir.
                $root = DOCROOT.'images/locations/'; //root folder
                $icon_name = $location->seoname.'.png';
                
                // if folder does not exist, try to make it
               	if ( ! file_exists($root) AND ! @mkdir($root, 0775, true)) { // mkdir not successful ?
                        Alert::set(Alert::ERROR, __('Image folder is missing and cannot be created with mkdir. Please correct to be able to upload images.'));
                        return; // exit function
                };

                // save file to root folder, file, name, dir
                Upload::save($icon, $icon_name, $root);
                
                Alert::set(Alert::SUCCESS, $icon['name'].' '.__('Icon is uploaded.'));
				$this->redirect(Route::get($this->_route_name)->uri(array('controller'=> Request::current()->controller(),'action'=>'update','id'=>$location->id_location)));
            }
            
        }
	}   

}
