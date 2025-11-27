<?php

use app\models\Direction;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\admin\models\Timetable $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Расписание рейсов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timetable-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="mt-5 mb-3">
        <?= Html::a('Открыть следующее расписание', ['create'], ['class' => 'btn btn-outline-success', 'data-method' => "post"]) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        "pager" => [
            "class" => LinkPager::class,
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'direction_id',
                "format" => "raw",
                'value' => fn($model) => $model->direction->title,
                "filter" => Direction::getDirections(),
            ],

            [
                'attribute' => 'date_start',
                'value' => fn($model) => Yii::$app->formatter->asDatetime($model->date_start, "php:d.m.Y H:i:s"),
                "filter" => false,
            ],


            [
                'attribute' => 'date_end',
                'value' => fn($model) => Yii::$app->formatter->asDatetime($model->date_end, "php:d.m.Y H:i:s"),
                "filter" => false,
            ],
            [
                'label' => 'Действие',
                "format" => "html",
                "value" => fn($model) => Html::a("Просмотр", ["view", "id" => $model->id], ["class" => "btn btn-outline-primary"]),
                "filter" => false,
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>