<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller SETTINGS contains all basic configurations displayed to Admin.
 */


class Controller_Panel_Settings extends Auth_Controller {

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Settings'))->set_url(Route::url('oc-panel',array('controller'  => 'settings'))));

    }

    public function action_index()
    {
        HTTP::redirect(Route::url('oc-panel',array('controller'  => 'settings','action'=>'general')));  
    }

    /**
     * Contains all data releated to new advertisment optional form inputs,
     * captcha, uploading text file  
     * @return [view] Renders view with form inputs
     */
    public function action_form()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Advertisement')));
        $this->template->title = __('Advertisement');
       
        // all form config values
        $advertisement = new Model_Config();
        $config = $advertisement->where('group_name', '=', 'advertisement')->find_all();
        $this->template->styles  = array('//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.css' => 'screen');
        $this->template->scripts['footer'][] = '//cdn.jsdelivr.net/bootstrap.tagsinput/0.3.9/bootstrap-tagsinput.min.js';
        // save only changed values
        if($this->request->post())
        {
            foreach ($config as $c) 
            {
                $config_res = $this->request->post($c->config_key); 

                if(isset($config_res))
                {
                    if($config_res !== $c->config_value)
                    {
                        $c->config_value = $config_res;
                        try {
                            $c->save();
                        } catch (Exception $e) {
                            echo $e;
                        }
                    }
                }
            }
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'form')));
            
        }

        $this->template->content = View::factory('oc-panel/pages/settings/advertisement', array('config'=>$config));
    }


    /**
     * Email configuration 
     * @return [view] Renders view with form inputs
     */
    public function action_email()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Email')));
        $this->template->title = __('Email');

        // all form config values
        $emailconf = new Model_Config();
        $config = $emailconf->where('group_name', '=', 'email')->find_all();

        // save only changed values
        if($this->request->post())
        {
            foreach ($config as $c) 
            {
                $config_res = $this->request->post($c->config_key); 

                if($config_res != $c->config_value)
                {
                    $c->config_value = $config_res;
                    try {
                        $c->save();
                    } catch (Exception $e) {
                        throw HTTP_Exception::factory(500,$e->getMessage());
                    }
                }
            }
            // Cache::instance()->delete_all();
            Alert::set(Alert::SUCCESS, __('Email Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'email')));
        }

        $this->template->content = View::factory('oc-panel/pages/settings/email', array('config'=>$config));
    }

    /**
     * All general configuration related with configuring site.
     * @return [view] Renders view with form inputs
     */
    public function action_general()
    {
        // validation active 
        //$this->template->scripts['footer'][]= '/js/oc-panel/settings.js';
        
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('General')));
        $this->template->title = __('General');

        // all form config values
        $generalconfig = new Model_Config();
        $config = $generalconfig->where('group_name', '=', 'general')->or_where('group_name', '=', 'i18n')->find_all();

        // config general array
        foreach ($config as $c) 
        {
            $forms[$c->config_key] = $forms[$c->config_key] = array('key'=>$c->group_name.'['.$c->config_key.'][]', 'id'=>$c->config_key, 'value'=>$c->config_value);
        }
        
        //not updatable fields
        $do_nothing = array('menu','locale','allow_query_language','charset','translate','ocacu');

        // save only changed values
        if($this->request->post())
        {
            //save general
            foreach ($config as $c) 
            {   
                $config_res = $this->request->post();
				if(!in_array($c->config_key, $do_nothing) AND $config_res[$c->group_name][$c->config_key][0] != $c->config_value)
                {
                    $c->config_value = $config_res[$c->group_name][$c->config_key][0];
                    Model_Config::set_value($c->group_name,$c->config_key,$c->config_value);
                }
                  
            }

            Alert::set(Alert::SUCCESS, __('General Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'general')));
        }

        $this->template->content = View::factory('oc-panel/pages/settings/general', array('forms'=>$forms));
    }

    /**
     * Payment deatails and paypal configuration can be configured here
     * @return [view] Renders view with form inputs
     */
    public function action_payment()
    {
        // validation active 
        //$this->template->scripts['footer'][]= '/js/oc-panel/settings.js';
        
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Payments')));
        $this->template->title = __('Payments');

        // all form config values
        $paymentconf = new Model_Config();
        $config = $paymentconf->where('group_name', '=', 'payment')->find_all();
        
        $paypal_currency = Paypal::get_currency(); // currencies limited by paypal


        // save only changed values
        if($this->request->post())
        {
            foreach ($config as $c) 
            {
                $config_res = $this->request->post($c->config_key); 

                
                if($c->config_key == 'paypal_currency')
                {   
                    $config_res = $paypal_currency[core::post('paypal_currency')];
                }

                if($config_res != $c->config_value)
                {
                    $c->config_value = $config_res;
                    try {
                        $c->save();
                    } catch (Exception $e) {
                        throw HTTP_Exception::factory(500,$e->getMessage());
                    }
                }
            }
            
            Alert::set(Alert::SUCCESS, __('General Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'payment')));
        }

        $pages = array(''=>__('Deactivated'));
        foreach (Model_Content::get_pages() as $key => $value) 
            $pages[$value->seotitle] = $value->title;

        $this->template->content = View::factory('oc-panel/pages/settings/payment', array('config'          => $config,
                                                                                           'pages'          => $pages,
                                                                                          'paypal_currency' => $paypal_currency));
    }

    /**
     * Image configuration 
     * @return [view] Renders view with form inputs
     */
    public function action_image()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Image')));
        $this->template->title = __('Image');

        // all form config values
        $imageconf = new Model_Config();
        $config = $imageconf->where('group_name', '=', 'image')->find_all();

        // save only changed values
        if($this->request->post())
        {
            foreach ($config as $c) 
            {
                $config_res = $this->request->post(); 
                
                if (!array_key_exists('allowed_formats', $config_res[$c->group_name]))
                {
                    Alert::set(Alert::ERROR, __('At least one image format should be allowed.'));
                    $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'image')));
                }

                if($config_res[$c->group_name][$c->config_key][0] != $c->config_value)
                {
                    if($c->config_key == 'allowed_formats')
                    {
                      $allowed_formats = '';
                      foreach ($config_res[$c->group_name][$c->config_key] as $key => $value) 
                      {
                          $allowed_formats .= $value.",";
                      }
                      $config_res[$c->group_name][$c->config_key][0] = $allowed_formats;
                    } 

                    $c->config_value = $config_res[$c->group_name][$c->config_key][0];
					Model_Config::set_value($c->group_name,$c->config_key,$c->config_value);
                }
            }
            Alert::set(Alert::SUCCESS, __('Image Configuration updated'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'settings','action'=>'image')));
        }

        $this->template->content = View::factory('oc-panel/pages/settings/image', array('config'=>$config));
    }

}//end of controller