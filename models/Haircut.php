<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "haircut".
 *
 * @property string $price
 * @property integer $shift_id
 * @property integer $master_id
 * @property integer $id
 * @property integer $card

 */
class Haircut extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'haircut';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['shift_id', 'master_id', 'card'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price' => 'Price',
            'shift_id' => 'Shift ID',
            'master_id' => 'Master ID',
            'id' => 'ID',
        ];
    }

    public function getShortTime()
    {
        return date("H:i", $this->time);
    }

    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['haircut_id' => 'id']);
    }

    public function getMaster()
    {
        return$this->hasOne(Master::className(),['id'=>'master_id']);
    }

    public function addMaterial($name, $price)
    {
        $material = new Material();
        $material->name = $name;
        $material->price = $price;
        $material->haircut_id = $this->id;
        $material->save();
        return $material->attributes;
    }

    public function getDiscount()
    {
        return $this->hasOne(Discount::className(), ['haircut' => 'id']);
    }

    public function getBonus()
    {
        return $this->hasOne(Bonus::className(), ['haircut' => 'id']);
    }
    public function getCard0()
    {
        return $this->hasOne(Card::className(), ['id'=>'card']);
    }


    public function drawInputCell()
    {
        $haircut = $this;
        $isBonus = $haircut->bonus ? ' hasBonus ' : '';
        //$isDiscount = $haircut->discount ? ' hasDiscount ' : '';
        $input = (0) ? "<span class='price-input $isBonus' onclick='alert(\"Возможно только удаление\")' data-id='{$haircut->id}'>{$this->price}</span>" : "<input value='{$haircut->price}' class='price-input $isBonus' onchange='saveHaircutPrice(this)' data-id='{$haircut->id}' id='haircut-price-{$haircut->id}'/>";
        return "<div id='container-haircut-{$haircut->id}'>
$input 
    <span class='btn btn-xs btn-default glyphicon glyphicon-pencil' style='float: right' onclick='haircutDialog({$haircut->id})'></span>
    <!--span class='btn btn-xs btn-default glyphicon glyphicon-trash' style='float: right' onclick='haircutRemove({$haircut->id})'></span-->
    <div  class='haircut-discount'>
    <span id='haircut-discount-{$haircut->id}'>".($haircut->discount ? $haircut->discount->price :'' )."</span>
    </div>
    </div>
";
    }

    public function bonusData()
    {
        return [
            'bonus'=>$this->bonus ? $this->bonus->price : 0,
            'discount'=>$this->discount ? $this->discount->price : 0,
            'price'=>$this->price,
            'card_bonus'=>$this->card0 ? $this->card0->bonus : 0,
            'card_number'=>$this->card0 ? $this->card0->number : null
        ];
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->bonus) $this->bonus->delete();
            if($this->discount) $this->discount->delete();
            return true;
        } else {
            return false;
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(!$this->bonus) return true;
            $this->bonus->price = $this->card0->bonusPrice($this->price);
            $this->bonus->save();
            return true;
        } else {
            return false;
        }
    }
}
