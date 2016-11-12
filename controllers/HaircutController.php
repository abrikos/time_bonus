<?php

namespace app\controllers;

use app\models\Bonus;
use app\models\Card;
use app\models\Discount;
use app\models\Haircut;
use app\models\Shift;
use Yii;
use yii\helpers\Json;

class HaircutController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $shift = Shift::getCurrent();
        $mas = $shift->masters;
        return $this->render('/update/index');
    }

    public function actionCardChange($id,$cardnum)
    {
        $haircut = Haircut::findOne($id);
        $card = Card::findOne(['number'=>$cardnum]);
        if(!$card) return false;
        if(!$haircut) return false;
        $haircut->card = $card->id;
        $haircut->save();
        return Json::encode(['bonus'=>$card->bonus]);
    }

    public function actionBonusAdd($id)
    {
        $haircut = Haircut::findOne($id);
        if(!$haircut) return false;
        if($haircut->bonus) return false;
        $card = $haircut->card0;
        if(!$card) return false;
        $bonus = new Bonus();
        $bonus->card = $card->id;
        $bonus->haircut = $haircut->id;
        $bonus->date = time();
        $bonus->price = $card->bonusPrice($haircut->price);
        if(!$bonus->save()) throw new HttpException(500,VarDumper::export($bonus->errors));
        return Json::encode($haircut->bonusData());
    }

    public function actionDiscountAdd($id,$reduce)
    {
        $haircut = Haircut::findOne($id);
        if(!$haircut) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Нет такой оплаты']]);
        if($haircut->bonus) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Оплата уже присуммирвана к бонусам']]);
        $card = $haircut->card0;
        if(!$card) return Json::encode(['status'=>['class'=>'danger', 'message'=>'Ошибочный номер карты']]);

        if($reduce>$haircut->price)
            return Json::encode(['status'=>['class'=>'warning', 'message'=>"Снимаемый бонус больше чем стоимость стрижки ($reduce>{$haircut->price})"]]);
        if($reduce > $card->bonus)
             return Json::encode(['status'=>['class'=>'warning', 'message'=>"Недостаточно бонусов на карте"]]);
        if($reduce < 100)
             return Json::encode(['status'=>['class'=>'warning', 'message'=>"Снимается только более 100 бонусов"]]);
        //$haircut->old_price = $haircut->price;
        $discount = new Discount();
        $discount->date = time();
        $discount->card = $card->id;
        $discount->haircut = $haircut->id;
        $discount->price = $reduce;
        $discount->save();
        $return = [
            'status' => ['class'=>'success','message'=>$reduce . ' бонусов переведено в оплату '  ],
            'bonusData'=>$haircut->bonusData()
        ];
        return Json::encode($return);
        return Json::encode(['status'=>$status, 'haircutBonus'=>$card->bonus, 'card'=>$card, 'haircut'=>$haircut]);
    }

    public function actionChangePrice($id, $price){
        $haircut = Haircut::findOne($id);
        if($haircut->bonus && 0){
            return Json::encode(['error'=>'Назначен бонус. Редактирование не доступно. Можно только удалить','haircut'=>$haircut->attributes]);
        }
        if($haircut->discount && 0){
            return Json::encode(['error'=>'Бонус вычтен. Редактирование не доступно. Можно только удалить', 'haircut'=>$haircut->attributes]);
        }
        $haircut->price = $price;
        $haircut->time = time();
        return ($haircut->save() ? Json::encode(['haircut'=>$haircut->attributes]) : false);
    }


    public function actionNoteChange($id,$note)
    {
        $haircut = Haircut::findOne($id);
        if(!$haircut) return false;
        $haircut->note = $note;
        $haircut->save();
    }

    public function actionDelete($id)
    {
        return false;
        Haircut::findOne($id)->delete();
    }
}
