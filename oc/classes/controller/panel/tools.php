<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mixed tools for admin
 *
 * @package    OC
 * @category   Tools
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */
class Controller_Panel_Tools extends Controller_Panel_OC_Tools {


    public function action_import_tool()
    {
        $this->template->title = __('Import tool for locations and categories');
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title));

        if($_POST)
        {
            $bool_csv_parse_error = FALSE;
            $prefix = Database::instance()->table_prefix();

            foreach($_FILES as $file => $path) 
            {

                $csv = $path["tmp_name"];
                if ($csv!=="" AND ($handle = fopen($csv, "r")) !== FALSE) 
                {
                    if($file=='csv_file_categories')
                    {
                        $type = 'categories';
                        $t = 'category';
                        $obj_model_imported = new Model_Category();
                    }
                    elseif($file=='csv_file_locations')
                    {
                        $type = 'locations';
                        $t = 'location';
                        $obj_model_imported = new Model_Location();
                    }

                    $i = -1;
                    $array='';
                    while(($data = fgetcsv($handle, 0, ",")) !== FALSE)
                    {
                        $i++;
                        //avoid first line and blank lines (fgetcsv() returns blank lines found in a CSV file as an array comprising a single NULL field)
                        if ($i==0 OR empty($data[0]))
                            continue; // next line

                        $name = $data[0];
                        $seoname = (isset($data[1])) ? $data[1] : '';

                        if (empty($seoname))
                        {
                            $seoname = $obj_model_imported->gen_seoname($name);

                            $array .= '('.Database::instance()->quote($name).',1,'.Database::instance()->quote($seoname).'),';
                        }
                        $i++;
                    }
                    fclose($handle); 
                    unset($obj_model_imported);

                    if (empty($array))
                    {
                        Alert::set(Alert::INFO, sprintf(__('File %s contains invalid parsing format!'),$path['name']));
                        $bool_csv_parse_error = TRUE;
                        continue; // next $_FILES if any
                    }
                   
                    $array = rtrim($array, ',');
                    
                    $query = DB::query(Database::UPDATE,"INSERT INTO `".$prefix.$type."`
                        (`name` ,`id_".$t."_parent`,`seoname`)
                        VALUES $array ON DUPLICATE KEY UPDATE `id_".$t."_parent`=1;"); 

                    try 
                    {
                       $query->execute();
                    } 
                    catch (Exception $e) 
                    {
                        $bool_csv_parse_error = TRUE;
                    }

                    if($query == FALSE)
                        $bool_csv_parse_error = TRUE;
                    else
                        Alert::set(Alert::SUCCESS, sprintf(__('%u %s have been created'),$i,__(ucfirst($type))));
                }
            }
            if ($bool_csv_parse_error)
            {
                Alert::set(Alert::ERROR, __('Something went wrong, please check format of the file! Remove single quotes or strange characters, in case you have any.'));
                $this->request->redirect(Route::url('oc-panel',array('controller'=>'tools','action'=>'import_tool')));
            }
        } 

        $this->template->content = View::factory('oc-panel/pages/tools/import_tool');
    }

    public function action_migration()
    {
        //@todo improve
        //flow: ask for new connection, if success we store it ina  config as an array.
        //then we display the tables with how many rows --> new view, bottom load the db connection form in case they want to change it
        //in the form ask to do diet in current DB cleanins visits users posts inactive?
        //Migration button
            //on submit 
            // create config group migration to store in which ID was stuck (if happens)
            // save ids migration for maps in configs?
            // do migration using iframe this

        $this->template->title   = __('Open Classifieds migration');
        Breadcrumbs::add(Breadcrumb::factory()->set_title(ucfirst(__('Migration'))));


        //force clean database from migration, not public, just internal helper
        if (Core::get('delete')==1)
        {
            // $this->clean_migration();
            // Alert::set(Alert::SUCCESS,__('Database cleaned'));
        }

        if ($this->request->post())
        {
            $db_config = array (
                'type' => 'mysql',
                'connection' => 
                array (
                    'hostname' => Core::post('hostname'),
                    'database' => Core::post('database'),
                    'username' => Core::post('username'),
                    'password' => Core::post('password'),
                    'persistent' => false,
                ),
                'table_prefix' => Core::post('table_prefix'),
                'charset' => Core::post('charset'),
                'caching' => false,
                'profiling' => false,
            );

            try
            {
                //connect DB
                $db = Database::instance('migrate', $db_config);

                //verify tables in DB
                $pf = Core::post('table_prefix');
                $migration_tables = array($pf.'accounts',$pf.'categories',$pf.'locations',$pf.'posts',$pf.'postshits');
                
                $tables = $db->query(Database::SELECT, 'SHOW TABLES;');
                
            }
            catch (Exception $e)
            {
                Alert::set(Alert::ERROR, __('Review database connection parameters'));
                return;
            }   
           
            //verify tables in DB
            foreach ($tables as $table => $value) 
            {
                $val = array_values($value);
                $t[] = $val[0];                        
            }
            $tables = $t;

            $match_tables = TRUE;
            foreach ($migration_tables as $t) 
            {
                if( ! in_array($t, $tables))
                {
                    $match_tables = FALSE;
                    Alert::set(Alert::ERROR, sprintf(__('Table %s not found'),$t));
                }
                    
            }
            //end tables verification
            
            
            if ($match_tables)
            {
                //start migration
                $start_time = microtime(true);
                $this->migrate($db,$pf);
                Alert::set(Alert::SUCCESS, 'oh yeah! '.round((microtime(true)-$start_time),3).' '.__('seconds'));
            }
            
        }
        else
        {
            $db_config = core::config('database.default');
        }

        $this->template->content = View::factory('oc-panel/pages/tools/migration',array('db_config'=>$db_config));
    }


    private function clean_migration()
    {
        set_time_limit(0);

        DB::delete('ads')->execute();

        DB::delete('categories')->where('id_category','!=','1')->execute();

        DB::delete('locations')->where('id_location','!=','1')->execute();

        DB::delete('users')->where('id_user','!=','1')->execute();

        DB::delete('visits')->execute();

    }


    /**
     * does the DB migration
     * @param  pointer $db 
     * @param  string $pf db_prefix
     */
    private function migrate($db,$pf)
    {
        set_time_limit(0);

        $db_config = core::config('database.default');
        $prefix = $db_config['table_prefix'];
        //connect DB original/to where we migrate
        $dbo = Database::instance('default');

        
        //oc_accounts --> oc_users
        $users_map = array();
        $accounts = $db->query(Database::SELECT, 'SELECT * FROM `'.$pf.'accounts`');

        foreach ($accounts as $account) 
        {

            $user = new Model_User();

            $user->where('email','=',$account['email'])->limit(1)->find();

            if (!$user->loaded())
            {
                $user->name         = $account['name'];
                $user->email        = $account['email'];
                $user->password     = $account['password'];
                $user->created      = $account['createdDate'];
                $user->last_modified= $account['lastModifiedDate'];
                $user->last_login   = $account['lastSigninDate'];
                $user->status       = $account['active'];
                $user->id_role      = 1;
                $user->seoname      = $user->gen_seo_title($user->name);
                $user->save();
            }

            $users_map[$account['email']] = $user->id_user;
        }

        //categories --> categories
        $categories_map = array(0=>1);

        $categories = $db->query(Database::SELECT, 'SELECT * FROM `'.$pf.'categories` ORDER BY `idCategoryParent` ASC');

        foreach ($categories as $category) 
        {
            $cat = new Model_Category();
            $cat->name      = $category['name'];
            $cat->order     = $category['order'];
            $cat->created   = $category['created'];
            $cat->seoname   = $category['friendlyName'];
            $cat->price     = $category['price'];
            $cat->description = substr($category['description'],0,250);
            $cat->parent_deep = ($category['idCategoryParent']>0)? 1:0; //there's only 1 deep
            $cat->id_category_parent = (isset($categories_map[$category['idCategoryParent']]))?$categories_map[$category['idCategoryParent']]:1;
            $cat->save();

            //we save old_id stores the new ID, so later we know the category parent, and to changes the ADS category id
            $categories_map[$category['idCategory']] = $cat->id_category;

        }


        //locations --> locations
        $locations_map = array(0=>1);

        $locations = $db->query(Database::SELECT, 'SELECT * FROM `'.$pf.'locations` ORDER BY `idLocationParent` ASC');

        foreach ($locations as $location) 
        {
            $loc = new Model_Location();
            $loc->name      = $location['name'];
            $loc->seoname   = $location['friendlyName'];
            $loc->parent_deep = ($location['idLocationParent']>0)? 1:0; //there's only 1 deep
            $loc->id_location_parent = (isset($locations_map[$location['idLocationParent']]))?$locations_map[$location['idLocationParent']]:1;
            $loc->save();

            //we save old_id stores the new ID, so later we know the location parent, and to changes the ADS location id
            $locations_map[$location['idLocation']] = $loc->id_location;

        }

        //posts --> ads
        $ads_map = array();
        $ads = $db->query(Database::SELECT, 'SELECT * FROM `'.$pf.'posts`');

        foreach ($ads as $a) 
        {
            if (Valid::email($a['email']))
            {
                //gettin the id_user
                if(isset($users_map[$a['email']]))
                {
                    $id_user = $users_map[$a['email']]; 
                }
                //doesnt exits creating it
                else
                {
                    $user = Model_User::create_email($a['email'], $a['name']);
                    $id_user = $user->id_user;
                }
                    

                $ad = new Model_Ad();
                $ad->id_ad          = $a['idPost']; //so images still work
                $ad->id_user        = $id_user;
                $ad->id_category    = (isset($categories_map[$a['idCategory']]))?$categories_map[$a['idCategory']]:1;
                $ad->id_location    = (isset($locations_map[$a['idLocation']]))?$locations_map[$a['idLocation']]:1;
                $ad->title          = $a['title'];
                $ad->seotitle       = $ad->gen_seo_title($a['title']);
                $ad->description    = (!empty($a['description']))?Text::html2bb($a['description']):$a['title'];
                $ad->address        = $a['place'];
                $ad->price          = $a['price'];
                $ad->phone          = $a['phone'];
                $ad->has_images     = $a['hasImages'];
                $ad->ip_address     = ip2long($a['ip']);
                $ad->created        = $a['insertDate'];
                $ad->published      = $ad->created;

                //Status migration...big mess!
                if ($a['isAvailable']==0 AND $a['isConfirmed'] ==0)
                {
                    $ad->status = Model_Ad::STATUS_NOPUBLISHED;
                }
                elseif ($a['isAvailable']==1 AND   $a['isConfirmed'] ==0)
                {
                    $ad->status = Model_Ad::STATUS_NOPUBLISHED;
                }
                elseif ($a['isAvailable']==1 AND   $a['isConfirmed'] ==1)
                {
                    $ad->status = Model_Ad::STATUS_PUBLISHED;
                }
                elseif ($a['isAvailable']==0 AND   $a['isConfirmed'] ==1)
                {
                    $ad->status = Model_Ad::STATUS_UNAVAILABLE;
                }
                elseif ($a['isAvailable']==2 )
                {
                    $ad->status = Model_Ad::STATUS_SPAM;
                }
                else
                {
                    $ad->status = Model_Ad::STATUS_UNAVAILABLE;
                }

                try
                {
                    $ad->save();
                }
                catch (ORM_Validation_Exception $e)
                {
                    // d($e->errors(''));
                }

                $ads_map[$a['idPost']] = $ad->id_ad;
            }
            
        }

        //posthits --> visits, mass migration
        $insert = 'INSERT INTO `'.$prefix.'visits` ( `id_ad`, `created`, `ip_address`) VALUES';

        $step  = 5000;
        $total = $db->query(Database::SELECT, 'SELECT count(*) cont FROM `'.$pf.'postshits`')->as_array();
        $total = $total[0]['cont'];

        for ($i=0; $i < $total; $i+=$step) 
        { 
            $hits = $db->query(Database::SELECT, 'SELECT * FROM `'.$pf.'postshits` LIMIT '.$i.', '.$step);
            $values = '';
            foreach ($hits as $hit) 
            {
                //build insert query
                $values.= '('.$hit['idPost'].',  \''.$hit['hitTime'].'\', \''.ip2long($hit['ip']).'\'),';
            }

            $dbo->query(Database::INSERT, $insert.substr($values,0,-1));
        }
            //old way of migrating
            // $hits = $db->query(Database::SELECT, 'SELECT * FROM `'.$pf.'postshits` ');

            // foreach ($hits as $hit) 
            // {
            //     //build insert query
                
            //     $visit = new Model_Visit();
            //     $visit->id_ad       = (isset($ads_map[$hit['idPost']]))?$ads_map[$hit['idPost']]:NULL;
            //     $visit->created     = $hit['hitTime'];
            //     $visit->ip_address  = ip2long($hit['ip']);
            //     $visit->save();
            // }
       

    }


}