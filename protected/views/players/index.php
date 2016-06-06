<?php

$page = 'Игроки сервера';

$this->pageTitle = Yii::app()->name . ' - ' . $page;

$this->breadcrumbs=array(
	$page,
);

/*
Yii::app()->clientScript->registerScript('playerlist', "
$('.bantr').live('click', function(){
	$('#loading').show();
	var pid = this.id.substr(4);
	$.post('".Yii::app()->createUrl('players/playerdetail/')."', {'id': pid}, function(data){
		eval(data);
	});
})
");
*/

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').slideToggle(1500);
    return false;
});
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('bans-grid', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<h2><?php echo $page; ?></h2>

<?php echo CHtml::link('Поиск','#',array('class'=>'search-button btn btn-small')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
    'model'=>$model,
)); ?>
</div>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
	'id'=>'bans-grid',
    'dataProvider'=>isset($_GET['Players']) ? $model->search() : $dataProvider,
	//'template' => '{items} {pager}',
	'summaryText' => 'Показано с {start} по {end} игроков из {count}. Страница {page} из {pages}',
	'htmlOptions' => array(
		'style' => 'width: 100%'
	),
    'rowHtmlOptionsExpression'=>'array(
		"id" => "ban_$data->id",
		"style" => "cursor:pointer;",
		"class" => $data->id == 1 ? "bantr success" : "bantr",
        "onclick" => "document.location.href=\'".Yii::app()->createUrl("/players/view", array("id" => $data->id))."\'"
	)',
	'pager' => array(
		'class'=>'bootstrap.widgets.TbPager',
		'displayFirstAndLast' => true,
	),
	'columns'=>array(
        /*
        array(
			'name' => 'rank',
			'value' => '$data->rank',
		),
        */
		array(
			'name' => 'nick',
			'value' => '$data->nick',
		),
        /*
		array(
			'name' => 'ip',
			'value' => '$data->ip',
		),
        */
        /*
        array(
			'name' => 'steam_id',
			'value' => '$data->steam_id',
		),
        */
        array(
			'name' => 'skill',
			'value' => '$data->skill',
		),
        array(
			'name' => 'last_seen',
			'value' => 'date("d.m.Y", $data->last_seen)',
		),
        /*
        array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'header' => '',
			'template'=>'{view}',
		),
        */
	),
)); ?>

<?php $this->beginWidget('bootstrap.widgets.TbModal',
	array(
		'id'=>'PlayerDetail',
//		'htmlOptions'=> array('style'=>' width: 600px; margin-left: -300px'),
)); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Подробности игрока </h4>
</div>

<div class="modal-body" id="ban_name">
<table class="items table table-bordered table-condensed" style="margin: 0 auto">
	<tr class="odd">
		<td class="span3">
			<b>Ник</b>
		</td>
		<td class="span6" id="playerdetail-nick">
		</td>
	</tr>
	<tr class="odd">
		<td>
			<b>Место</b>
		</td>
		<td id="playerdetail-rank">
		</td>
	</tr>
	<tr class="odd">
		<td>
			<b>Скилл</b>
		</td>
		<td id="playerdetail-skill">
		</td>
	</tr>
	<!--tr class="odd">
		<td>
			<b>IP адрес</b>
		</td>
		<td id="playerdetail-ip">
		</td>
	</tr-->
	<tr class="odd">
		<td>
			<b>Стим</b>
		</td>
		<td id="playerdetail-steam">
		</td>
	</tr>
	<tr class="odd">
		<td>
			<b>Был на сервере</b>
		</td>
		<td id="playerdetail-datetime">
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center">
			<?php $this->widget('bootstrap.widgets.TbButton', array(
				'label'=>'Показать подробности',
				'url'=> '#',
				'htmlOptions'=>array('id' => 'viewplayer'),
			)); ?>
		</td>
	</tr>
</table>
<br>

</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Закрыть',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
<?php $this->endWidget(); ?>
