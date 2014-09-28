<?php defined('SYSPATH') or die('No direct script access.');
/**
 * user favorite ads
 *
 * @author      Chema <chema@open-classifieds.com>
 * @package     Core
 * @copyright   (c) 2009-2014 Open Classifieds Team
 * @license     GPL v3
 */

class Model_Favorite extends ORM {


    /**
     * @var  string  Table name
     */
    protected $_table_name = 'favorites';

    /**
     * @var  string  PrimaryKey field name
     */
    protected $_primary_key = 'id_favorite';

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

    /**
     * is favorite?
     * @param  Model_User $user user
     * @param  Model_Ad   $ad   ad
     * @return boolean          
     */
    public static function is_favorite(Model_User $user, Model_Ad $ad)
    {
        if ($user->loaded() AND $ad->loaded())
        {
            $fav = new Model_Favorite();
            $fav->where('id_user','=',$user->id_user)
                ->where('id_ad', '=', $ad->id_ad)
                ->find();
            if ($fav->loaded())
                return TRUE;
        }

        return FALSE;
    }

}