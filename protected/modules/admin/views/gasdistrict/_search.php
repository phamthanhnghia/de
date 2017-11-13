<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'province_id'); ?>
		<?php echo $form->dropDownList($model,'province_id', GasProvince::getArrAll(),array('empty'=>'Select','style'=>'width:350px;')); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'name',array('label' => Yii::t('translation','Name'))); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>150)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'short_name',array('label' => 'Tên Tiếng Việt Không Dấu')); ?>
		<?php echo $form->textField($model,'short_name',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row buttons" style="padding-left: 159px;">
		        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'label'=>Yii::t('translation','Search'),
            'type'=>'null', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
            'size'=>'small', // null, 'large', 'small' or 'mini'
            //'htmlOptions' => array('style' => 'margin-bottom: 10px; float: right;'),
        )); ?>	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->