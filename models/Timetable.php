<?php

namespace app\models;

use DateTime;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "timetable".
 *
 * @property int $id
 * @property string $date_start
 * @property int $direction_id
 * @property string $date_end
 *
 * @property Direction $direction
 */
class Timetable extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timetable';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_start', 'direction_id', 'date_end'], 'required'],
            [['date_start', 'date_end'], 'safe'],
            [['direction_id'], 'integer'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::class, 'targetAttribute' => ['direction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_start' => 'Дата-время отправления',
            'direction_id' => 'Направление',
            'date_end' => 'Дата-время прибытия',
        ];
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }


    public static function getTimetables()
    {
        $date_start = date("Y-m-d H:i:s");
        $date_end = (new \DateTime($date_start))->modify("+10 days")->format("Y-m-d H:i:s");
        $date_start0 = self::find()
            ->select("date_start")
            ->where([">=", "date_start", $date_start])
            ->andWhere(["<=", "date_end", $date_end])
            ->one();

        $date_end = (new \DateTime($date_start0->date_start))->modify("+14 days")->format("Y-m-d H:i:s");


        return self::find()
            ->select(new Expression("concat(title, ': ', date_start)"))
            ->innerJoin("direction", "direction.id = timetable.direction_id")
            ->where([">=", "date_start", $date_start0->date_start])
            ->andWhere(["<=", "date_end", $date_end])
            ->indexBy("timetable.id")
            ->column()
        ;
    }
}
