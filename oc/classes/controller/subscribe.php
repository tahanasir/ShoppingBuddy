<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Subscribe extends Controller {

	public function action_index()
	{
		$email = $this->request->post('email_subscribe');

		if (Valid::email($email,TRUE))
		{
			/* find user and compare emails */
			
			$obj_user = new Model_User();

			$user = $obj_user->where('email', '=', $email)->limit(1)->find();

			// case when user is not logged in. 
    		// We create new user if he doesn't exists in DB
    		// and send him mail for ad created + new profile created
			if(!$user->loaded())
			{
				$name = substr($email, '0', stripos($email, '@'));
				$user = Model_User::create_email($email, $name);
			}
			/* save this user to data base as subscriber */
			
			$arr_cat = $this->request->post('category_subscribe');
			
			// string in this case is returned as "int,int" so we need to format min/max price
			$price = $this->request->post('price_subscribe');
			
			if($price = $this->request->post('price_subscribe'))
			{
				$min_price = substr($price, '0', stripos($price, ',')); 
				$max_price = substr($price, strrpos($price, ',')+1);
			}
			else
			{
				//in case of mobile version
				// jquery mobile have different slider, so we need to get data differently
				$min_price = $this->request->post('price_subscribe-1');
				$max_price = $this->request->post('price_subscribe-2');
			}
			
			
			//if categry is not selected, subscribe them for all 
			$obj_category = new Model_Category(); 
			if($arr_cat === NULL)
			{
				$all_cats = $obj_category->get_all();
				$arr_cat = array();
				foreach ($all_cats as $ac) 
				{
					foreach ($ac as $key => $v) 
						$arr_cat[] = $key;
				}
			}	 

			// create entry table subscriber for each category selected
			foreach ($arr_cat as $c => $id_value) 
			{
				$obj_subscribe = new Model_Subscribe();

				$obj_subscribe->id_user = $user->id_user;
				$obj_subscribe->id_category = $id_value;
				$obj_subscribe->id_location = $this->request->post('location_subscribe');
				$obj_subscribe->min_price = $min_price;
				$obj_subscribe->max_price = $max_price;

				try {
					$obj_subscribe->save();
				} catch (Exception $e) {
					throw HTTP_Exception::factory(500,$e->getMessage());
				}
				
			}
			Alert::set(Alert::SUCCESS, __('Thank you for subscribing'));
			$this->redirect(Route::url('default'));
		}
		else
		{
			Alert::set(Alert::ALERT, __('Invalid Email'));
			$this->redirect(Route::url('default'));
		}
	} 

	public function action_unsubscribe()
	{
        if (Auth::instance()->logged_in()) 
        {
            $user = Auth::instance()->get_user();

            DB::delete('subscribers')->where('id_user', '=', $user->id_user)->execute();

    		Alert::set(Alert::SUCCESS, __('You are unsubscribed'));
        }

		$this->redirect(Route::url('default'));
		
	}

	public function action_subscribe()
	{
		$this->template->content = View::factory('pages/ad/subscribe');
	}
}
