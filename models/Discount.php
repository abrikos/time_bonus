<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discount".
 *
 * @property integer $id
  * @property integer $date
 * @property integer $price
 * @property integer $haircut
 */
class Discount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['haircut', 'date', 'price'], 'integer'],
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
