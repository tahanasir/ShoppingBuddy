<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Feed extends Controller {

	public function action_index()
	{
        $this->auto_render = FALSE;

		$info = array(
						'title' 	=> 'RSS '.htmlspecialchars(Core::config('general.site_name')),
						'pubDate' => date("D, d M Y H:i:s T"),
						'description' => __('Latest published'),
						'generator' 	=> 'Open Classifieds',
		); 
  		
  		$items = array();

  		//last ads, you can modify this value at: advertisement.feed_elements
        $ads = DB::select('a.seotitle')
                ->select(array('c.seoname','category'),'a.title','a.description','a.published')
                ->from(array('ads', 'a'))
                ->join(array('categories', 'c'),'INNER')
                ->on('a.id_category','=','c.id_category')
                ->where('a.status','=',Model_Ad::STATUS_PUBLISHED)
                ->order_by('published','desc')
                ->limit(Core::config('advertisement.feed_elements'));

        //filter by category aor location
        if (Model_Category::current()->loaded())
            $ads->where('a.id_category','=',Model_Category::current()->id_category);
 
        if (Model_Location::current()->loaded())
            $ads->where('a.id_location','=',Model_Location::current()->id_location);

        $ads = $ads->as_object()->cached()->execute();

        foreach($ads as $a)
        {
            $url= Route::url('ad',  array('category'=>$a->category,'seotitle'=>$a->seotitle));

            $items[] = array(
			                	'title' 	    => htmlspecialchars($a->title,ENT_QUOTES),
			                	'link' 	        => $url,
			                	'pubDate'       => Date::mysql2unix($a->published),
			                	'description'   => htmlspecialchars(Text::removebbcode($a->description),ENT_QUOTES),
			              );
        }
  
  		$xml = Feed::create($info, $items);

  		$this->response->headers('Content-type','text/xml');
        $this->response->body($xml);
	
	}


    public function action_blog()
    {
        $this->auto_render = FALSE;

        $info = array(
                        'title'     => 'RSS Blog '.Core::config('general.site_name'),
                        'pubDate' => date("D, d M Y H:i:s T"),
                        'description' => __('Latest post published'),
                        'generator'     => 'Open Classifieds',
        ); 
        
        $items = array();

        $posts = new Model_Post();
        $posts = $posts->where('status','=', 1)
                ->order_by('created','desc')
                ->limit(Core::config('advertisement.feed_elements'))
                ->cached()
                ->find_all();
           

        foreach($posts as $post)
        {
            $url= Route::url('blog',  array('seotitle'=>$post->seotitle));

            $items[] = array(
                                'title'         => preg_replace('/&(?!\w+;)/', '&amp;', $post->title),
                                'link'          => $url,
                                'pubDate'       => Date::mysql2unix($post->created),
                                'description'   => Text::removebbcode(preg_replace('/&(?!\w+;)/', '&amp;',$post->description)),
                          );
        }
  
        $xml = Feed::create($info, $items);

        $this->response->headers('Content-type','text/xml');
        $this->response->body($xml);
    
    }

    public function action_forum()
    {
        $this->auto_render = FALSE;

        $info = array(
                        'title'     => 'RSS Forum '.Core::config('general.site_name'),
                        'pubDate' => date("D, d M Y H:i:s T"),
                        'description' => __('Latest post published'),
                        'generator'     => 'Open Classifieds',
        ); 
        
        $items = array();

        $topics = new Model_Topic();

        if(Model_Forum::current()->loaded())
            $topics->where('id_forum','=',Model_Forum::current()->id_forum);
        else
            $topics->where('id_forum','!=',NULL);//any forum
        
        $topics = $topics->where('status','=', Model_Topic::STATUS_ACTIVE)
                ->where('id_post_parent','IS',NULL)
                ->order_by('created','desc')
                ->limit(Core::config('advertisement.feed_elements'))
                ->cached()
                ->find_all();
           
        foreach($topics as $topic)
        {
            $url= Route::url('forum-topic',  array('seotitle'=>$topic->seotitle,'forum'=>$topic->forum->seoname));

            $items[] = array(
                                'title'         => preg_replace('/&(?!\w+;)/', '&amp;', $topic->title),
                                'link'          => $url,
                                'pubDate'       => Date::mysql2unix($topic->created),
                                'description'   => Text::removebbcode(preg_replace('/&(?!\w+;)/', '&amp;',$topic->description)),
                          );
        }
  
        $xml = Feed::create($info, $items);

        $this->response->headers('Content-type','text/xml');
        $this->response->body($xml);
    
    }

    public function action_info()
    {

        //try to get the info from the cache
        $info = Core::cache('action_info',NULL);

        //not cached :(
        if ($info === NULL)
        {
            $ads = new Model_Ad();
            $total_ads = $ads->count_all();

            $last_ad = $ads->select('published')->order_by('published','desc')->limit(1)->find();
            $last_ad = $last_ad->published;

            $ads = new Model_Ad();
            $first_ad = $ads->select('published')->order_by('published','asc')->limit(1)->find();
            $first_ad = $first_ad->published;

            $views = new Model_Visit();
            $total_views = $views->count_all();

            $users = new Model_User();
            $total_users = $users->count_all();

            $info = array(
                            'site_url'      => Core::config('general.base_url'),
                            'site_name'     => Core::config('general.site_name'),
                            'site_description' => Core::config('general.site_description'),
                            'created'       => $first_ad,   
                            'updated'       => $last_ad,   
                            'email'         => Core::config('email.notify_email'),
                            'version'       => Core::VERSION,
                            'theme'         => Core::config('appearance.theme'),
                            'theme_mobile'  => Core::config('appearance.theme_mobile'),
                            'charset'       => Kohana::$charset,
                            'timezone'      => Core::config('i18n.timezone'),
                            'locale'        => Core::config('i18n.locale'),
                            'currency'      => '',
                            'ads'           => $total_ads,
                            'views'         => $total_views,
                            'users'         => $total_users,
            );

            Core::cache('action_info',$info);
        }
       

        $this->response->headers('Content-type','application/javascript');
        $this->response->body(json_encode($info));

    }


    /**
     * after does nothing since we send an XML
     */
    public function after(){}


} // End feed
