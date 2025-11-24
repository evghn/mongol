<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stop_point".
 *
 * @property int $id
 * @property int $name
 * @property int $ru
 */
class StopPoint extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stop_point';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ru'], 'default', 'value' => 1],
            [['name'], 'required'],
            [['name', 'ru'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'ru' => 'Ru',
        ];
    }

}
