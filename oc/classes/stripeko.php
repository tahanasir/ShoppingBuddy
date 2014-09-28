<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Stripe helper class
 *
 * @package    OC
 * @category   Payment
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2014 Open Classifieds Team
 * @license    GPL v3
 */

class StripeKO extends OC_StripeKO{
    

    /**
     * generates HTML for apy buton
     * @param  Model_Order $order 
     * @return string                 
     */
    public static function button(Model_Order $order)
    {
        if ( Core::config('payment.stripe_private')!='' AND Core::config('payment.stripe_public')!='' AND Theme::get('premium')==1)
        {
            return View::factory('pages/stripe/button',array('order'=>$order));
        }

        return '';
    }

}