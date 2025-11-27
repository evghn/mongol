<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property string $title
 * @property string $alias
 */
class Direction extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'alias'], 'required'],
            [['title'], 'string', 'max' => 20],
            [['alias'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'alias' => 'Alias',
        ];
    }

    /**
     * Gets query for [[Timetables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimetables()
    {
        return $this->hasMany(Timetable::class, ['direction_id' => 'id']);
    }



    public static function getDiractionId($alias)
    {
        return static::findOne(["alias" => $alias])->id;
    }


    public static function getDirections()
    {
        return static::find()
            ->select("title")
            ->indexBy('id')
            ->column();
    }
}
