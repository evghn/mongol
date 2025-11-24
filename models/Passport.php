<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "passport".
 *
 * @property int $id
 * @property int $user_id
 * @property int $passport_type_id
 * @property string $passport_expire
 * @property string $passport_number
 * @property string|null $passport_another
 * @property int $expire_check
 */
class Passport extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'passport';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['passport_another'], 'default', 'value' => null],
            [['user_id', 'passport_type_id', 'passport_expire', 'passport_number'], 'required'],
            [['user_id', 'passport_type_id'], 'integer'],
            [['passport_expire'], 'safe'],
            [['passport_number', 'passport_another'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'passport_type_id' => 'Passport Type ID',
            'passport_expire' => 'Passport Expire',
            'passport_number' => 'Passport Number',
            'passport_another' => 'Passport Another',
        ];
    }
}
