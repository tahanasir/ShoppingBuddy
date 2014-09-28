<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Paymill helper class
 *
 * @package    OC
 * @category   Payment
 * @author     Chema <chema@open-classifieds.com>, Slobodan <slobodan@open-classifieds.com>
 * @copyright  (c) 2009-2014 Open Classifieds Team
 * @license    GPL v3
 */

class Paymill extends OC_Paymill{
    

    /**
     * generates HTML for apy buton
     * @param  Model_Product $order 
     * @return string                 
     */
    public static function button(Model_Order $order)
    {
        if ( Core::config('payment.paymill_private')!='' AND Core::config('payment.paymill_public')!='' AND Theme::get('premium')==1)
        {
            if (Auth::instance()->logged_in() AND $order->loaded())
                return View::factory('pages/paymill/button_loged',array('order'=>$order));
            elseif ($order->loaded())
                return View::factory('pages/paymill/button',array('order'=>$order));
        }

        return '';
    }

}