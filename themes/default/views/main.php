<?php defined('SYSPATH') or die('No direct script access.');?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?=i18n::html_lang()?>"> <!--<![endif]-->
<head>
  <script src="https://cdn.firebase.com/js/client/1.0.15/firebase.js"></script>
  <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'></script>
  <link rel="stylesheet" type="text/css" href="../css/example.css">
      <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">


	<meta charset="<?=Kohana::$charset?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?=$title?></title>
    <meta name="keywords" content="<?=$meta_keywords?>" >
    <meta name="description" content="<?=HTML::chars($meta_description)?>" >
    <meta name="copyright" content="<?=HTML::chars($meta_copyright)?>" >
	<meta name="author" content="open-classifieds.com">

    <?if (Controller::$image!==NULL):?>
    <meta property="og:image"   content="<?=core::config('general.base_url').Controller::$image?>"/>
    <?endif?>
    <meta property="og:title"   content="<?=HTML::chars($title)?>"/>
    <meta property="og:description"   content="<?=HTML::chars($meta_description)?>"/>
    <meta property="og:url"     content="<?=URL::current()?>"/>
    <meta property="og:site_name" content="<?=HTML::chars(core::config('general.site_name'))?>"/>
    
    <?if (core::config('general.disallowbots')=='1'):?>
        <meta name="robots" content="noindex,nofollow,noodp,noydir" />
        <meta name="googlebot" content="noindex,noarchive,nofollow,noodp" />
        <meta name="slurp" content="noindex,nofollow,noodp" />
        <meta name="bingbot" content="noindex,nofollow,noodp,noydir" />
        <meta name="msnbot" content="noindex,nofollow,noodp,noydir" />
    <?endif?>

    <?if (core::config('general.blog')==1):?>
    <link rel="alternate" type="application/atom+xml" title="RSS Blog <?=HTML::chars(Core::config('general.site_name'))?>" href="<?=Route::url('rss-blog')?>" />
    <?endif?>
    <?if (core::config('general.forums')==1):?>
    <link rel="alternate" type="application/atom+xml" title="RSS Forum <?=HTML::chars(Core::config('general.site_name'))?>" href="<?=Route::url('rss-forum')?>" />
      <?if (Model_Forum::current()->loaded()):?>
      <link rel="alternate" type="application/atom+xml" title="RSS Forum <?=HTML::chars(Core::config('general.site_name'))?> - <?=Model_Forum::current()->name?>" href="<?=Route::url('rss-forum', array('forum'=>Model_Forum::current()->seoname))?>" />
      <?endif?>
    <?endif?>
    <link rel="alternate" type="application/atom+xml" title="RSS <?=HTML::chars(Core::config('general.site_name'))?>" href="<?=Route::url('rss')?>" />


    <?if (Model_Category::current()->loaded() AND Model_Location::current()->loaded()):?>
    <link rel="alternate" type="application/atom+xml"  title="RSS <?=HTML::chars(Core::config('general.site_name').' - '.Model_Category::current()->name)?> - <?=Model_Location::current()->name?>"  href="<?=Route::url('rss',array('category'=>Model_Category::current()->seoname,'location'=>Model_Location::current()->seoname))?>" />
    <?elseif (Model_Location::current()->loaded()):?>
    <link rel="alternate" type="application/atom+xml"  title="RSS <?=HTML::chars(Core::config('general.site_name').' - '.Model_Location::current()->name)?>"  href="<?=Route::url('rss',array('category'=>URL::title(__('all')),'location'=>Model_Location::current()->seoname))?>" />
    <?elseif (Model_Category::current()->loaded()):?>
    <link rel="alternate" type="application/atom+xml"  title="RSS <?=HTML::chars(Core::config('general.site_name').' - '.Model_Category::current()->name)?>"  href="<?=Route::url('rss',array('category'=>Model_Category::current()->seoname))?>" />
    <?endif?>     
        
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
    <!--[if lt IE 9]>
      <?=HTML::script('http://html5shim.googlecode.com/svn/trunk/html5.js')?>
    <![endif]-->
    
    <?=Theme::styles($styles)?>	
	<?=Theme::scripts($scripts)?>
    <?if ( Kohana::$environment === Kohana::PRODUCTION AND core::config('general.analytics')!=='' ): ?>
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '<?=Core::config('general.analytics')?>']);
      _gaq.push(['_setDomainName', '<?=$_SERVER['SERVER_NAME']?>']);
      _gaq.push(['_setAllowLinker', true]);
      _gaq.push(['_trackPageview']);
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script> 
    <?endif?>
    <link rel="shortcut icon" href="<?=core::config('general.base_url').'images/favicon.ico'?>">


    
  </head>

  <body data-spy="scroll" data-target=".subnav" data-offset="50">

    <script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '550549481743159',
      xfbml      : true,
      version    : 'v2.1'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>



    <?if(!isset($_COOKIE['accept_terms']) AND core::config('general.alert_terms') != ''):?>
        <?=View::factory('alert_terms')?>
    <?endif?>

	<?=$header?>
    <div class="container bs-docs-container">
    <div class="alert alert-warning off-line" style="display:none;"><strong><?=__('Warning')?>!</strong> <?=__('We detected you are currently off-line, please connect to gain full experience.')?></div>
        <div class="row">


     
            <div class="col-xs-8">
                <?=Breadcrumbs::render('breadcrumbs')?>
                <?=Alert::show()?>
                <?=$content?>


                
            </div><!--/span-->



            <?= FORM::open(Route::url('search'), array('class'=>'col-xs-4', 'method'=>'GET', 'action'=>''))?>



                <div class="form-group">

                 <?= FORM::close()?>


  <!--@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@>
<!--@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->

<!-- CHAT MARKUP -->
<div class="example-chat l-demo-container" style="width:292px; margin-top:0px;  margin-bottom:15px;box-shadow:none;">
  <header>Shopping Buddy Chat Room</header>

  <div class='example-chat-toolbar'>
    <label for="nameInput">Username:</label>
    <input type='text' id='nameInput' placeholder='enter a username...'>
  </div>

  <ul id='example-messages' class="example-chat-messages"></ul>

  <footer>
    <input type='text' id='messageInput'  placeholder='Type a message...'>
  </footer>
</div>


<!-- CHAT JAVACRIPT -->
<script>

  // CREATE A REFERENCE TO FIREBASE
  var messagesRef = new Firebase('https://amber-torch-6409.firebaseio.com/');

  // REGISTER DOM ELEMENTS
  var messageField = $('#messageInput');
  var nameField = $('#nameInput');
  var messageList = $('#example-messages');

  // LISTEN FOR KEYPRESS EVENT
  messageField.keypress(function (e) {
    if (e.keyCode == 13) {
      //FIELD VALUES
      var username = nameField.val();
      var message = messageField.val();

      //SAVE DATA TO FIREBASE AND EMPTY FIELD
      messagesRef.push({name:username, text:message});
      messageField.val('');
    }
  });

  // Add a callback that is triggered for each chat message.
  messagesRef.limit(10).on('child_added', function (snapshot) {
    //GET DATA
    var data = snapshot.val();
    var username = data.name || "anonymous";
    var message = data.text;

    //CREATE ELEMENTS MESSAGE & SANITIZE TEXT
    var messageElement = $("<li>");
    var nameElement = $("<strong class='example-chat-username'></strong>")
    nameElement.text(username);
    messageElement.text(message).prepend(nameElement);

    //ADD MESSAGE
    messageList.append(messageElement)

    //SCROLL TO BOTTOM OF MESSAGE LIST
    messageList[0].scrollTop = messageList[0].scrollHeight;

    messageList.remove(messageElement);   
  });

</script>



<a class="twitter-timeline" href="https://twitter.com/hashtag/buy1get1" data-widget-id="513426358739820545" height="552px"  style="margin-left:10px;">#buy1get1 Tweets</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                </div>  



         
           
            <?=View::fragment('sidebar_front','sidebar')?>
        </div><!--/row-->


        <?=$footer?>
    </div><!--/.fluid-container-->
  
  <?=Theme::scripts($scripts,'footer')?>
	
		

  <?=(Kohana::$environment === Kohana::DEVELOPMENT)? View::factory('profiler'):''?>


  </body>

</html>




<style>
.example-base {
  font-family: "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
  color: #333; }

.hide {
  display: none; }

.l-demo-container {
  width: 500px;
  margin: 40px auto 0px auto; }

.l-popout {
  margin: 0;
  padding: 0;
  border: 0; }
  .l-popout iframe {
    width: 100%;
    height: 100%;
    min-height: 100%;
    margin: 0;
    padding: 0;
    border: 0; }

.example-chat {
  font-family: "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
  border-radius: 3px;
  -webkit-box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
  box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
  background-color: #dfe3ea;
  border: 1px solid #CCC;
  overflow: auto;
  padding: 0px;
  font-size: 18px;
  line-height: 22px;
  color: #666; }
  .example-chat header {
    background-color: #EEE;
    background: -webkit-gradient(linear, left top, left bottom, from(#EEEEEE), to(#DDDDDD));
    background: -webkit-linear-gradient(top, #EEEEEE, #DDDDDD);
    background: linear-gradient(top, #EEEEEE, #DDDDDD);
    -webkit-box-shadow: inset 0px 1px 0px rgba(255, 255, 255, 0.9), 0px 1px 2px rgba(0, 0, 0, 0.1);
    box-shadow: inset 0px 1px 0px rgba(255, 255, 255, 0.9), 0px 1px 2px rgba(0, 0, 0, 0.1);
    border-radius: 3px 3px 0px 0px;
    border-bottom: 1px solid #CCC;
    line-height: 24px;
    font-size: 12px;
    text-align: center;
    color: #999; }
  .example-chat input {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-box-shadow: inset 0px 1px 3px rgba(0, 0, 0, 0.2);
    box-shadow: inset 0px 1px 3px rgba(0, 0, 0, 0.2);
    border-radius: 3px;
    padding: 0px 10px;
    height: 30px;
    font-size: 18px;
    width: 100%;
    font-weight: normal;
    outline: none; }
  .example-chat .example-chat-toolbar {
    background-color: #FFF;
    padding: 10px;
    position: relative;
    border-bottom: 1px solid #CCC; }
    .example-chat .example-chat-toolbar label {
      text-transform: uppercase;
      line-height: 32px;
      font-size: 14px;
      color: #999;
      position: absolute;
      top: 10px;
      left: 20px;
      z-index: 1; }
    .example-chat .example-chat-toolbar input {
      -webkit-box-shadow: none;
      box-shadow: none;
      border: 1px solid #FFF;
      padding-left: 100px;
      color: #999; }
      .example-chat .example-chat-toolbar input:active, .example-chat .example-chat-toolbar input:focus {
        color: #1d9dff;
        border: 1px solid #FFF; }
  .example-chat ul {
    list-style: none;
    margin: 0px;
    padding: 20px;
    height: 200px;
    overflow: auto; }
    .example-chat ul li {
      margin-bottom: 10px;
      line-height: 24px; }
      .example-chat ul li:last-child {
        margin: 0px; }
    .example-chat ul .example-chat-username {
      margin-right: 10px; }
  .example-chat footer {
    display: block;
    padding: 10px; }
    .example-chat footer input {
      border: 1px solid #ced3db;
      height: 40px; }

#colorholder {
  width: 480px;
  height: 30px;
  border: 2px solid #424547;
  margin: 5px auto 0px auto; }

#drawing-canvas {
  border: 3px solid #999; }

.colorbox {
  width: 22px;
  height: 22px;
  margin: 1px;
  display: inline-block;
  border: 3px solid black; }

.example-leaderboard {
  -webkit-box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.3);
  box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.3);
  padding: 20px;
  border-radius: 5px;
  background-color: white;
  overflow: auto;
  color: #666; }
  .example-leaderboard table {
    border-radius: 3px;
    margin-bottom: 20px;
    width: 100%;
    border-collapse: collapse; }
    .example-leaderboard table th {
      background: #EEE;
      border-bottom: 1px solid #CCC;
      font-size: 12px;
      color: #999;
      padding: 5px 10px;
      text-align: left; }
    .example-leaderboard table td {
      border-bottom: 1px solid #EEE;
      padding: 10px;
      color: #28a562; }
      .example-leaderboard table td em {
        font-style: normal;
        font-weight: bold;
        color: #666; }
    .example-leaderboard table tr:first-child td {
      background: green; }
  .example-leaderboard input {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-box-shadow: inset 0px 1px 3px rgba(0, 0, 0, 0.2);
    box-shadow: inset 0px 1px 3px rgba(0, 0, 0, 0.2);
    border-radius: 3px;
    padding: 0px 10px;
    height: 30px;
    font-size: 18px;
    font-weight: normal;
    outline: none;
    border: 1px solid #CCC; }
    .example-leaderboard input.example-leaderboard-name {
      width: 186px;
      margin-right: 10px; }
    .example-leaderboard input.example-leaderboard-score {
      width: 300px; }

#highestscore {
  margin-top: 20px;
  font-size: 14px; }

/* Presence */
#presenceDiv {
  text-align: center; }

/* Tetris */
.tetris-body {
  width: 600px; }

#canvas0, #canvas1 {
  display: inline-block;
  border: 4px solid #424547; }

#restartButton {
  margin-top: 5px; }

#gameInProgress {
  font-size: 14px; }

  </style>