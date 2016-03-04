<?php

$page = 'Игроки сервера';

$this->pageTitle = Yii::app()->name . ' - ' . $page;

$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    'links'=>array($page),
));

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
	'dataProvider'=>$dataProvider,
	'type'=>'striped bordered condensed',
	'id' => 'admins-grid',
	'summaryText' => 'Показано с {start} по {end} игроков из {count}. Страница {page} из {pages}',
	'enableSorting' => false,
	'pager' => array(
		'class'=>'bootstrap.widgets.TbPager',
		'displayFirstAndLast' => true,
	),
	'columns'=>array(
		array(
			'name' => 'nick',
			'value' => '$data->nick',
		),
		array(
			'name' => 'ip',
			'value' => '$data->ip',
		),
        array(
			'name' => 'steam_id',
			'value' => '$data->steam_id',
		),
        array(
			'name' => 'last_seen',
			'value' => 'date("d.m.Y", $data->last_seen)',
		),
	),
)); ?>
