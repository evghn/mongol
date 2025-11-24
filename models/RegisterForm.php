<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class RegisterForm extends Model
{



    public $name;
    public $surname;
    public $patronymic;
    public $phone;
    public $password;
    public $password_repeat;
    public $rules;
    public $email;
    public $passport_type_id;
    public $passport_expire;
    public $passport_number;
    public $passport_another;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [["passport_number", "passport_expire", "passport_type_id", 'name', 'surname', 'patronymic', 'email', 'phone', 'password_repeat', 'password'], 'required'],
            [['passport_another', 'name', 'surname', 'patronymic', 'email'], 'string', 'max' => 255],
            ["passport_type_id", "integer"],
            [['email'], 'email'],
            [['email'], 'unique', "targetClass" => User::class],
            [['name', 'surname', 'patronymic'], 'match', 'pattern' => '/^[а-яёa-z\-]+$/ui'],
            [['password', 'password_repeat'], 'match', 'pattern' => '/^[a-z\d]+$/i'],
            [['phone'], 'match', 'pattern' => '/^\+[\d]{11}$/', "message" => "формат \"+\" в начале и 11 цифр"],
            [['phone'], 'string', 'max' => 12],
            [['password_repeat', 'password'], 'string', "min" => 6, 'max' => 20],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            [['rules'], 'required', 'requiredValue' => 1, "message" => "Необходимо дать согласие на обработку персональных данных"],
            ['passport_expire', 'date', "format" => "php:Y-m-d"],
            [
                'passport_expire',
                'compare',
                'compareValue' => date('Y-m-d'),
                'operator' => '>=',
                'message' => 'Дата должна быть не раньше: ' . date("d.m.Y")
            ],
            [
                'passport_expire',
                'compare',
                'compareValue' => date('Y-m-d', strtotime('+190 days')), // Дата через 190 дней от сегодня
                'operator' => '>=',
                'message' => 'Срок действия пароля должен быть не менее 190 дней от текущей даты.',
                // Применяем правило ТОЛЬКО если пасспорт РФ
                'when' => function ($model) {
                    return $model->passport_type_id != 1;
                },
                'whenClient' => "(attribute, value) => $('#registerform-passport_type_id').val() != '' && $('#registerform-passport_type_id').val() != '1'"
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'email' => 'Email',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'rules' => 'Cогласие на обработку персональных данных',
            'passport_type' => 'Тип документа',
            'passport_expire' => 'Дата окончания действия документа',
            'passport_number' => 'Номер документа',
            'passport_another' => 'Другой документ',

        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
    public function register()
    {
        if ($this->validate()) {

            return true;
        }
        return false;
    }
}
