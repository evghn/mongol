<?php

use yii\helpers\Html;
use yii\helpers\Url;

$timetableId = $timetable->id;
?>

<h2>Выберите билеты для рейса</h2>

<form id="booking-form" method="get">
    <label>Откуда:</label>
    <select name="from_stop_id" id="from-stop" required>
        <?php foreach ($stops as $stop): ?>
            <option value="<?= $stop->id ?>" <?= isset($_GET['from_stop_id']) && $_GET['from_stop_id'] == $stop->id ? 'selected' : '' ?>>
                <?= Html::encode($stop->name) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Куда:</label>
    <select name="to_stop_id" id="to-stop" required>
        <?php foreach ($stops as $stop): ?>
            <option value="<?= $stop->id ?>" <?= isset($_GET['to_stop_id']) && $_GET['to_stop_id'] == $stop->id ? 'selected' : '' ?>>
                <?= Html::encode($stop->name) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Показать места</button>
</form>

<div id="seats-container" style="margin-top: 20px;">
    <?php if (isset($_GET['from_stop_id']) && isset($_GET['to_stop_id'])): ?>
        <?php if ($_GET['from_stop_id'] >= $_GET['to_stop_id']): ?>
            <p style="color: red;">Ошибка: начальная остановка должна быть раньше конечной.</p>
        <?php else: ?>
            <?php
            $fromStopId = (int)$_GET['from_stop_id'];
            $toStopId = (int)$_GET['to_stop_id'];

            $availableSeats = $timetable->getAvailableSeats($fromStopId, $toStopId);
            ?>
            <form method="post" action="<?= Url::to(['trip/book-seat']) ?>" id="seat-booking-form" style="display:inline;">
                <input type="hidden" name="timetable_id" value="<?= $timetableId ?>">
                <input type="hidden" name="start_stop_id" value="<?= $fromStopId ?>">
                <input type="hidden" name="end_stop_id" value="<?= $toStopId ?>">
                <input type="hidden" name="seat_number" id="seat-input" required>

                <?php if (empty($availableSeats)): ?>
                    <p>Нет свободных мест на данном участке.</p>
                <?php else: ?>
                    <div class="seats">
                        <?php foreach ($layout['rows'] as $row): ?>
                            <div style="display:flex; margin-bottom: 10px;">
                                <?php foreach ($row as $seat): ?>
                                    <?php $isAvailable = in_array($seat['number'], $availableSeats); ?>
                                    <?php if ($isAvailable): ?>
                                        <button type="submit" name="seat_number" value="<?= $seat['number'] ?>" style="
                                            width: 40px; height: 40px; border: 1px solid #ccc; margin: 2px; background-color: #90EE90; cursor: pointer;
                                        "><?= $seat['number'] ?></button>
                                    <?php else: ?>
                                        <div style="
                                            width: 40px; height: 40px; border: 1px solid #ccc; margin: 2px; background-color: #FFB6C1; cursor: not-allowed;
                                            text-align: center; line-height: 40px;
                                        "><?= $seat['number'] ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>