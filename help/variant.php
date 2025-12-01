CREATE TABLE `seat_reservation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `timetable_id` INT NOT NULL,
  `seat_number` INT NOT NULL,
  `start_stop_point_id` INT NOT NULL,
  `end_stop_point_id` INT NOT NULL,
  `user_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timetable_id` (`timetable_id`),
  KEY `start_stop_point_id` (`start_stop_point_id`),
  KEY `end_stop_point_id` (`end_stop_point_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `seat_reservation_ibfk_1` FOREIGN KEY (`timetable_id`) REFERENCES `timetable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `seat_reservation_ibfk_2` FOREIGN KEY (`start_stop_point_id`) REFERENCES `stop_point` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `seat_reservation_ibfk_3` FOREIGN KEY (`end_stop_point_id`) REFERENCES `stop_point` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `seat_reservation_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<?php
namespace app\models;

use yii\db\ActiveRecord;

class SeatReservation extends ActiveRecord
{
    public static function tableName()
    {
        return 'seat_reservation';
    }

    public function rules()
    {
        return [
            [['timetable_id', 'seat_number', 'start_stop_point_id', 'end_stop_point_id'], 'required'],
            [['timetable_id', 'seat_number', 'start_stop_point_id', 'end_stop_point_id', 'user_id'], 'integer'],
            [['start_stop_point_id', 'end_stop_point_id'], 'validateStopOrder'],
        ];
    }

    public function validateStopOrder($attribute)
    {
        if ($this->start_stop_point_id >= $this->end_stop_point_id) {
            $this->addError($attribute, 'Start stop must be before end stop.');
        }
    }

    public function getTimetable()
    {
        return $this->hasOne(Timetable::class, ['id' => 'timetable_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getStartStopPoint()
    {
        return $this->hasOne(StopPoint::class, ['id' => 'start_stop_point_id']);
    }

    public function getEndStopPoint()
    {
        return $this->hasOne(StopPoint::class, ['id' => 'end_stop_point_id']);
    }
}


<?php
namespace app\models;

use yii\db\ActiveRecord;

class Timetable extends ActiveRecord
{
    public static function tableName()
    {
        return 'timetable';
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }

    public function getRouteStopPoints()
    {
        return $this->hasMany(RouteStopPoint::class, ['tabletime_id' => 'id'])->orderBy('stop_point_id');
    }

    public function getReservations()
    {
        return $this->hasMany(SeatReservation::class, ['timetable_id' => 'id']);
    }

    /**
     * Возвращает список свободных мест на участке
     * @param int $fromStopId ID начальной остановки
     * @param int $toStopId ID конечной остановки
     * @return array
     */
    public function getAvailableSeats($fromStopId, $toStopId)
    {
        $layout = $this->cabine; // JSON
        $totalSeats = 0;
        foreach ($layout['rows'] as $row) {
            $totalSeats += count($row);
        }

        $occupiedSeats = SeatReservation::find()
            ->select('seat_number')
            ->where(['timetable_id' => $this->id])
            ->andWhere([
                'and',
                ['<', 'start_stop_point_id', $toStopId],
                ['>', 'end_stop_point_id', $fromStopId]
            ])
            ->column();

        $allSeats = range(1, $totalSeats);
        $availableSeats = array_diff($allSeats, $occupiedSeats);

        return array_values($availableSeats);
    }
}

<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Timetable;
use app\models\RouteStopPoint;
use app\models\SeatReservation;

class TripController extends Controller
{
    public function actionSeats($id)
    {
        $timetable = Timetable::findOne($id);
        if (!$timetable) {
            throw new \yii\web\NotFoundHttpException('Timetable not found');
        }

        $routeStops = $timetable->getRouteStopPoints()->all();
        $stops = [];
        foreach ($routeStops as $rs) {
            $stops[] = $rs->stopPoint;
        }

        return $this->render('seats', [
            'timetable' => $timetable,
            'stops' => $stops,
        ]);
    }

    public function actionGetAvailableSeats()
    {
        $request = Yii::$app->request;
        $timetableId = $request->get('timetable_id');
        $fromStopId = $request->get('from_stop_id');
        $toStopId = $request->get('to_stop_id');

        $timetable = Timetable::findOne($timetableId);
        if (!$timetable) {
            return $this->asJson(['error' => 'Timetable not found']);
        }

        if ($fromStopId >= $toStopId) {
            return $this->asJson(['error' => 'Invalid stop range']);
        }

        $availableSeats = $timetable->getAvailableSeats($fromStopId, $toStopId);
        return $this->asJson(['available_seats' => $availableSeats]);
    }

    public function actionBookSeat()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $timetableId = $request->post('timetable_id');
        $seatNumber = $request->post('seat_number');
        $fromStopId = $request->post('start_stop_id');
        $toStopId = $request->post('end_stop_id');

        if ($fromStopId >= $toStopId) {
            return ['error' => 'Неверный диапазон остановок.'];
        }

        $existing = SeatReservation::find()
            ->where(['timetable_id' => $timetableId, 'seat_number' => $seatNumber])
            ->andWhere([
                'and',
                ['<', 'start_stop_point_id', $toStopId],
                ['>', 'end_stop_point_id', $fromStopId]
            ])
            ->one();

        if ($existing) {
            return ['error' => 'Место занято на данном участке.'];
        }

        $reservation = new SeatReservation();
        $reservation->timetable_id = $timetableId;
        $reservation->seat_number = $seatNumber;
        $reservation->start_stop_point_id = $fromStopId;
        $reservation->end_stop_point_id = $toStopId;
        $reservation->user_id = Yii::$app->user->id;

        if ($reservation->save()) {
            return ['success' => true];
        } else {
            return ['error' => 'Не удалось забронировать место.'];
        }
    }

    public function actionHistory()
    {
        $reservations = SeatReservation::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['timetable', 'startStopPoint', 'endStopPoint'])
            ->all();

        return $this->render('history', [
            'reservations' => $reservations,
        ]);
    }
}


<?php
use yii\helpers\Html;
use yii\helpers\Url;

$timetableId = $timetable->id;
?>

<h2>Выберите билеты для рейса</h2>

<form id="booking-form">
    <label>Откуда:</label>
    <select id="from-stop" required>
        <?php foreach ($stops as $stop): ?>
            <option value="<?= $stop->id ?>"><?= Html::encode($stop->name) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Куда:</label>
    <select id="to-stop" required>
        <?php foreach ($stops as $stop): ?>
            <option value="<?= $stop->id ?>"><?= Html::encode($stop->name) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Показать места</button>
</form>

<div id="seats-container" style="margin-top: 20px;"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
const timetableId = <?= $timetableId ?>;
const layout = <?= json_encode($timetable->cabine) ?>;

$('#booking-form').on('submit', function(e) {
    e.preventDefault();

    const fromStop = $('#from-stop').val();
    const toStop = $('#to-stop').val();

    if (parseInt(fromStop) >= parseInt(toStop)) {
        alert('Начальная остановка должна быть раньше конечной.');
        return;
    }

    $.get('<?= Url::to(['trip/get-available-seats']) ?>', {
        timetable_id: timetableId,
        from_stop_id: fromStop,
        to_stop_id: toStop
    }, function(data) {
        if (data.error) {
            alert(data.error);
        } else {
            renderSeats(data.available_seats);
        }
    });
});

function renderSeats(availableSeats) {
    const container = $('#seats-container');
    container.empty();

    const rows = layout.rows;

    rows.forEach(row => {
        const rowDiv = $('<div>').css({
            display: 'flex',
            marginBottom: '10px'
        });

        row.forEach(seat => {
            const seatDiv = $('<div>')
                .addClass('seat')
                .text(seat.number)
                .data('seat', seat.number)
                .css({
                    width: '40px',
                    height: '40px',
                    border: '1px solid #ccc',
                    textAlign: 'center',
                    lineHeight: '40px',
                    margin: '2px'
                });

            if (availableSeats.includes(seat.number)) {
                seatDiv
                    .addClass('available')
                    .css('backgroundColor', '#90EE90')
                    .css('cursor', 'pointer');
            } else {
                seatDiv
                    .addClass('occupied')
                    .css('backgroundColor', '#FFB6C1')
                    .css('cursor', 'not-allowed');
            }

            rowDiv.append(seatDiv);
        });

        container.append(rowDiv);
    });

    $('.seat.available').on('click', function() {
        const seatNumber = $(this).data('seat');
        const fromStop = $('#from-stop').val();
        const toStop = $('#to-stop').val();

        if (confirm(`Забронировать место ${seatNumber} от остановки ${fromStop} до ${toStop}?`)) {
            bookSeat(seatNumber, fromStop, toStop);
        }
    });
}

function bookSeat(seatNumber, fromStop, toStop) {
    $.ajax({
        url: '<?= Url::to(['trip/book-seat']) ?>',
        method: 'POST',
        data: JSON.stringify({
            timetable_id: timetableId,
            seat_number: seatNumber,
            start_stop_id: fromStop,
            end_stop_id: toStop
        }),
        contentType: 'application/json',
        success: function(data) {
            if (data.success) {
                alert('Место успешно забронировано!');
                location.reload();
            } else {
                alert(data.error || 'Ошибка при бронировании');
            }
        }
    });
}
</script>




/***** изменения - резервирование через пост запрос обычный */
views/trip/seats.php
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



Обновлённый TripController.php
public function actionBookSeat()
{
    $request = Yii::$app->request;
    $timetableId = $request->post('timetable_id');
    $seatNumber = $request->post('seat_number');
    $fromStopId = $request->post('start_stop_id');
    $toStopId = $request->post('end_stop_id');

    if ($fromStopId >= $toStopId) {
        Yii::$app->session->setFlash('error', 'Неверный диапазон остановок.');
        return $this->redirect(['trip/seats', 'id' => $timetableId]);
    }

    $existing = SeatReservation::find()
        ->where(['timetable_id' => $timetableId, 'seat_number' => $seatNumber])
        ->andWhere([
            'and',
            ['<', 'start_stop_point_id', $toStopId],
            ['>', 'end_stop_point_id', $fromStopId]
        ])
        ->one();

    if ($existing) {
        Yii::$app->session->setFlash('error', 'Место занято на данном участке.');
        return $this->redirect(['trip/seats', 'id' => $timetableId, 'from_stop_id' => $fromStopId, 'to_stop_id' => $toStopId]);
    }

    $reservation = new SeatReservation();
    $reservation->timetable_id = $timetableId;
    $reservation->seat_number = $seatNumber;
    $reservation->start_stop_point_id = $fromStopId;
    $reservation->end_stop_point_id = $toStopId;
    $reservation->user_id = Yii::$app->user->id;

    if ($reservation->save()) {
        Yii::$app->session->setFlash('success', 'Место успешно забронировано!');
    } else {
        Yii::$app->session->setFlash('error', 'Не удалось забронировать место.');
    }

    return $this->redirect(['trip/seats', 'id' => $timetableId, 'from_stop_id' => $fromStopId, 'to_stop_id' => $toStopId]);
}