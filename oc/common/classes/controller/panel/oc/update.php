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
class Controller_Panel_OC_Update extends Auth_Controller {    

    static $db_prefix     = NULL;
    static $db_charset    = NULL;

    //list of files to ignore the copy, TODO ignore languages folder?
    static $update_ignore_list = array('robots.txt',
                                        'oc/config/auth.php',
                                        'oc/config/database.php',
                                        '.htaccess',
                                        'sitemap.xml.gz',
                                        'sitemap.xml',
                                        'install/',
                                        );


    public function __construct($request, $response)
    {
        ignore_user_abort(TRUE);
        parent::__construct($request, $response);

        self::$db_prefix  = Core::config('database.default.table_prefix');
        self::$db_charset = Core::config('database.default.charset');
    }

    public function action_index()
    {
        
        //force update check reload
        if (Core::get('reload')==1 )
            Core::get_updates(TRUE);
        
        $versions = core::config('versions');

        if (Core::get('json')==1)
        {
            $this->auto_render = FALSE;
            $this->template = View::factory('js');
            $this->template->content = json_encode($versions);  
        }
        else
        {
            $this->template->title = __('Updates');
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title));

            //version numbers in a key value
            $version_nums = array();
            foreach ($versions as $version=>$values)
                $version_nums[] = $version;

            $latest_version = current($version_nums);
            $latest_version_update = next($version_nums);


            //check if we have latest version of OC and using the previous version then we allow to auto update
            //if ($latest_version!=core::VERSION AND core::VERSION == $latest_version_update )
            if ($latest_version!=core::VERSION)
                Alert::set(Alert::ALERT,__('You are not using latest version, please update.').
                    '<br/><br/><a class="btn btn-primary update_btn" href="'.Route::url('oc-panel',array('controller'=>'update','action'=>'latest')).'">
                '.__('Update').'</a>');
            //elseif ($latest_version!=core::VERSION AND core::VERSION != $latest_version_update )
                //Alert::set(Alert::ALERT,__('You are using an old version, can not update automatically, please update manually.'));

            //pass to view from local versions.php         
            $this->template->content = View::factory('oc-panel/pages/tools/versions',array('versions'       =>$versions,
                                                                                           'latest_version' =>key($versions)));
        }        

    }

    /**
     * STEP 1
     * Downloads and extracts latest version
     */
    public function action_latest()
    {    
        //save in a session the current version so we can selective update the DB later
        Session::instance()->set('update_from_version', Core::VERSION);

        $versions       = core::config('versions'); //loads OC software version array 
        $last_version   = key($versions); //get latest version
        $download_link  = $versions[$last_version]['download']; //get latest download link
        $update_src_dir = DOCROOT.'update'; // update dir 
        $file_name      = $update_src_dir.'/'.$last_version.'.zip'; //full file name
        
        
        //check if exists already the download, if does delete
        if (file_exists($file_name))  
            unlink($file_name); 

        //create update dir if doesnt exists
        if (!is_dir($update_src_dir))  
            mkdir($update_src_dir, 0775); 
          
        //verify we could get the zip file
        $file_content = core::curl_get_contents($download_link);
        if ($file_content == FALSE)
        {
            Alert::set(Alert::ALERT, __('We had a problem downloading latest version, try later please.'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'update', 'action'=>'index')));
        }

        //Write the file
        file_put_contents($file_name, $file_content);

        //unpack zip
        $zip = new ZipArchive;
        // open zip file, and extract to dir
        if ($zip_open = $zip->open($file_name)) 
        {
            $zip->extractTo($update_src_dir);
            $zip->close();  
        }   
        else 
        {
            Alert::set(Alert::ALERT, $file_name.' '.__('Zip file failed to extract, please try again.'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'update', 'action'=>'index')));
        }

        //delete downloaded file
        unlink($file_name);
        
        //move files in different request so more time
        $this->redirect(Route::url('oc-panel', array('controller'=>'update', 'action'=>'files'))); 
      
    }

    /**
     * STEP 2
     * this controller moves the extracted files
     */
    public function action_files()
    {
        $update_src_dir = DOCROOT.'update'; // update dir 
        
        //getting the directory where the zip was uncompressed
        foreach (new DirectoryIterator($update_src_dir) as $file) 
        {
            if($file->isDir() AND !$file->isDot())
            {
                $folder_udpate = $file->getFilename();
                break;
            }
        }

        $from = $update_src_dir.'/'.$folder_udpate;

        //can we access the folder?
        if (is_dir($from))
        {
            //so we just simply delete the ignored files ;)
            foreach (self::$update_ignore_list as $file) 
                File::delete($from.'/'.$file);

            //activate maintenance mode since we are moving files...
            Model_Config::set_value('general','maintenance',1);

            //copy from update to docroot only if files different size
            File::copy($from, DOCROOT, 1);
        }
        else
        {
            Alert::set(Alert::ALERT, $from.' '.sprintf(__('Update folder `%s` not found.'),$from));
            $this->redirect(Route::url('oc-panel',array('controller'=>'update', 'action'=>'index')));
        }
          
        //delete update files when all finished
        File::delete($update_src_dir);

        //clean cache
        Core::delete_cache();

        //deactivate maintenance mode
        Model_Config::set_value('general','maintenance',0);

        //update the DB in different request
        $this->redirect(Route::url('oc-panel', array('controller'=>'update', 'action'=>'database'))); 
    }


    /**
     *  STEP 3
     *  Updates the DB using the functions action_XX
     *  they are actions, just in case you want to launch the update of a specific release like /oc-panel/update/218 for example
     */
    public function action_database()
    {        
        //activate maintenance mode
        Model_Config::set_value('general','maintenance',1);

        //getting the version from where we are upgrading
        $from_version = Session::instance()->get('update_from_version', Core::get('from_version',Core::VERSION));
        $from_version = str_replace('.', '',$from_version);//getting the integer
        //$from_version = substr($from_version,0,3);//we allow only 3 digits updates, if update has more than 3 its a minor release no DB changes?
        $from_version = (int) $from_version;

        //we get all the DB updates available
        $db_updates   = $this->get_db_action_methods();

        foreach ($db_updates as $version) 
        {
            //we only execute those that are newer or same
            if ($version >= $from_version)
            {
                call_user_method('action_'.$version, $this);
                Alert::set(Alert::INFO, __('Updated to ').$version);
            }    

        }

        //deactivate maintenance mode
        Model_Config::set_value('general','maintenance',0);

        Alert::set(Alert::SUCCESS, __('Software DB Updated to latest version!'));

        //clean cache
        Core::delete_cache();

        //TODO maybe a setting that forces the update of the themes?
        $this->redirect(Route::url('oc-panel', array('controller'=>'update', 'action'=>'themes'))); 
    }
    
    /**
     * STEP 4 and last
     * updates all themes to latest version from API license
     * @return void 
     */
    public function action_themes()
    {
        
        $licenses = array();

        //getting the licenses unique. to avoid downloading twice
        $themes = core::config('theme');
        foreach ($themes as $theme) 
        {
            $settings = json_decode($theme,TRUE);
            if (isset($settings['license']))
            {
                if (!in_array($settings['license'], $licenses))
                    $licenses[] = $settings['license'];
            }
        }

        //only if theres work to do ;)
        if (count($licenses)>0)
        {
            //activate maintenance mode
            Model_Config::set_value('general','maintenance',1);

            //for each unique license then download!
            foreach ($licenses as $license) 
                Theme::download($license); 
            
            Alert::set(Alert::SUCCESS, __('Themes Updated'));

            //deactivate maintenance mode
            Model_Config::set_value('general','maintenance',0);

            //clean cache
            Core::delete_cache();
        }
        
        //finished the entire update process
        $this->redirect(Route::url('oc-panel', array('controller'=>'theme', 'action'=>'index'))); 
                    
    }

    /**
     * we get all the DB updates available
     * @return array
     */
    private function get_db_action_methods()
    {
        $updates = array();
        
        $class      = new ReflectionClass($this);
        $methods    = $class->getMethods();
        foreach ($methods as $obj => $val) 
        {
            //only if they are actions and numeric ;)
            if ( is_numeric($version = str_replace('action_', '', $val->name)) )
                $updates[] = $version;
        }
        
        //from less to more, so they are executed in order for sure
        sort($updates);

        return $updates;
    }
}