<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Product reviews
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Core
 * @copyright   (c) 2009-2014 Open Classifieds Team
 * @license     GPL v3
 */

class Model_Review extends ORM {

    /**
     * status constants
     */
    const STATUS_NOACTIVE = 0; 
    const STATUS_ACTIVE   = 1; 

    const RATE_MAX   = 5; 
    
    /**
     * @var  string  Table name
     */
    protected $_table_name = 'reviews';

    /**
     * @var  string  PrimaryKey field name
     */
    protected $_primary_key = 'id_review';

    /**
     * @var  array  ORM Dependency/hirerachy
     */
    protected $_belongs_to = array(
        'ad' => array(
                'model'       => 'ad',
                'foreign_key' => 'id_ad',
            ),
        'user' => array(
                'model'       => 'user',
                'foreign_key' => 'id_user',
            ),
    );



    public function exclude_fields()
    {
        return array('created');
    }

    /**
     * 
     * formmanager definitions
     * 
     */
    public function form_setup($form)
    {   

        $form->fields['id_ad']['display_as']      = 'text'; 
        $form->fields['id_user']['display_as']    = 'text';
        $form->fields['description']['display_as']    = 'textarea';
    }

    /**
     * returns the ad rate from all the reviews
     * @param  Model_Ad $ad [description]
     * @return [type]                 [description]
     */
    public static function get_ad_rate(Model_Ad $ad)
    {
        //visits created last XX days
        $query = DB::select(DB::expr('SUM(rate) rate'))
                        ->select(DB::expr('COUNT(id_ad) total'))
                        ->from('reviews')
                        ->where('id_ad','=',$ad->id_ad)
                        ->where('status','=',Model_Review::STATUS_ACTIVE)
                        ->group_by('id_ad')
                        ->execute();

        $rates = $query->as_array();

        return (isset($rates[0]))?round($rates[0]['rate']/$rates[0]['total'],2):FALSE;

    }

    /**
     * returns the user rate from all the reviews
     * @param  Model_User $user [description]
     * @return [type]                 [description]
     */
    public static function get_user_rate(Model_User $user)
    {
        //visits created last XX days
        $query = DB::select(DB::expr('SUM(rate) rate'))
                        ->select(DB::expr('COUNT(id_ad) total'))
                        ->from('reviews')
                        ->where('id_user','=',$user->id_user)
                        ->where('status','=',Model_Review::STATUS_ACTIVE)
                        ->group_by('id_ad')
                        ->execute();

        $rates = $query->as_array();

        return (isset($rates[0]))?round($rates[0]['rate']/$rates[0]['total'],2):FALSE;

    }

    /**
     * returns best rated ads
     * @return [type]                 [id]
     */
    public static function best_rated()
    {
        $query = DB::select('id_ad',DB::expr('ROUND(SUM(rate)/COUNT(id_ad)) rate'))
                        ->from('reviews')
                        ->where('status','=',Model_Review::STATUS_ACTIVE)
                        ->group_by(DB::expr('id_ad'))
                        ->order_by('rate','desc')
                        ->cached()
                        ->execute();

        return $rates = $query->as_array();
    }




}