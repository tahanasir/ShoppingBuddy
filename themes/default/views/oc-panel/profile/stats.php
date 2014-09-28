<div class="page-header">
    <h1><?=__('Statistics')?></h1>   
</div>

<div class="row">

    <div class="col-md-9">

        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th><?=__('Today')?></th>
                    <th><?=__('Yesterday')?></th>
                    <th><?=__('Last 30 days')?></th>
                    <th><?=__('Total')?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b><?=__('Contacts')?></b></td>
                    <td><?=$contacts_today?></td>
                    <td><?=$contacts_yesterday?></td>
                    <td><?=$contacts_month?></td>
                    <td><?=$contacts_total?></td>
                </tr>
                <tr>
                    <td><b><?=__('Visits')?></b></td>
                    <td><?=$visits_today?></td>
                    <td><?=$visits_yesterday?></td>
                    <td><?=$visits_month?></td>
                    <td><?=$visits_total?></td>
                </tr>
            </tbody>
        </table>

        <hr>
        <h2><?=__('Charts')?></h2>

        <form id="edit-profile" class="form-inline" method="post" action="">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><?=__('From')?></div>
                    <input type="text" class="form-control" id="from_date" name="from_date" value="<?=$from_date?>" data-date="<?=$from_date?>" data-date-format="yyyy-mm-dd">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <span>-</span>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><?=__('To')?></div>
                    <input type="text" class="form-control" id="to_date" name="to_date" value="<?=$to_date?>" data-date="<?=$to_date?>" data-date-format="yyyy-mm-dd">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?=__('Filter')?></button>
        </form>
        
    </div> <!-- /.col-md-12 -->
    
</div> <!-- /.row -->

<br>

<?=Chart::column($stats_daily,array('title'=>__('Views and Contacts statistic'),
                                    'height'=>400,
                                    'width'=>800,
                                    'series'=>'{0:{targetAxisIndex:1, visibleInLegend: true}}'))?>                                 
