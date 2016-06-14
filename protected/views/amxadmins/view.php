<?php
/**
 * Вьюшка просмотра деталей админа серверов
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

$this->pageTitle = Yii::app()->name . ' :: Админцентр - Редактировать админа';
$this->breadcrumbs = array(
	'Админцентр' => array('/admin/index'),
	'AmxModX админы' => array('admin'),
	'Админ ' . $model->nickname
);
$this->renderPartial('/admin/mainmenu', array('active' =>'server', 'activebtn' => 'servamxadmins'));

$this->menu=array(
	array('label'=>'Добавить AmxModX админа', 'url'=>array('create')),
	array('label'=>'Управление AmxModX админами', 'url'=>array('admin')),
);
?>
<h2>Детали админа &laquo;<?php echo $model->nickname; ?>&raquo;</h2>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'nickname',
		'username',
		'password',
		array(
			'name' => 'flags',
			'value' => Amxadmins::getAuthType($model->flags)
		),
		'access',
		'icq',
        array(
			'name' => 'is_active',
			'value' => $model->is_active ? 'Да' : 'Нет'
		),
		array(
			'name' => 'ashow',
			'value' => $model->ashow ? 'Да' : 'Нет'
		),
		array(
			'name' => 'created',
			'type' => 'datetime',
			'value' => $model->created
		),
	),
)); ?>
