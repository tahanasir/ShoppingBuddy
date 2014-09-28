<?php defined('SYSPATH') or die('No direct script access.');
/**
 * description...
 *
 * @author		Chema <chema@open-classifieds.com>
 * @package		OC
 * @copyright	(c) 2009-2013 Open Classifieds Team
 * @license		GPL v3
 * *
 */
class Model_Category extends ORM {


	/**
	 * Table name to use
	 *
	 * @access	protected
	 * @var		string	$_table_name default [singular model name]
	 */
	protected $_table_name = 'categories';

	/**
	 * Column to use as primary key
	 *
	 * @access	protected
	 * @var		string	$_primary_key default [id]
	 */
	protected $_primary_key = 'id_category';


	/**
	 * @var  array  ORM Dependency/hirerachy
	 */
	protected $_has_many = array(
		'ads' => array(
			'model'       => 'Ad',
			'foreign_key' => 'id_category',
		),
	);

    protected $_belongs_to = array(
        'parent'   => array('model'       => 'Category',
                            'foreign_key' => 'id_category_parent'),
    );

    /**
     * global Model Category instance get from controller so we can access from anywhere like Model_Category::current()
     * @var Model_Location
     */
    protected static $_current = NULL;

    /**
     * returns the current category
     * @return Model_Category
     */
    public static function current()
    {
        //we don't have so let's retrieve
        if (self::$_current === NULL)
        {
            self::$_current = new self();
            if(Request::current()->param('category') != URL::title(__('all')))
            {
                self::$_current = self::$_current->where('seoname', '=', Request::current()->param('category'))
                                                    ->limit(1)->cached()->find();
            }
        }

        return self::$_current;
    }


	/**
	 * Rule definitions for validation
	 *
	 * @return array
	 */
	public function rules()
	{
		return array('id_category'		=> array(array('numeric')),
			        'name'				=> array(array('not_empty'), array('max_length', array(':value', 145)), ),
			        'order'				=> array(),
			        'id_category_parent'=> array(),
			        'parent_deep'		=> array(),
			        'seoname'			=> array(array('not_empty'), array('max_length', array(':value', 145)), ),
			        'description'		=> array(),
			        'price'				=> array(), );
	}

	/**
	 * Label definitions for validation
	 *
	 * @return array
	 */
	public function labels()
	{
		return array('id_category'			=> __('Id'),
			        'name'					=> __('Name'),
			        'order'					=> __('Order'),
			        'created'				=> __('Created'),
			        'id_category_parent'	=> __('Parent'),
			        'parent_deep'			=> __('Parent deep'),
			        'seoname'				=> __('Seoname'),
			        'description'			=> __('Description'),
			        'price'					=> __('Price'));
	}
	
    /**
     * Filters to run when data is set in this model. The password filter
     * automatically hashes the password when it's set in the model.
     *
     * @return array Filters
     */
    public function filters()
    {
        return array(
                'seoname' => array(
                                array(array($this, 'gen_seoname'))
                              ),
                'id_category_parent' => array(
                                array(array($this, 'check_parent'))
                              ),
        );
    }

    /**
     * we get the categories in an array 
     * @return array 
     */
    public static function get_as_array()
    {
        //transform the cats to an array
        if ( ($cats_arr = Core::cache('cats_arr'))===NULL)
        {
            $cats = new self;
            $cats = $cats->order_by('order','asc')->find_all()->cached()->as_array('id_category');

            $cats_arr = array();
            foreach ($cats as $cat) 
            {
                $cats_arr[$cat->id_category] =  array('name'               => $cat->name,
                                                      'order'              => $cat->order,
                                                      'id_category_parent' => $cat->id_category_parent,
                                                      'parent_deep'        => $cat->parent_deep,
                                                      'seoname'            => $cat->seoname,
                                                      'price'              => $cat->price,
                                                      'id'                 => $cat->id_category,
                                                    );
            }
            Core::cache('cats_arr',$cats_arr);
        }   

        return $cats_arr;
    }

    /**
     * we get the categories in an array using as key the deep they are, perfect fro chained selects
     * @return array 
     */
    public static function get_by_deep()
    {
        // array by parent deep, 
        // each parent deep is one array with categories of the same index
        if ( ($cats_parent_deep = Core::cache('cats_parent_deep'))===NULL)
        {
            $cats = new self;
            $cats = $cats->order_by('order','asc')->find_all()->cached()->as_array('id_category');

            $cats_parent_deep = array();
            foreach ($cats as $cat) 
            {
                $cats_parent_deep[$cat->parent_deep][$cat->id_category] =  array('name'               => $cat->name,
                                                                                  'id_category_parent' => $cat->id_category_parent,
                                                                                  'parent_deep'        => $cat->parent_deep,
                                                                                  'seoname'            => $cat->seoname,
                                                                                  'price'              => $cat->price,
                                                                                  'id'                 => $cat->id_category,
                                                                        );
            }
            //sort by key, in case lover level is befor higher
            ksort($cats_parent_deep);
            Core::cache('cats_parent_deep',$cats_parent_deep);
        }

        return $cats_parent_deep;
    }


    /**
     * we get the categories in an array miltidimensional by deep.
     * @return array 
     */
    public static function get_multidimensional()
    {
        //multidimensional array
        if ( ($cats_m = Core::cache('cats_m'))===NULL)
        {
            $cats = new self;
            $cats = $cats->order_by('order','asc')->find_all()->cached()->as_array('id_category');

            //for each category we get his siblings
            $cats_s = array();
            foreach ($cats as $cat) 
                 $cats_s[$cat->id_category_parent][] = $cat->id_category;
            

            //last build multidimensional array
            if (count($cats_s)>1)
                $cats_m = self::multi_cats($cats_s);
            else
                $cats_m = array();
            Core::cache('cats_m',$cats_m);
        }

        return $cats_m;
    }

    /**
     * gets a multidimensional array wit the categories
     * @param  array  $cats_s      id_category->array(id_siblings)
     * @param  integer $id_category 
     * @param  integer $deep        
     * @return array               
     */
    public static function multi_cats($cats_s,$id_category = 1, $deep = 0)
    {    
        $ret = NULL;
        //we take all the siblings and try to set the grandsons...
        //we check that the id_category sibling has other siblings
        if (isset($cats_s[$id_category]))
        {
            foreach ($cats_s[$id_category] as $id_sibling) 
            {
                //we check that the id_category sibling has other siblings
                if (isset($cats_s[$id_sibling]))
                {
                    if (is_array($cats_s[$id_sibling]))
                    {
                        $ret[$id_sibling] = self::multi_cats($cats_s,$id_sibling,$deep+1);
                    }
                }
                //no siblings we only set the key
                else 
                    $ret[$id_sibling] = NULL;
                
            }
        }
        return $ret;
    }

    /**
     * we get the categories in an array and a multidimensional array to know the deep @todo refactor this, is a mess
     * @deprecated function not in use, just here so we do not break the API to old themes
     * @return array 
     */
    public static function get_all()
    {
        //as array
        $cats_arr = self::get_as_array();

        //multidimensional array
        $cats_m = self::get_multidimensional();

        //array by deep
        $cats_parent_deep = self::get_by_deep();
        
        return array($cats_arr,$cats_m, $cats_parent_deep);
    }

    /**
     * counts how many ads have each category
     * @return array
     */
    public static function get_category_count()
    {
        $expr_date = (is_numeric(core::config('advertisement.expire_date')))?core::config('advertisement.expire_date'):0;
        $db_prefix = core::config('database.default.table_prefix');

        //get the categories that have ads id_category->num ads
        $count_ads = DB::select('c.id_category' , array(DB::expr('COUNT("a.id_ad")'),'count'))
                    ->from(array('categories', 'c'))
                    ->join(array('ads','a'))
                    ->using('id_category')
                        ->where('a.id_category','=',DB::expr($db_prefix.'c.id_category'))
                        ->where(DB::expr('IF('.$expr_date.' <> 0, DATE_ADD( published, INTERVAL '.$expr_date.' DAY), DATE_ADD( NOW(), INTERVAL 1 DAY))'), '>', Date::unix2mysql())
                        ->where('a.status','=',Model_Ad::STATUS_PUBLISHED)
                    ->group_by('c.id_category')
                    ->order_by('c.order','asc')
                    ->cached()
                    ->execute();
        $count_ads = $count_ads->as_array('id_category');

        //get all the categories ORM so we can use the functions, do not use root category
        $categories = new self();
        $categories = $categories->where('id_category','!=',1)->order_by('order','asc')->cached()->find_all();


        //getting the count of ads into the parents
        $parents_count = array();
        foreach ($categories as $category) 
        {
            //this one has ads so lets add it to the parents
            if (isset($count_ads[$category->id_category]))
            {
                //adding himself if doesnt exists
                if (!isset($parents_count[$category->id_category]))
                    $parents_count[$category->id_category] = $count_ads[$category->id_category];

                //for each parent of this category add the count
                foreach ($category->get_parents_ids() as $id ) 
                {
                    if (isset($parents_count[$id]))
                        $parents_count[$id]['count']+= $count_ads[$category->id_category]['count'];
                    else
                        $parents_count[$id]['count'] = $count_ads[$category->id_category]['count'];
                }
            }
        }

        //generating the array
        $cats_count = array();
        foreach ($categories as $category) 
        {
            $cats_count[$category->id_category] = array(   'id_category'   => $category->id_category,
                                                            'seoname'       => $category->seoname,
                                                            'name'          => $category->name,
                                                            'id_category_parent'        => $category->id_category_parent,
                                                            'parent_deep'   => $category->parent_deep,
                                                            'order'         => $category->order,
                                                            'price'         => $category->price,
                                                            'has_siblings'  => isset($parents_count[$category->id_category]),
                                                            'count'         => isset($parents_count[$category->id_category])?$parents_count[$category->id_category]['count']:0,
                                                );
        }

        return $cats_count;
    }


    /**
     * returns all the siblings ids+ the idcategory, used to filter the ads
     * @return array
     */
	public function get_siblings_ids()
    {
        if ($this->loaded())
        {
            //name used in the cache for storage
            $cache_name = 'get_siblings_ids_category_'.$this->id_category;

            if ( ($ids_siblings = Core::cache($cache_name))===NULL)
            {
                //array that contains all the siblings as keys (1,2,3,4,..)
                $ids_siblings = array();

                //we add himself as we use the clause IN on the where
                $ids_siblings[] = $this->id_category;

                $categories = new self();
                $categories = $categories->where('id_category_parent','=',$this->id_category)->cached()->find_all();

                foreach ($categories as $category) 
                {
                    $ids_siblings[] = $category->id_category;

                    //adding his children recursevely if they have any
                    if ( count($siblings_cats = $category->get_siblings_ids())>1 ) 
                        $ids_siblings = array_merge($ids_siblings,$siblings_cats);       
                }

                //removing repeated values
                $ids_siblings = array_unique($ids_siblings);

                //cache the result is expensive!
                Core::cache($cache_name,$ids_siblings);
            }

            return $ids_siblings;
        }

        //not loaded
        return NULL;
    }


    /**
     * returns all the parents ids, used to count ads
     * @return array
     */
    public function get_parents_ids()
    {
        if ($this->loaded())
        {
            //name used in the cache for storage
            $cache_name = 'get_parents_ids_category_'.$this->id_category;

            if ( ($ids_parents = Core::cache($cache_name))===NULL)
            {
                //array that contains all the parents as keys (1,2,3,4,..)
                $ids_parents = array();

                if ($this->id_category_parent!=1)
                {
                    //adding the parent only if loaded
                    if ($this->parent->loaded())
                    {
                        $ids_parents[] = $this->parent->id_category;
                        $ids_parents = array_merge($ids_parents,$this->parent->get_parents_ids()); //recursive 
                    }
                    //removing repeated values
                    $ids_parents = array_unique($ids_parents);  
                }

                //cache the result is expensive!
                Core::cache($cache_name,$ids_parents);
            }

            return $ids_parents;
        }

        //not loaded
        return NULL;
    }

	/**
	 * 
	 * formmanager definitions
	 * 
	 */
	public function form_setup($form)
	{	
		$form->fields['description']['display_as'] = 'textarea';

        $form->fields['id_category_parent']['display_as']   = 'select';
        $form->fields['id_category_parent']['caption']      = 'name';   

        $form->fields['order']['display_as']   = 'select';
        $form->fields['order']['options']      = range(1, 100);
	}


	public function exclude_fields()
	{
		return array('created','parent_deep');
	}

    /**
     * return the title formatted for the URL
     *
     * @param  string $title
     * 
     */
    public function gen_seoname($seoname)
    {

        //in case seoname is really small or null
        if (strlen($seoname)<3)
        {
            if (strlen($this->name)>=3)
                $seoname = $this->name;
            else
                $seoname = __('category').'-'.$seoname;
        }

        $seoname = URL::title($seoname);

        //this are reserved categories names used in the routes.php
        $banned_names = array('blog','faq','forum','oc-panel','rss','oc-error',URL::title(__('all')),URL::title(__('reviews')),'user','stripe','paymill','bitpay','paypal');
        //same name as a route..shit!
        if (in_array($seoname, $banned_names))
            $seoname = URL::title(__('category')).'-'.$seoname; 

        if ($seoname != $this->seoname)
        {
            $cat = new self;
            //find a user same seoname
            $s = $cat->where('seoname', '=', $seoname)->limit(1)->find();

            //found, increment the last digit of the seoname
            if ($s->loaded())
            {
                $cont = 2;
                $loop = TRUE;
                while($loop)
                {
                    $attempt = $seoname.'-'.$cont;
                    $cat = new self;
                    unset($s);
                    $s = $cat->where('seoname', '=', $attempt)->limit(1)->find();
                    if(!$s->loaded())
                    {
                        $loop = FALSE;
                        $seoname = $attempt;
                    }
                    else
                    {
                        $cont++;
                    }
                }
            }
        }
        

        return $seoname;
    }

    /**
     * rule to verify that we selected a parent if not put the root location
     * @param  integer $id_parent 
     * @return integer                     
     */
    public function check_parent($id_parent)
    {
        return (is_numeric($id_parent))? $id_parent:1;
    }

    /**
     * returns the deep of parents of this category
     * @return integer
     */
    public function get_deep()
    {
        //initial deep
        $deep = 0;

        if ($this->loaded())
        {
            //getting all the cats as array
            $cats_arr = Model_Category::get_as_array();

            //getin the parent of this category
            $id_category_parent = $cats_arr[$this->id_category]['id_category_parent'];

            //counting till we find the begining
            while ($id_category_parent != 1 AND $id_category_parent != 0 AND $deep<100) 
            {
                $id_category_parent = $cats_arr[$id_category_parent]['id_category_parent'];
                $deep++;
            }
        }
        
        return $deep;
    }

    /**
     * reurns the url of the category icon
     * @return string url
     */
    public function get_icon()
    {
		if(is_file(DOCROOT."images/categories/".$this->seoname.".png"))
            return URL::base().'images/categories/'.$this->seoname.'.png';
		else
            return FALSE;
    }

} // END Model_Category