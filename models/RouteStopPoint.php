<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "route_stop_point".
 *
 * @property int $id
 * @property int $stop_point_id
 * @property string|null $time_in
 * @property string|null $time_out
 * @property string|null $stop_time
 * @property int $tabletime_id
 * @property int|null $region_id
 * @property int $direction_id
 *
 * @property Direction $direction
 * @property Region $region
 * @property StopPoint $stopPoint
 * @property Timetable $tabletime
 */
class RouteStopPoint extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'route_stop_point';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stop_point_id', 'tabletime_id', 'direction_id'], 'required'],
            [['stop_point_id', 'tabletime_id', 'region_id', 'direction_id'], 'integer'],
            [['time_in', 'time_out', 'stop_time'], 'safe'],
            [['stop_point_id'], 'exist', 'skipOnError' => true, 'targetClass' => StopPoint::class, 'targetAttribute' => ['stop_point_id' => 'id']],
            [['tabletime_id'], 'exist', 'skipOnError' => true, 'targetClass' => Timetable::class, 'targetAttribute' => ['tabletime_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['region_id' => 'id']],
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
            'stop_point_id' => 'Stop Point ID',
            'time_in' => 'Time In',
            'time_out' => 'Time Out',
            'stop_time' => 'Stop Time',
            'tabletime_id' => 'Tabletime ID',
            'region_id' => 'Region ID',
            'direction_id' => 'Direction ID',
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

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }

    /**
     * Gets query for [[StopPoint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStopPoint()
    {
        return $this->hasOne(StopPoint::class, ['id' => 'stop_point_id']);
    }

    /**
     * Gets query for [[Tabletime]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTabletime()
    {
        return $this->hasOne(Timetable::class, ['id' => 'tabletime_id']);
    }
}
