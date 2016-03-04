<?php

$page = 'Карты сервера';

$this->pageTitle = Yii::app()->name . ' - ' . $page;

$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    'links'=>array($page),
));

?>

<h2><?php echo $page; ?></h2>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'dataProvider'=>$dataProvider,
	'type'=>'striped bordered condensed',
	'id' => 'admins-grid',
	'summaryText' => 'Показано с {start} по {end} карт из {count}. Страница {page} из {pages}',
	'enableSorting' => false,
	'pager' => array(
		'class'=>'bootstrap.widgets.TbPager',
		'displayFirstAndLast' => true,
	),
	'columns'=>array(
		array(
			'name' => 'map',
			'value' => '$data->map',
		),
		array(
			'name' => 'games',
			'value' => '$data->games',
		),
        array(
			'name' => 'human_wins',
			'value' => '$data->human_wins',
		),
        array(
			'name' => 'zombie_wins',
			'value' => '$data->zombie_wins',
		),
	),
)); ?>
