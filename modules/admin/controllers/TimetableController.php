<?php

namespace app\modules\admin\controllers;

use app\models\Direction;
use app\models\Region;
use app\models\RouteStopPoint;
use app\models\RouteStopPointBase;
use app\models\StopPoint;
use app\models\Timetable;
use app\modules\admin\models\Timetable as TimetableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;

/**
 * TimetableController implements the CRUD actions for Timetable model.
 */
class TimetableController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Timetable models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TimetableSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Timetable model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Timetable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // 1 - проверяем есть ли запрос ПОСТ
        if ($this->request->isPost) {
            // ищем последнее созданное расписание
            $last = Timetable::find(["direction_id" => Direction::getDiractionId("mn")])
                ->orderBy(["id" => SORT_DESC])
                ->limit(1)
                ->all();

            /* поиск пятницы */

            // если расписавние нашлось
            if ($last) {
                // если оно есть, то делаем next5 по datetime + 7 дней
                $next5 = new \DateTime($last[0]->date_start);
                $next5->modify("+7 days");
            } else {
                // расписание пустое, то тогда делаем также переменную next5, однако считаем ещё сколько дней будет до следующей пятницы
                $day_n = (int)date('N');
                $add = ($day_n < 6 ? 5 : 12) - $day_n;
                $next5 = new \DateTime();
                $next5->modify("+$add days");
            }
            // создаём переменную, где будет лежать формат нашей даты
            $date_next5 = $next5->format('Y-m-d');

            // далее нужно создать в бд две новые записи: монгольское и русское направление
            $model = new Timetable();
            $model->date_start = $date_next5 . " 23:59:00";
            $model->date_end = $next5->modify("+1 days")->format('Y-m-d') . " 11:00:00";
            $model->direction_id = Direction::getDiractionId("mn");


            if ($model->save()) {
                $this->record_route_stop('mn', $date_next5, $model->id);
            }

            // $cityes = StopPoint::find()->select('name')->indexBy('id')->column();
            $model = new Timetable();
            $model->date_start = $next5->modify("+1 days")->format('Y-m-d') . " 20:00:00";
            $model->date_end = $next5->modify("+1 days")->format('Y-m-d') . " 07:00:00";
            $model->direction_id = Direction::getDiractionId("ru");
            $model->save();

            if ($model->save()) {
                $this->record_route_stop('ru', $date_next5, $model->id);
            }
        }
        // отправляем обратно в админа
        return $this->redirect('/admin');
    }

    /**
     * Finds the Timetable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Timetable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Timetable::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function record_route_stop($direction, $date, $id)
    {
        $base =  RouteStopPointBase::find()->where(['direction_id'  => Direction::getDiractionId($direction)])->asArray()->all();

        $date = new \DateTime($date);
        $time = null;

        foreach ($base as $value) {
            $route_stop_point = new RouteStopPoint();
            $route_stop_point->load($value, '');

            // var_dump($route_stop_point->time_out, $time, $route_stop_point->time_out < $time);
            if ($time && $route_stop_point->time_out) {
                if (strtotime($route_stop_point->time_out) < strtotime($time)) {
                    // var_dump($route_stop_point->time_out, $time);
                    $date->modify("+1 days");
                }
            }

            $time = $route_stop_point->time_out;


            if ($route_stop_point->time_out) {
                $route_stop_point->time_out = $date->format("Y-m-d") . ' ' . $route_stop_point->time_out;
            }
            if ($route_stop_point->time_in) {
                $route_stop_point->time_in = $date->format("Y-m-d") . ' ' . $route_stop_point->time_in;
            }

            $route_stop_point->tabletime_id = $id;
            if (!$route_stop_point->save()) {
                VarDumper::dump($route_stop_point->errors, 10, true);
            };
        }
    }
}
