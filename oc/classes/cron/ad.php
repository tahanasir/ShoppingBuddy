<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Cron for advertisements
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Cron
 * @copyright   (c) 2009-2014 Open Classifieds Team
 * @license     GPL v3
 * 
 */
class Cron_Ad {

    /**
     * expired featured ads
     * @return void
     */
    public static function expired_featured()
    {
        //find expired ads of yesterday
        $ads = new Model_Ad();
        $ads = $ads ->where('status','=',Model_Ad::STATUS_PUBLISHED)
                    ->where(DB::expr('DATE(featured)'),'=', Date::format('-1 days','Y-m-d'))
                    ->find_all();

        foreach ($ads as $ad) 
        {
            $edit_url = $ad->user->ql('oc-panel',array('controller'=>'profile','action'=>'update','id'=>$ad->id_ad));

            $ad->user->email('ad-expired', array('[AD.NAME]'      =>$ad->title,
                                                 '[URL.EDITAD]'   =>$edit_url));
        }
    }


    /**
     * expired featured ads
     * @return void
     */
    public static function expired()
    {
        //feature expire ads from yesterday
        if(core::config('advertisement.expire_date') > 0)
        {
            $ads = new Model_Ad();
            $ads = $ads ->where('status','=',Model_Ad::STATUS_PUBLISHED)
                        ->where(DB::expr('DATE(DATE_ADD( published, INTERVAL '.core::config('advertisement.expire_date').' DAY))'),'=', Date::format('-1 days','Y-m-d'))
                        ->find_all();

            foreach ($ads as $ad) 
            {
                $edit_url = $ad->user->ql('oc-panel',array('controller'=>'profile','action'=>'update','id'=>$ad->id_ad));

                $ad->user->email('ad-expired', array('[AD.NAME]'      =>$ad->title,
                                                     '[URL.EDITAD]'   =>$edit_url));
            }

        }

    }


    /**
     * unpaid orders for ads 2 days ago reminder
     * @param integer $days, how many days after created
     * @return void
     */
    public static function unpaid($days = 2)
    {
        //getting orders not paid from 2 days ago
        $orders = new Model_Order();
        $orders = $orders->where('status','=',Model_Order::STATUS_CREATED)
                            ->where(DB::expr('DATE( created)'),'=', Date::format('-'.$days.' days','Y-m-d'))
                            ->where('id_ad','IS NOT',NULL)
                            ->find_all();

        foreach ($orders as $order) 
        {
            $url_checkout = $order->user->ql('default', array('controller'=>'ad','action'=>'checkout','id'=>$order->id_order));

            $order->user->email('new-order', array(  '[ORDER.ID]'    => $order->id_order,
                                                     '[ORDER.DESC]'  => $order->description,
                                                     '[URL.CHECKOUT]'=> $url_checkout));
        }
    }



}