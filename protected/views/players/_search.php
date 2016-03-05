<?php 
Yii::app()->clientScript->registerScript('playersearch', '
	$("#Players_server_ip").change(function(){
		$.post("'.Yii::app()->createUrl('/players/index').'", {"server": $(this).val()}, function(data) {eval(data);});
		return false;
	});
');

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array( 
    'action'=>Yii::app()->createUrl($this->route), 
    'method'=>'get', 
));
?>

    <?php echo $form->textFieldRow($model, 'nick', array('maxlength'=>32)); ?>

	<?php echo $form->textFieldRow($model, 'steam_id', array('maxlength'=>20)); ?>

    <?php echo $form->textFieldRow($model, 'ip', array('maxlength'=>15)); ?>

	<label for="Players_last_seen" class="required">Последний онлайн</label>
	<?php 
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'id' => 'last_seen',
				'attribute' => 'last_seen',
				'language' => 'ru',
				'i18nScriptFile' => 'jquery-ui-i18n.min.js',
				'htmlOptions' => array(
					'id' => 'last_seen',
					'size' => '10',
				),
				'options' => array(
					'showAnim'=>'fold',
				)
			)
		)
	?>

    <div class="form-actions"> 
        <?php $this->widget('bootstrap.widgets.TbButton', array( 
            'buttonType'=>'submit', 
            'type'=>'primary', 
            'label'=>'Искать', 
        )); ?>
    </div> 

<?php $this->endWidget(); ?>
