<?php

namespace app\modules\admin\controllers;

use app\models\Direction;
use app\models\Timetable;
use app\modules\admin\models\Timetable as TimetableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
                    'class' => VerbFilter::className(),
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
        if ($this->request->isPost) {
            $last = Timetable::find(["direction_id" => Direction::getDiractionId("mn")])
                ->orderBy("id", SORT_DESC)
                ->limit(1)
                ->all();

            /* поиск пятницы */

            if ($last) {
                $next5 = new \DateTime($last[0]->date_start);
                $next5->modify("+7 days");
            } else {
                // расписание пустое 
                $day_n = (int)date('N');
                $add = ($day_n < 6 ? 5 : 12) - $day_n;
                $next5 = new \DateTime();
                $next5->modify("+$add days");
            }

            $date_next5 = $next5->format('Y-m-d');

            $model = new Timetable();
            $model->date_start = $date_next5 . " 23:59:00";
            $model->date_end = $next5->modify("+1 days")->format('Y-m-d') . " 11:00:00";
            $model->direction_id = Direction::getDiractionId("mn");
            $model->save();

            $model = new Timetable();
            $model->date_start = $next5->modify("+1 days")->format('Y-m-d') . " 20:00:00";
            $model->date_end = $next5->modify("+1 days")->format('Y-m-d') . " 07:00:00";
            $model->direction_id = Direction::getDiractionId("ru");
            $model->save();
        }

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
}
