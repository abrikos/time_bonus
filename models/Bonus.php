<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bonus".
 *
 * @property integer $id
 * @property integer $date
 * @property integer $price
 * @property integer $haircut
 */
class Bonus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bonus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'price', 'haircut'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'price' => 'Price',
        ];
    }


	public function getDateH()
	{
		return date('d-m-Y H:i:s', $this->date);
    }

    public function getHaircut0()
    {
        return $this->hasOne(Haircut::className(),['id'=>'haircut']);
    }

}
