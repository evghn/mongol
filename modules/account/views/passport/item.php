<?php

use app\models\DocType;
?>
<div class="border p-2 m-2">
    <div>
        Название документа: <?= DocType::getTitle($model->passport_type_id) ?>
    </div>
    <?php if ($model->passport_type_id == 4) { ?>
        <div>
            Название: <?= $model->passport_another ?>
        </div>
    <?php } ?>
    <div>
        Действует до: <?= $model->passport_expire ?>
    </div>
    <div>
        Номер документа: <?= $model->passport_number ?>
    </div>
    <div class="mt-2">
       Статус: <span class=" px-2 py-1 bg-opacity-10 <?= $model->passport_expire < date('Y-m-d') ? "bg-danger" : "bg-success"?>"><?= $model->passport_expire < date('Y-m-d') ? "Пасспорт просрочнен" : "Паспорт действителен" ?></span>
    </div>
</div>