<?php defined('SYSPATH') or die('No direct script access.');

/**
 * bitpay helper class
 *
 * @package    OC
 * @category   Payment
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2014 Open Classifieds Team
 * @license    GPL v3
 */

class Bitpay extends OC_Bitpay{
    

    /**
     * generates HTML for apy buton
     * @param  Model_Order $order 
     * @return string                 
     */
    public static function button(Model_Order $order)
    {
        if ( Core::config('payment.bitpay_apikey')!='' AND Theme::get('premium')==1)
        {           
            if (Auth::instance()->logged_in() AND $order->loaded())
                return View::factory('pages/bitpay/button_loged',array('order'=>$order));
            elseif ($order->loaded())
                return View::factory('pages/bitpay/button',array('order'=>$order));

        }

        return '';
    }

}