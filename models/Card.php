<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "card".
 *
 * @property integer $id
 * @property string $number
 * @property integer $percent
 */
class Card extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number'], 'string'],
            [['number'], 'unique'],
            [['percent'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'number' => 'Номер карты',
            'percent' => 'Процент',
            'bonus' => 'Сумма бонусов',
            'discount' => 'Произведено вычетов (руб)',
        ];
    }

	public function getHaircuts()
	{
		return $this->hasMany(Haircut::className(), ['card' => 'id'])->orderBy('time desc');
	}

    public function getBonuses()
    {
        return $this->hasMany(Bonus::className(),['haircut'=>'id'])->via('haircuts');
	}

	public function getDiscounts()
	{
        return $this->hasMany(Discount::className(),['haircut'=>'id'])->via('haircuts');
	}

	public function getBonus()
	{
        return $this->hasMany(Bonus::className(),['haircut'=>'id'])->via('haircuts')->sum('price') - $this->getDiscount();
	}

	public function bonusPrice($price)
	{
		return round($price /100 * $this->percent);
	}

    public function getDiscount()
    {
        return $this->hasMany(Discount::className(),['haircut'=>'id'])->via('haircuts')->sum('price');
	}

}
