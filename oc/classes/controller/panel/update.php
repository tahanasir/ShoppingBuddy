<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Update controllers 
 *
 * @package    OC
 * @category   Update
 * @author     Chema <chema@open-classifieds.com>, Slobodan <slobodan@open-classifieds.com>
 * @copyright  (c) 2009-2014 Open Classifieds Team
 * @license    GPL v3
 */
class Controller_Panel_Update extends Controller_Panel_OC_Update {    

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.2.1
     */
    public function action_221()
    {  
        $configs = array(
                        array( 'config_key'     =>'count_visits',
                               'group_name'     =>'advertisement', 
                               'config_value'   => 1),
                        array( 'config_key'     =>'disallowbots',
                               'group_name'     =>'general', 
                               'config_value'   => 0),

                        );

        Model_Config::config_array($configs);
    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.2.0
     */
    public function action_220()
    {   
        //updating contents replacing . for _
        try
        {
            DB::query(Database::UPDATE,"UPDATE ".self::$db_prefix."content SET seotitle=REPLACE(seotitle,'.','-') WHERE type='email'")->execute();
        }catch (exception $e) {}

        //cleaning emails not in use
        try
        {
            DB::query(Database::DELETE,"DELETE FROM ".self::$db_prefix."content WHERE seotitle='user.new' AND type='email'")->execute();
        }catch (exception $e) {}

        //updating contents bad names
        try
        {
            DB::query(Database::UPDATE,"UPDATE ".self::$db_prefix."content SET seotitle='ads-sold' WHERE seotitle='adssold' AND type='email'")->execute();
        }catch (exception $e) {}

        try
        {
            DB::query(Database::UPDATE,"UPDATE ".self::$db_prefix."content SET seotitle='out-of-stock' WHERE seotitle='outofstock' AND type='email'")->execute();
        }catch (exception $e) {}

        try
        {
            DB::query(Database::UPDATE,"UPDATE ".self::$db_prefix."content SET seotitle='ads-purchased' WHERE seotitle='adspurchased' AND type='email'")->execute();
        }catch (exception $e) {}

        try
        {
            DB::query(Database::UPDATE,"UPDATE ".self::$db_prefix."content SET seotitle='ads-purchased' WHERE seotitle='adspurchased' AND type='email'")->execute();
        }catch (exception $e) {}
        //end updating emails
        

        //order transaction
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."orders` ADD  `txn_id` VARCHAR( 255 ) NULL DEFAULT NULL")->execute();
        }catch (exception $e) {}
        

        //ip_address from float to bigint
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."users` CHANGE last_ip last_ip BIGINT NULL DEFAULT NULL ")->execute();
        }catch (exception $e) {}
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."visits` CHANGE ip_address ip_address BIGINT NULL DEFAULT NULL ")->execute();
        }catch (exception $e) {}
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."ads` CHANGE ip_address ip_address BIGINT NULL DEFAULT NULL ")->execute();
        }catch (exception $e) {}
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."posts` CHANGE ip_address ip_address BIGINT NULL DEFAULT NULL ")->execute();
        }catch (exception $e) {}

        //crontab table
        try
        {
            DB::query(Database::UPDATE,"CREATE TABLE IF NOT EXISTS `".self::$db_prefix."crontab` (
                    `id_crontab` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `name` varchar(50) NOT NULL,
                      `period` varchar(50) NOT NULL,
                      `callback` varchar(140) NOT NULL,
                      `params` varchar(255) DEFAULT NULL,
                      `description` varchar(255) DEFAULT NULL,
                      `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `date_started` datetime  DEFAULT NULL,
                      `date_finished` datetime  DEFAULT NULL,
                      `date_next` datetime  DEFAULT NULL,
                      `times_executed`  bigint DEFAULT '0',
                      `output` varchar(50) DEFAULT NULL,
                      `running` tinyint(1) NOT NULL DEFAULT '0',
                      `active` tinyint(1) NOT NULL DEFAULT '1',
                      PRIMARY KEY (`id_crontab`),
                      UNIQUE KEY `".self::$db_prefix."crontab_UK_name` (`name`)
                  ) ENGINE=MyISAM;")->execute();
        }catch (exception $e) {}

        //crontabs
        try
        {
            DB::query(Database::UPDATE,"INSERT INTO `".self::$db_prefix."crontab` (`name`, `period`, `callback`, `params`, `description`, `active`) VALUES
                                    ('Sitemap', '* 3 * * *', 'Sitemap::generate', NULL, 'Regenerates the sitemap everyday at 3am',1),
                                    ('Clean Cache', '* 5 * * *', 'Core::delete_cache', NULL, 'Once day force to flush all the cache.', 1),
                                    ('Optimize DB', '* 4 1 * *', 'Core::optimize_db', NULL, 'once a month we optimize the DB', 1),
                                    ('Unpaid Orders', '* 7 * * *', 'Cron_Ad::unpaid', NULL, 'Notify by email unpaid orders 2 days after was created', 1),
                                    ('Expired Featured Ad', '* 8 * * *', 'Cron_Ad::expired_featured', NULL, 'Notify by email of expired featured ad', 1),
                                    ('Expired Ad', '* 9 * * *', 'Cron_Ad::expired', NULL, 'Notify by email of expired ad', 1);")->execute();
        }catch (exception $e) {}

        //delete old sitemap config
        try
        {
            DB::query(Database::DELETE,"DELETE FROM ".self::$db_prefix."config WHERE (config_key='expires' OR config_key='on_post') AND  group_name='sitemap'")->execute();
        }catch (exception $e) {}

        //categories description to HTML
        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."categories` CHANGE  `description`  `description` TEXT NULL DEFAULT NULL;")->execute();
        }catch (exception $e) {}
        
        $categories = new Model_Category();
        $categories = $categories->find_all();
        foreach ($categories as $category) 
        {
            $category->description = Text::bb2html($category->description,TRUE, FALSE);
            try {
                $category->save();
            } catch (Exception $e) {}
        }

        //locations description to HTML
        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."locations` CHANGE  `description`  `description` TEXT NULL DEFAULT NULL;")->execute();
        }catch (exception $e) {}

        $locations = new Model_Location();
        $locations = $locations->find_all();
        foreach ($locations as $location) 
        {
            $location->description = Text::bb2html($location->description,TRUE, FALSE);
            try {
                $location->save();
            } catch (Exception $e) {}
        }

        //content description to HTML

        $contents = new Model_Content();
        $contents = $contents->find_all();
        foreach ($contents as $content) 
        {
            $content->description = Text::bb2html($content->description,TRUE, FALSE);
            try {
                $content->save();
            } catch (Exception $e) {}
        }

        //blog description to HTML

        $posts =  new Model_Post();
		$posts = $posts->where('id_forum','IS',NULL)->find_all();
        foreach ($posts as $post) 
        {
            $post->description = Text::bb2html($post->description,TRUE, FALSE);
            try {
                $post->save();
            } catch (Exception $e) {}
        }

        //Reviews
        try 
        {
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."users` ADD `rate` FLOAT( 4, 2 ) NULL DEFAULT NULL ;")->execute();
        }catch (exception $e) {}

        try 
        {
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."ads` ADD `rate` FLOAT( 4, 2 ) NULL DEFAULT NULL ;")->execute();
        }catch (exception $e) {}

        try
        {
            DB::query(Database::UPDATE,"CREATE TABLE IF NOT EXISTS ".self::$db_prefix."reviews (
                id_review int(10) unsigned NOT NULL AUTO_INCREMENT,
                id_user int(10) unsigned NOT NULL,
                id_ad int(10) unsigned NOT NULL,
                rate int(2) unsigned NOT NULL DEFAULT '0',
                description varchar(1000) NOT NULL,
                created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ip_address float DEFAULT NULL,
                status tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (id_review) USING BTREE,
                KEY ".self::$db_prefix."reviews_IK_id_user (id_user),
                KEY ".self::$db_prefix."reviews_IK_id_ad (id_ad)
                ) ENGINE=MyISAM;")->execute();
        } catch (Exception $e) {}

        //User description About
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."users`  ADD  `description` TEXT NULL DEFAUlT NULL AFTER  `password` ")->execute();
        }catch (exception $e) {}

        //Favorites table
        try
        {
            DB::query(Database::UPDATE,"CREATE TABLE IF NOT EXISTS ".self::$db_prefix."favorites (
                                        id_favorite int(10) unsigned NOT NULL AUTO_INCREMENT,
                                        id_user int(10) unsigned NOT NULL,
                                        id_ad int(10) unsigned NOT NULL,
                                        created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                        PRIMARY KEY (id_favorite) USING BTREE,
                                        KEY ".self::$db_prefix."favorites_IK_id_user_AND_id_ad (id_user,id_ad)
                                        ) ENGINE=MyISAM;")->execute();
        } catch (Exception $e) {}

        //new mails
        $contents = array(array('order'=>0,
                                'title'=>'Reciept for [ORDER.DESC] #[ORDER.ID]',
                               'seotitle'=>'new-order',
                               'description'=>"Hello [USER.NAME],Thanks for buying [ORDER.DESC].\n\nPlease complete the payment here [URL.CHECKOUT]",
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'),
                            array('order'=>0,
                                'title'=>'Your ad [AD.NAME] has expired',
                               'seotitle'=>'ad-expired',
                               'description'=>"Hello [USER.NAME],Your ad [AD.NAME] has expired \n\nPlease check your ad here [URL.EDITAD]",
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'),
                            array('order'=>'0',
                               'title'=>'New review for [AD.TITLE] [RATE]',
                               'seotitle'=>'ad-review',
                               'description'=>'[URL.QL]\n\n[RATE]\n\n[DESCRIPTION]',
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'),
                        );

        Model_Content::content_array($contents);

        //new configs...
        $configs = array(
                         array('config_key'     =>'bitpay_apikey',
                               'group_name'     =>'payment', 
                               'config_value'   =>''), 
                         array('config_key'     =>'paymill_private',
                               'group_name'     =>'payment', 
                               'config_value'   =>''), 
                         array('config_key'     =>'paymill_public',
                               'group_name'     =>'payment', 
                               'config_value'   =>''), 
                         array('config_key'     =>'stripe_public',
                               'group_name'     =>'payment', 
                               'config_value'   =>''), 
                         array('config_key'     =>'stripe_private',
                               'group_name'     =>'payment', 
                               'config_value'   =>''), 
                         array('config_key'     =>'stripe_address',
                               'group_name'     =>'payment', 
                               'config_value'   =>'0'), 
                         array('config_key'     =>'alternative',
                               'group_name'     =>'payment', 
                               'config_value'   =>''), 
                         array('config_key'     =>'authorize_sandbox',
                               'group_name'     =>'payment', 
                               'config_value'   =>'0'), 
                         array('config_key'     =>'authorize_login',
                               'group_name'     =>'payment', 
                               'config_value'   =>''), 
                         array('config_key'     =>'authorize_key',
                               'group_name'     =>'payment', 
                               'config_value'   =>''),
                         array('config_key'     =>'elastic_active',
                               'group_name'     =>'email', 
                               'config_value'   =>0),
                         array('config_key'     =>'elastic_username',
                               'group_name'     =>'email', 
                               'config_value'   =>''),
                         array('config_key'     =>'elastic_password',
                               'group_name'     =>'email', 
                               'config_value'   =>''),
                         array('config_key'     =>'reviews',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'0'), 
                         array('config_key'     =>'reviews_paid',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'0'), 
                        );

        Model_Config::config_array($configs);

        //delete old files from 323, no need they need to update manually
        // File::delete(APPPATH.'ko323');
        // File::delete(APPPATH.'classes/image/');

        // //delete modules since now they are part of module common
        // File::delete(MODPATH.'pagination');
        // File::delete(MODPATH.'breadcrumbs');
        // File::delete(MODPATH.'formmanager');
        // File::delete(MODPATH.'mysqli');
		
		//assign new group_name to configs
        try
        {
            DB::query(Database::UPDATE,"UPDATE ".self::$db_prefix."config SET group_name='advertisement' WHERE config_key = 'advertisements_per_page' OR config_key = 'feed_elements' OR config_key = 'map_elements' OR config_key = 'sort_by'")->execute();
        }catch (exception $e) {}
            DB::query(Database::UPDATE,"UPDATE ".self::$db_prefix."content SET seotitle=REPLACE(seotitle,'.','-') WHERE type='email'")->execute();
       
    }
    
    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.1.8
     */
    public function action_218()
    {   


        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE ".self::$db_prefix."config DROP INDEX ".self::$db_prefix."config_IK_group_name_AND_config_key")->execute();
        }catch (exception $e) {}
        
        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE ".self::$db_prefix."config ADD PRIMARY KEY (config_key);")->execute();
        }catch (exception $e) {}

        try
        {
            DB::query(Database::UPDATE,"CREATE UNIQUE INDEX ".self::$db_prefix."config_UK_group_name_AND_config_key ON ".self::$db_prefix."config(`group_name` ,`config_key`)")->execute();
        }catch (exception $e) {}

        $configs = array(
                         array('config_key'     =>'login_to_post',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'0'),  
                        );

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);
        
        //delete old files from 322
        File::delete(APPPATH.'ko322');
        File::delete(MODPATH.'auth');
        File::delete(MODPATH.'cache');
        File::delete(MODPATH.'database');
        File::delete(MODPATH.'image');
        File::delete(MODPATH.'orm');
        File::delete(MODPATH.'unittest');

    }
    
    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.1.7
     */
    public function action_217()
    {        

        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."posts` ADD  `id_post_parent` INT NULL DEFAULT NULL AFTER  `id_user`")->execute();
        }catch (exception $e) {}
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."posts` ADD  `ip_address` FLOAT NULL DEFAULT NULL AFTER  `created`")->execute();
        }catch (exception $e) {}
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."posts` ADD  `id_forum` INT NULL DEFAULT NULL AFTER  `id_post_parent`")->execute();
        }catch (exception $e) {}
        try
        {    
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."posts` ENGINE = MYISAM ")->execute();
        }catch (exception $e) {}
        

        DB::query(Database::UPDATE,"CREATE TABLE IF NOT EXISTS  `".self::$db_prefix."forums` (
                      `id_forum` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `name` varchar(145) NOT NULL,
                      `order` int(2) unsigned NOT NULL DEFAULT '0',
                      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      `id_forum_parent` int(10) unsigned NOT NULL DEFAULT '0',
                      `parent_deep` int(2) unsigned NOT NULL DEFAULT '0',
                      `seoname` varchar(145) NOT NULL,
                      `description` varchar(255) NULL,
                      PRIMARY KEY (`id_forum`) USING BTREE,
                      UNIQUE KEY `".self::$db_prefix."forums_IK_seo_name` (`seoname`)
                    ) ENGINE=MyISAM")->execute();

        // build array with new (missing) configs
        
        //set sitemap to 0
        Model_Config::set_value('sitemap','on_post',0);

        $configs = array(
                         array('config_key'     =>'forums',
                               'group_name'     =>'general', 
                               'config_value'   =>'0'), 
                         array('config_key'     =>'ocacu',
                               'group_name'     =>'general', 
                               'config_value'   =>'0'), 
                        );

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);

    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.1.5
     */
    public function action_215()
    {        
        // build array with new (missing) configs
        $configs = array(array('config_key'     =>'qr_code',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'0'),
                         array('config_key'     =>'black_list',
                               'group_name'     =>'general', 
                               'config_value'   =>'1'),
                         array('config_key'     =>'stock',
                               'group_name'     =>'payment', 
                               'config_value'   =>'0'), 
                         array('config_key'     =>'fbcomments',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>''),
                        );
        $contents = array(array('order'=>'0',
                               'title'=>'Advertisement `[AD.TITLE]` is sold on [SITE.NAME]!',
                               'seotitle'=>'ads-sold',
                               'description'=>"Order ID: [ORDER.ID]\n\nProduct ID: [PRODUCT.ID]\n\nPlease check your bank account for the incoming payment.\n\nClick here to visit [URL.AD]", // @FIXME i18n ?
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'),
                          array('order'=>'0',
                               'title'=>'Advertisement `[AD.TITLE]` is purchased on [SITE.NAME]!',
                               'seotitle'=>'ads-purchased',
                               'description'=>"Order ID: [ORDER.ID]\n\nProduct ID: [PRODUCT.ID]\n\nFor any inconvenience please contact administrator of [SITE.NAME], with a details provided above.\n\nClick here to visit [URL.AD]", // @FIXME i18n ?
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'),
                          array('order'=>'0',
                               'title'=>'Advertisement `[AD.TITLE]` is out of stock on [SITE.NAME]!',
                               'seotitle'=>'out-of-stock',
                               'description'=>"Hello [USER.NAME],\n\nWhile your ad is out of stock, it is unavailable for others to see. If you wish to increase stock and activate, please follow this link [URL.EDIT].\n\nRegards!", // @FIXME i18n ?
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'),);

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);
        $return_cont = Model_Content::content_array($contents);


        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."users` ADD `subscriber` tinyint(1) NOT NULL DEFAULT '1'")->execute();
        }catch (exception $e) {}
        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."ads` ADD `stock` int(10) unsigned DEFAULT NULL")->execute();
        }catch (exception $e) {}
        try
        {
            DB::query(Database::UPDATE,"INSERT INTO  `".self::$db_prefix."roles` (`id_role`, `name`, `description`) VALUES (7, 'moderator', 'Limited access')")->execute();
        }catch (exception $e) {}
        try
        {
            DB::query(Database::UPDATE,"INSERT INTO  `".self::$db_prefix."access` (`id_access`, `id_role`, `access`) VALUES 
                                                                         (17, 7, 'location.*'),(16, 7, 'profile.*'),(15, 7, 'content.*'),(14, 7, 'stats.user'),
                                                                         (13, 7, 'blog.*'),(12, 7, 'translations.*'),(11, 7, 'ad.*'),
                                                                         (10, 7, 'widgets.*'),(9, 7, 'menu.*'),(8, 7, 'category.*')")->execute();
        }catch (exception $e) {}

    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.1.3
     */
    public function action_214()
    {        
        // build array with new (missing) configs
        $configs = array(array('config_key'     =>'sort_by',
                               'group_name'     =>'general', 
                               'config_value'   =>'published-desc'),
                         array('config_key'     =>'map_pub_new',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'0'), 
                        );

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);
    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.1
     */
    public function action_211()
    {
      // build array with new (missing) configs
        $configs = array(array('config_key'     =>'related',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'5'),
                        array('config_key'     =>'faq',
                               'group_name'     =>'general', 
                               'config_value'   =>'0'), 
                         array('config_key'     =>'faq_disqus',
                               'group_name'     =>'general', 
                               'config_value'   =>''),
                         );

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs); 
       
    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.0.7
     * changes added: config for advanced search by description
     */
    public function action_210()
    {
        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE  `".self::$db_prefix."users` ADD  `hybridauth_provider_name` VARCHAR( 40 ) NULL DEFAULT NULL ,ADD  `hybridauth_provider_uid` VARCHAR( 245 ) NULL DEFAULT NULL")->execute();
        }catch (exception $e) {}
        try
        {
            DB::query(Database::UPDATE,"CREATE UNIQUE INDEX ".self::$db_prefix."users_UK_provider_AND_uid on ".self::$db_prefix."users (hybridauth_provider_name, hybridauth_provider_uid)")->execute();
        }catch (exception $e) {}
        
        try
        {
            DB::query(Database::UPDATE,"CREATE TABLE IF NOT EXISTS  `".self::$db_prefix."posts` (
                  `id_post` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `id_user` int(10) unsigned NOT NULL,
                  `title` varchar(245) NOT NULL,
                  `seotitle` varchar(245) NOT NULL,
                  `description` text NOT NULL,
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `status` tinyint(1) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id_post`) USING BTREE,
                  UNIQUE KEY `".self::$db_prefix."posts_UK_seotitle` (`seotitle`)
                ) ENGINE=InnoDB DEFAULT CHARSET=".self::$db_charset.";")->execute();
        }catch (exception $e) {}


        // build array with new (missing) configs
        $configs = array(array('config_key'     =>'search_by_description',
                               'group_name'     =>'general', 
                               'config_value'   => 0),
                        array('config_key'     =>'blog',
                               'group_name'     =>'general', 
                               'config_value'   => 0),
                        array('config_key'     =>'minify',
                               'group_name'     =>'general', 
                               'config_value'   => 0),
                        array('config_key'     =>'parent_category',
                               'group_name'     =>'advertisement', 
                               'config_value'   => 1),
                        array('config_key'     =>'blog_disqus',
                               'group_name'     =>'general', 
                               'config_value'   => ''),
                        array('config_key'     =>'config',
                               'group_name'     =>'social', 
                               'config_value'   =>'{"debug_mode":"0","providers":{
                                                          "OpenID":{"enabled":"1"},
                                                          "Yahoo":{"enabled":"0","keys":{"id":"","secret":""}},
                                                          "AOL":{"enabled":"1"}
                                                          ,"Google":{"enabled":"0","keys":{"id":"","secret":""}},
                                                          "Facebook":{"enabled":"0","keys":{"id":"","secret":""}},
                                                          "Twitter":{"enabled":"0","keys":{"key":"","secret":""}},
                                                          "Live":{"enabled":"0","keys":{"id":"","secret":""}},
                                                          "MySpace":{"enabled":"0","keys":{"key":"","secret":""}},
                                                          "LinkedIn":{"enabled":"0","keys":{"key":"","secret":""}},
                                                          "Foursquare":{"enabled":"0","keys":{"id":"","secret":""}}},
                                                      "base_url":"",
                                                      "debug_file":""}'));

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);

        
    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.0.6
     * changes added: config for custom field
     */
    public function action_207()
    {
      // build array with new (missing) configs
        $configs = array(array('config_key'     =>'fields',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>''),
                         array('config_key'     =>'alert_terms',
                               'group_name'     =>'general', 
                               'config_value'   =>''),
                         );

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs); 
    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.0.5 
     * changes added: config for landing page, etc..  
     */
    public function action_206()
    {
      // build array with new (missing) configs
        $configs = array(array('config_key'     =>'landing_page',
                               'group_name'     =>'general', 
                               'config_value'   =>'{"controller":"home","action":"index"}'),
                         array('config_key'     =>'banned_words',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>''),
                         array('config_key'     =>'banned_words_replacement',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>''),
                         array('config_key'     =>'akismet_key',
                               'group_name'     =>'general', 
                               'config_value'   =>''));

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);

        
    }

    /**
     * This function will upgrade DB that didn't existed in versions prior to 2.0.5 
     * changes added: subscription widget, new email content, map zoom, paypal seller etc..  
     */
    public function action_205()
    {
        // build array with new (missing) configs
        $configs = array(array('config_key'     =>'paypal_seller',
                               'group_name'     =>'payment', 
                               'config_value'   =>'0'),
                         array('config_key'     =>'map_zoom',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'16'),
                         array('config_key'     =>'center_lon',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'3'),
                         array('config_key'     =>'center_lat',
                               'group_name'     =>'advertisement', 
                               'config_value'   =>'40'),
                         array('config_key'     =>'new_ad_notify',
                               'group_name'     =>'email', 
                               'config_value'   =>'0'));

        $contents = array(array('order'=>'0',
                               'title'=>'Advertisement `[AD.TITLE]` is created on [SITE.NAME]!',
                               'seotitle'=>'ads_subscribers',
                               'description'=>"Hello,\n\nYou may be interested in this one [AD.TITLE]!\n\nYou can visit this link to see advertisement [URL.AD]",
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'),
                          array('order'=>'0',
                               'title'=>'Advertisement `[AD.TITLE]` is created on [SITE.NAME]!',
                               'seotitle'=>'ads-to-admin',
                               'description'=>"Click here to visit [URL.AD]",
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'));

        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);
        $return_cont = Model_Content::content_array($contents);

        
        
        try
        {
            DB::query(Database::UPDATE,"CREATE TABLE IF NOT EXISTS `".self::$db_prefix."subscribers` (
                    `id_subscribe` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `id_user` int(10) unsigned NOT NULL,
                    `id_category` int(10) unsigned NOT NULL DEFAULT '0',
                    `id_location` int(10) unsigned NOT NULL DEFAULT '0',
                    `min_price` decimal(14,3) NOT NULL DEFAULT '0',
                    `max_price` decimal(14,3) NOT NULL DEFAULT '0',
                    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id_subscribe`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=".self::$db_charset.";")->execute();
        }catch (exception $e) {}
        
        // remove INDEX from content table
        try
        {
            DB::query(Database::UPDATE,"ALTER TABLE `".self::$db_prefix."content` DROP INDEX `".self::$db_prefix."content_UK_seotitle`")->execute();
        }catch (exception $e) {}
    }


    /**
     * This function will upgrade configs that didn't existed in versions prior to 2.0.3 
     */
    public function action_203()
    {
        // build array with new (missing) configs
        $configs = array(array('config_key'     =>'watermark',
                               'group_name'     =>'image', 
                               'config_value'   =>'0'), 
                         array('config_key'     =>'watermark_path',
                               'group_name'     =>'image', 
                               'config_value'   =>''), 
                         array('config_key'     =>'watermark_position',
                               'group_name'     =>'image', 
                               'config_value'   =>'0'),
                         array('config_key'     =>'ads_in_home',
                               'group_name'     =>'advertisement',
                               'config_value'   =>'0'));
        
        $contents = array(array('order'=>'0',
                               'title'=>'Hello [USER.NAME]!',
                               'seotitle'=>'user-profile-contact',
                               'description'=>"User [EMAIL.SENDER] [EMAIL.FROM], have a message for you: \n\n [EMAIL.SUBJECT] \n\n[EMAIL.BODY]. \n\n Regards!",
                               'from_email'=>core::config('email.notify_email'),
                               'type'=>'email',
                               'status'=>'1'));
        
        // returns TRUE if some config is saved 
        $return_conf = Model_Config::config_array($configs);
        $return_cont = Model_Content::content_array($contents);

    }


}
