<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Panel_Order extends Auth_Crud {

    /**
    * @var $_index_fields ORM fields shown in index
    */
    protected $_index_fields = array('id_order','id_user', 'paymethod','amount','status');
    
    /**
     * @var $_orm_model ORM model name
     */
    protected $_orm_model = 'order';

    /**
     *
     * list of possible actions for the crud, you can modify it to allow access or deny, by default all
     * @var array
     */
    public $crud_actions = array('update');

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Orders'))->set_url(Route::url('oc-panel',array('controller'  => 'order'))));
    }

    /**
     *
     * Loads a basic list info
     * @param string $view template to render 
     */
    public function action_index($view = NULL)
    {
        $this->template->title = __('Orders');
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('List')));

        $this->template->scripts['footer'][] = 'js/oc-panel/crud/index.js';

        $orders = new Model_Order();
        //$orders = $orders->where('status', '=', Model_Order::STATUS_PAID);

        if (core::get('email')!==NULL)
        {
            $user = new Model_User();
            $user->where('email','=',core::get('email'))->limit(1)->find();
            if ($user->loaded())
                $orders = $orders->where('id_user', '=', $user->id_user);
        }


        $pagination = Pagination::factory(array(
                    'view'           => 'oc-panel/crud/pagination',
                    'total_items'    => $orders->count_all(),
        ))->route_params(array(
                    'controller' => $this->request->controller(),
                    'action'     => $this->request->action(),
        ));

        $pagination->title($this->template->title);

        $orders = $orders->order_by('created','desc')
        ->limit($pagination->items_per_page)
        ->offset($pagination->offset)
        ->find_all();

        $pagination = $pagination->render();

        
        $this->render('oc-panel/pages/order/index', array('orders' => $orders,'pagination'=>$pagination));
    }    

   

}
