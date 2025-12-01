<?php

namespace app\modules\account\controllers;

use Yii;
use yii\web\Controller;
use app\models\Timetable;
use app\models\RouteStopPoint;
use app\models\SeatReservation;
use yii\helpers\VarDumper;

class TripController extends Controller
{
    public function actionIndex()
    {
        VarDumper::dump(Timetable::getTimetables(), 10, true);
        die;
    }

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
