<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_type".
 *
 * @property int $id
 * @property string $title
 */
class DocType extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doc_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_region'], 'default', 'value' => null],
            [['title'], 'required'],
            [['id_region'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['id_region'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['id_region' => 'id']],
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
            'id_region' => 'Id Region',
        ];
    }


    /** 
     * Gets query for [[Region]]. 
     * 
     * @return \yii\db\ActiveQuery 
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'id_region']);
    }



    public static function getDocTypes()
    {
        return self::find()
            ->select("title")
            ->indexBy('id')
            ->column();
    }
}
