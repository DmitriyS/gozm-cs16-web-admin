<?php
/**
 * Вьюшка просмотра деталей бана
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$page = 'Игроки';
$this->pageTitle = Yii::app()->name . ' - ' . $page . ' - Детали игрока ' . $model->nick;
$this->breadcrumbs=array(
	$page=>array('index'),
	$model->nick,
);
/*
if($geo) {
	Yii::app()->clientScript->registerScriptFile('//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU',CClientScript::POS_END);

	Yii::app()->clientScript->registerScript('yandexmap', "
		ymaps.ready(inityamaps);
		function inityamaps () {
			var myMap = new ymaps.Map('map', {
				center: [{$geo['lat']}, {$geo['lng']}],
				zoom: {$geo['zoom']},
				behaviors: ['default', 'scrollZoom']
			});
		}
	",CClientScript::POS_END);
}
*/
?>

<h2>Подробности игрока <i><?php echo CHtml::encode($model->nick); ?></i></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'type' => array('condensed', 'bordered'),
	'htmlOptions' => array('style'=>'text-align: left'),
	'attributes'=>array(
        'rank',
        'nick',
        'skill',
		array(
			'name' => 'steam_id',
			'type' => 'raw',
			'value' => Prefs::steam_convert($model->steam_id, TRUE)
				? CHtml::link($model->steam_id, 'http://steamcommunity.com/profiles/'
						. Prefs::steam_convert($model->steam_id), array('target' => '_blank'))
				: $model->steam_id,
		),
		/*
		array(
			'name' => 'player_ip',
			'type' => 'raw',
			'value' => $geo['country'] ? CHtml::link(
					$model->player_ip,
					'#',
					array(
						'onclick' => '$("#modal-map").modal("show");',
						'rel' => 'tooltip',
						'title' => 'Подробности IP адреса'
					)
				) : $model->player_ip,
			'visible' => ($ipaccess)
		),
		*/
		array(
			'name' => 'last_seen',
			'value' => date('d.m.Y - H:i:s', $model->last_seen),
		),
	),
)); ?>

<?php ?>
