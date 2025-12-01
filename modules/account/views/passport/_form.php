<?php

use app\models\DocType;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Passport $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="passport-form">

    <?php $form = ActiveForm::begin([
        'id' => "passport-form"
    ]); ?>

    <?= $form->field($model, 'passport_type_id')->dropDownList(DocType::getData(), ['prompt' => 'Выберите документ из предложенных']) ?>

    <?= $form->field($model, 'passport_another', ['options' => ['class' => 'd-none']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'passport_expire')->textInput(['type' => 'date']) ?>

    <?= $form->field($model, 'passport_number')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php

$this->registerJsFile(
    'js/register_second.js',
    ['depends' => [yii\web\JqueryAsset::class]],
);