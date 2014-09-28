<html>
<body>

<head>
    <title><?=$title?></title>
</head>

<body>

    <div id="gmap" style="height:<?=$height?>;width:<?=$width?>;"></div>

</body>

  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.7"></script>
  <script type="text/javascript" src="//cdn.jsdelivr.net/maplace.js/0.1.2c/maplace.min.js"></script>

  <script type="text/javascript">
    var locations = [
    <?foreach ($ads as $ad):?>
    {       
            lat: <?=str_replace(',', '.', $ad->lat)?>,
            lon: <?=str_replace(',', '.', $ad->lon)?>,

            title: '<?=htmlentities(json_encode($ad->title),ENT_QUOTES)?>',
            html: '<p><?=__('Address')?>: <?=$ad->address?></p><div style="overflow: visible; cursor: default; clear: both; position: relative; background-color: rgb(255, 255, 255); border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px; padding: 0px; min-width: 150px; max-width:250px; height: 70px;"><div><div><p><a target="_blank" href="<?=Route::url('ad',  array('category'=>$ad->category,'seotitle'=>$ad->seotitle))?>"><?=htmlentities($ad->title,ENT_QUOTES)?></a></p></div></div></div>',

    },

    <?endforeach?>
    ];

    $(function() {
        new Maplace({
            locations: locations,
            controls_on_map: false,
            map_options: {
                //set_center: [<?=$center_lat?>,<?=$center_lon?> ],
                zoom: <?=$zoom?>
            }

        }).Load();
    });
  </script>

</html>