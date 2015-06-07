<!-- EXAMPLE FORM IMPORT -->
<?php $form = \yii\widgets\ActiveForm::begin([
	'options' => [
		'enctype'=> 'multipart/form-data',
	],
	'action' => ['import'],
]) ?>
<?= $form->field($modelImport,'fileImport')->fileInput() ?>
<?= Html::submitButton('Import',['class'=>'btn btn-primary']) ?>
<?php \yii\widgets\ActiveForm::end() ?>
<!-- EXAMPLE FORM IMPORT -->

<!-- EXAMPLE BUTTON EXPORT PHPEXCEL -->
<?= Html::a('Export Excel', ['export-excel'], ['class'=>'btn btn-info']); ?>  

<!-- EXAMPLE BUTTON EXPORT OPENTBS -->
<?= Html::a('Export Word', ['export-word'], ['class'=>'btn btn-warning']); ?>  
<?= Html::a('Export Excel', ['export-excel2'], ['class'=>'btn btn-info']); ?>  

<!-- EXAMPLE BUTTON EXPORT MPDF -->
<?= Html::a('Export PDF', ['export-pdf'], ['class'=>'btn btn-success']); ?>  