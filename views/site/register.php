<?php

use app\models\DocType;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\web\JqueryAsset;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var ActiveForm $form */
?>
<div class="site-register">

    <div class="my-3">
        <h3>
            Регистрация пользователя
        </h3>
    </div>

    <?php $form = ActiveForm::begin([
        "id" => "register-form"
    ]); ?>

    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'surname') ?>
    <?= $form->field($model, 'patronymic') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'phone') ?>
    <?= $form->field($model, 'passport_type_id')->dropDownList(DocType::getDocTypes(), ["prompt" => "Выберете документ"]) ?>

    <?= $form->field($model, 'passport_another', ["options" => ["class" => "d-none"]]) ?>
    <div class="d-flex gap-3 w-100 ">

        <?= $form->field($model, 'passport_expire')->textInput(["type" => "date"]) ?>
        <?= $form->field($model, 'passport_number') ?>
    </div>



    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'password_repeat')->passwordInput() ?>
    <?= $form->field($model, 'rules')->checkbox() ?>


    <div class="form-group">
        <?= Html::submitButton('Регистрация', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-register -->

<?php

$this->registerJsFile("/js/register.js", ["depends" => JqueryAsset::class]);
