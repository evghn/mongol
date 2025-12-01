<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seat_reservation".
 *
 * @property int $id
 * @property int $timetable_id
 * @property int $seat_number
 * @property int $start_stop_point_id
 * @property int $end_stop_point_id
 * @property int|null $user_id
 *
 * @property StopPoint $endStopPoint
 * @property StopPoint $startStopPoint
 * @property Timetable $timetable
 * @property User $user
 */
class SeatReservation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seat_reservation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'default', 'value' => null],
            [['timetable_id', 'seat_number', 'start_stop_point_id', 'end_stop_point_id'], 'required'],
            [['timetable_id', 'seat_number', 'start_stop_point_id', 'end_stop_point_id', 'user_id'], 'integer'],
            [['timetable_id'], 'exist', 'skipOnError' => true, 'targetClass' => Timetable::class, 'targetAttribute' => ['timetable_id' => 'id']],
            [['start_stop_point_id'], 'exist', 'skipOnError' => true, 'targetClass' => StopPoint::class, 'targetAttribute' => ['start_stop_point_id' => 'id']],
            [['end_stop_point_id'], 'exist', 'skipOnError' => true, 'targetClass' => StopPoint::class, 'targetAttribute' => ['end_stop_point_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['start_stop_point_id', 'end_stop_point_id'], 'validateStopOrder', 'whenClient' => 'function (attribute, value) {
                const start = $("#" + attribute.modelName + "-start_stop_point_id").val();
                const end = $("#" + attribute.modelName + "-end_stop_point_id").val();
                return parseInt(start) >= parseInt(end);
            }'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timetable_id' => 'Timetable ID',
            'seat_number' => 'Seat Number',
            'start_stop_point_id' => 'Start Stop Point ID',
            'end_stop_point_id' => 'End Stop Point ID',
            'user_id' => 'User ID',
        ];
    }

    public function validateStopOrder($attribute)
    {
        if ($this->start_stop_point_id >= $this->end_stop_point_id) {
            $this->addError($attribute, 'Начальная остановкка должна быть после следующей.');
        }
    }



    /**
     * Gets query for [[EndStopPoint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEndStopPoint()
    {
        return $this->hasOne(StopPoint::class, ['id' => 'end_stop_point_id']);
    }

    /**
     * Gets query for [[StartStopPoint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStartStopPoint()
    {
        return $this->hasOne(StopPoint::class, ['id' => 'start_stop_point_id']);
    }

    /**
     * Gets query for [[Timetable]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimetable()
    {
        return $this->hasOne(Timetable::class, ['id' => 'timetable_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
