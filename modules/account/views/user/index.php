<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\account\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Личный кабинет';
$model = User::findOne(Yii::$app->user->id);
?>
<div class="user-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'surname',
            'patronymic',
            [
                'attribute' => 'email',
                'format' => 'html',
                'value' => "<div class='d-md-flex justify-content-between'><div>"
                    . $model->email
                    . "</div>"
                    . Html::a("Изменить", ['change', 'field' => 'email'], ['class' => 'btn btn-outline-success'])
                    . "</div>"

            ],
            [
                'attribute' => 'phone',
                'format' => 'html',
                'value' => "<div class='d-md-flex justify-content-between'><div>"
                    . $model->phone
                    . "</div>"
                    . Html::a("Изменить", ['change', 'field' => 'phone'], ['class' => 'btn btn-outline-success'])
                    . "</div>"

            ],
            [
                'attribute' => 'role',
                'value' => $model->role ? "Админ" : "Пользователь"
            ]

        ],
    ]) ?>

    <?= Html::a("Купить билет", ['/account/trip'], ['class' => 'btn btn-outline-primary']) ?>
    <?= Html::a("Мои документы", ['/account/passport'], ['class' => 'btn btn-outline-primary']) ?>

</div>