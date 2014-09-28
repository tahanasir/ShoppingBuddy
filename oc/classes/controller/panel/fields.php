<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Fields extends Auth_Controller {

    
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Custom Fields'))->set_url(Route::url('oc-panel',array('controller'  => 'fields'))));

    }

	public function action_index()
	{
     
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Custom Fields for Advertisements')));
		$this->template->title = __('Custom Fields');

        $this->template->styles              = array('css/sortable.css' => 'screen');
        $this->template->scripts['footer'][] = 'js/jquery-sortable-min.js';
        $this->template->scripts['footer'][] = 'js/oc-panel/fields.js';
        //retrieve fields

		$this->template->content = View::factory('oc-panel/pages/fields/index',array('fields'=>Model_Field::get_all()));
	}
    

    public function action_new()
    {
     
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New field')));
        $this->template->title = __('New Custom Field for Advertisement');

        //find all, for populating form select fields 
        $categories         = Model_Category::get_as_array();  
        $order_categories   = Model_Category::get_multidimensional();

        if ($_POST)
        {
            $name   = URL::title(Core::post('name'));

            $field = new Model_Field();

            try {

                $options = array(
                                'label'             => Core::post('label'),
                                'tooltip'           => Core::post('tooltip'),
                                'required'          => (Core::post('required')=='on')?TRUE:FALSE,
                                'searchable'        => (Core::post('searchable')=='on')?TRUE:FALSE,
                                'admin_privilege'   => (Core::post('admin_privilege')=='on')?TRUE:FALSE,
                                'show_listing'      => (Core::post('show_listing')=='on')?TRUE:FALSE,
                                );

                if ($field->create($name,Core::post('type'),Core::post('values'),Core::post('categories'),$options))
                {
                    Core::delete_cache();
                    Alert::set(Alert::SUCCESS,sprintf(__('Field %s created'),$name));
                }
                else
                    Alert::set(Alert::ERROR,sprintf(__('Field %s already exists'),$name));
 

            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());     
            }

            HTTP::redirect(Route::url('oc-panel',array('controller'  => 'fields','action'=>'index')));  
        }

        $this->template->content = View::factory('oc-panel/pages/fields/new',array('categories' => $categories,
                                                                                   'order_categories' => $order_categories,
        																			));
    }

    public function action_update()
    {
        $name   = $this->request->param('id');
        $field  = new Model_Field();
        $field_data  = $field->get($name);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit').' '.$name));
        $this->template->title = __('Edit Custom Field for Advertisement');

        //find all, for populating form select fields 
        $categories  = Model_Category::get_as_array();

        if ($_POST)
        {

            try {

                $options = array(
                                'label'             => Core::post('label'),
                                'tooltip'           => Core::post('tooltip'),
                                'required'          => (Core::post('required')=='on')?TRUE:FALSE,
                                'searchable'        => (Core::post('searchable')=='on')?TRUE:FALSE,
                                'admin_privilege'   => (Core::post('admin_privilege')=='on')?TRUE:FALSE,
                                'show_listing'      => (Core::post('show_listing')=='on')?TRUE:FALSE,
                                );

                if ($field->update($name,Core::post('values'),Core::post('categories'),$options))
                {
                    Core::delete_cache();
                    Alert::set(Alert::SUCCESS,sprintf(__('Field %s edited'),$name));
                }
                else
                    Alert::set(Alert::ERROR,sprintf(__('Field %s cannot be edited'),$name));

            } catch (Exception $e) {
                throw HTTP_Exception::factory(500,$e->getMessage());     
            }

            HTTP::redirect(Route::url('oc-panel',array('controller'  => 'fields','action'=>'index')));  
        }

        $this->template->content = View::factory('oc-panel/pages/fields/update',array('field_data'=>$field_data,'name'=>$name,'categories'=>$categories));
    }


    public function action_delete()
    {
        //get name of the param, get the name of the custom fields, deletes from config array and alters table
        $this->auto_render = FALSE;
        $name   = $this->request->param('id');
        $field  = new Model_Field();

        try {
            $this->template->content = ($field->delete($name))?sprintf(__('Field %s deleted'),$name):sprintf(__('Field %s does not exists'),$name);
        } catch (Exception $e) {
            //throw 500
            throw HTTP_Exception::factory(500,$e->getMessage());     
        }
        
    }

    /**
     * used for the ajax request to reorder the fields
     * @return string 
     */
    public function action_saveorder()
    {
        $field  = new Model_Field();

        $this->auto_render = FALSE;
        $this->template = View::factory('js');


        $order = Core::get('order');

        array_walk($order, function(&$item, $key){
                $item = str_replace('li_', '', $item);
        });

        if ($field->change_order($order))

            $this->template->content = __('Saved');
        else
            $this->template->content = __('Error');
    }
	

}
