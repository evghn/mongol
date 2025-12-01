<?php

use app\models\Passport;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\modules\account\models\PassportSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Мои документы';
?>
<div class="passport-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a("Добавить новый документ", ['create'], ['class' => 'btn btn-outline-success'])  ?>


    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => 'item', 
        "pager" => [
            "class" => LinkPager::class
        ]
        
    ]) ?>

</div>