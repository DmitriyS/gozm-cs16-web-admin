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

$page = 'История банов';
$this->pageTitle = Yii::app()->name . ' - ' . $page . ' - Детали бана из истории ' . $model->player_nick;
$this->breadcrumbs=array(
	$page=>array('index'),
	$model->player_nick,
);
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

if($model->ban_length == '-1') {
    $length = 'Разбанен';
} else {
    $length = Prefs::date2word($model->ban_length) . ' (Истек)';
}
?>

<h2>Подробности истории бана <i><?php echo CHtml::encode($model->player_nick); ?></i></h2>
<div style="float: right">
	<?php
	if(Webadmins::checkAccess('bans_edit', $model->admin_nick)):
	echo CHtml::link(
		'<i class="icon-edit"></i>',
		$this->createUrl('/bans/update', array('id' => $model->bhid)),
		array(
			'rel' => 'tooltip',
			'title' => 'Редактировать',
		)
	);
	endif;
	?>
	&nbsp;
	<?php
	if(Webadmins::checkAccess('bans_delete', $model->admin_nick)):
	echo CHtml::ajaxLink(
		'<i class="icon-trash"></i>',
		$this->createUrl('/bans/delete', array('id' => $model->bhid, 'ajax' => 1)),
		array(
			'type' => 'post',
			'beforeSend' => 'function() {if(!confirm("Удалить бан?")) {return false;} }',
			'success' => 'function() {alert("Бан удален"); document.location.href="'.$this->createUrl('/bans/index').'"}'
		),
		array(
			'rel' => 'tooltip',
			'title' => 'Удалить бан',
		)
	);
	endif;
	?>
</div>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'type' => array('condensed', 'bordered'),
	'htmlOptions' => array('style'=>'text-align: left'),
	'attributes'=>array(
        'player_nick',
		array(
			'name' => 'player_id',
			'type' => 'raw',
			'value' => Prefs::steam_convert($model->player_id, TRUE)
				? CHtml::link($model->player_id, 'http://steamcommunity.com/profiles/'
						. Prefs::steam_convert($model->player_id), array('target' => '_blank'))
				: $model->player_id,
		),
		array(
			'name' => 'player_ip',
			'type' => 'raw',
			'value' => $geo['city'] ? CHtml::link(
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
		array(
			'name' => 'ban_created',
			'value' => date('d.m.Y - H:i:s', $model->ban_created),
		),
		array(
			'name' => 'ban_length',
			'type' => 'raw',
			'value' => $length
		),
		//'expiredTime',
		'map_name',
		'ban_reason',
		//'adminName:html',
		'admin_nick',
		'server_name',
		//'ban_kicks',
	),
)); ?>

<hr>
<p class="text-success">
	<i class="icon-calendar"></i>
	История банов
</p>
<?php
$this->widget('bootstrap.widgets.TbGridView',array(
	'type' => 'bordered stripped',
	'id'=>'ban-history-grid',
	'dataProvider'=>$history,
	'enableSorting' => FALSE,
	'template' => '{items} {pager}',
	'columns'=>array(
		array(
			'name' => 'ban_created',
			'value' => 'date("d.m.Y", $data->ban_created)',
		),
		array(
			'name' => 'player_nick',
			'type' => 'html',
			'value' => 'Chtml::link($data->player_nick, Yii::app()->createUrl("/history/view", array("id" => $data->bhid)))'
		),
		/*
		array(
			'name' => 'player_id',
			'type' => 'raw',
			'value' => 'Prefs::steam_convert($data->player_id, TRUE)
				? CHtml::link($data->player_id, "http://steamcommunity.com/profiles/"
						. Prefs::steam_convert($data->player_id), array("target" => "_blank"))
				: $data->player_id',
		),
		array(
			'name' => 'player_ip',
			'value' => '$data->player_ip',
			'visible' => $ipaccess
		),
		*/
		'ban_reason',
		array(
			'name' => 'ban_length',
			'type' => 'raw',
			'value' => 'Prefs::date2word($data->ban_length)'
		),
		array(
			'name' => 'unban_admin_nick',
			'value' => '$data->unban_admin_nick == "amxbans" ? "Срок бана истек" : $data->unban_admin_nick'
		),
	),
));
?>

<?php if($ipaccess): ?>
<?php $this->beginWidget('bootstrap.widgets.TbModal',
	array(
		'id'=>'modal-map',
//		'htmlOptions'=> array('style'=>' width:860px; margin-left: -430px;height: 600px'),
)); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
	<h3>Информация об IP "<?php echo $model->player_ip ?>"</h3>
</div>
<div class="modal-body" style="min-height: 200px">
	<div id="map" style="min-width:200px; min-height:200px; marg: 0 auto"></div>
	<div style="top: -30px">
		<b>Страна: </b>
		<?php echo $geo['country'] ?>
		<br>
		<b>Регион: </b>
		<?php echo $geo['region'] ?>
		<br>
		<b>Город: </b>
		<?php echo $geo['city'] ?>
	</div>
</div>
<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>'Закрыть',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
<?php $this->endWidget(); ?>
<?php endif; ?>
