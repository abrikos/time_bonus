<?php

namespace app\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\Shift;
use app\models\Haircut;
use app\models\Material;
use app\models\Master;
use app\models\Administrator;
use app\models\MasterStack;
use app\models\ReserveStack;
use app\models\Expense;
use app\models\Income;
use app\models\Sale;

class SiteController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $shift = Shift::getCurrent();
        $masters = $shift->masters;
        usort($masters, function($a, $b) {
            return ($a->time . $a->id) > ($b->time . $b->id);
        });
        $masterStack = MasterStack::get();
        $reserveStack = ReserveStack::get();
        $haircutTable = $shift->haircutTable;
        $totalTable = $shift->totalTable;
        return $this->render('index', [
            'shift' => $shift,
            'masterStack' => $masterStack,
            'reserveStack' => $reserveStack,
            'masters' => $masters,
            'haircutTable' => $haircutTable,
            'totalTable' => $totalTable
        ]);
    }

    public function actionGetTotalTable()
    {
        $shift = Shift::getCurrent();
        return json_encode($shift->totalTable);
    }

    public function actionCloseShift()
    {
        $shift = Shift::getCurrent();
        if ($shift->administrator_id) {
            $shift->close();
        } else {
            Yii::$app->session->setFlash('shiftHasNoAdmin', 'Перед закрытием смены нужно выбрать администратора!');
        }
        return $this->redirect(['/site']);
    }

    public function actionMasterArrive($id)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->addMaster($id);
        }
        return false;
    }

    public function actionMasterLeave($id)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->removeMaster($id);
        }
        return false;
    }

    public function actionAddMasterToStack($id)
    {
        if ($master = Master::findOne(['id' => $id])) {
            return MasterStack::add($id);
        }
        return false;
    }

    public function actionRemoveMasterFromStack($id)
    {
        if ($item = MasterStack::findOne(['master_id' => $id, 'removed' => null])) {
            return $item->remove();
        }
        return false;
    }

    public function actionSelectAdmin($id = null)
    {
        if ($id && $admin = Administrator::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            $shift->administrator_id = $admin->id;
            $shift->administrator_arrive = time();
            $shift->save();
        }
        $this->redirect(['/']);
    }

    public function actionAddReserve($name, $phone, $time)
    {
        return ReserveStack::add($name, $phone, $time);
    }

    public function actionRemoveReserve($id)
    {
        if ($item = ReserveStack::findOne($id)) {
            return $item->remove();
        }
        return false;
    }

    public function actionGetReserve()
    {
        $items = [];
        $reserveStack = ReserveStack::get();
        foreach ($reserveStack as $reserve) {
            $items[] = [
                'id' => $reserve->id,
                'time' => $reserve->prettyTime,
                'text' => $reserve->text
            ];
        }
        return json_encode($items);
    }

    public function actionAddExpense($name, $amount)
    {
        $shift = Shift::getCurrent();
        return $shift->addExpense($name, $amount);
    }

    public function actionRemoveExpense($id)
    {
        if ($expense = Expense::findOne($id)) {
            return $expense->delete();
        }
        return false;
    }

    public function actionGetExpense()
    {
        $shift = Shift::getCurrent();
        $items = [];
        foreach ($shift->expenses as $expense) {
            $items[] = [
                'id' => $expense->id,
                'name' => $expense->name,
                'amount' => $expense->amount
            ];
        }
        return json_encode($items);
    }

    public function actionAddIncome($name, $amount)
    {
        $shift = Shift::getCurrent();
        return $shift->addIncome($name, $amount);
    }

    public function actionRemoveIncome($id)
    {
        if ($income = Income::findOne($id)) {
            return $income->delete();
        }
        return false;
    }

    public function actionGetIncome()
    {
        $shift = Shift::getCurrent();
        $items = [];
        foreach ($shift->incomes as $income) {
            $items[] = [
                'id' => $income->id,
                'name' => $income->name,
                'amount' => $income->amount
            ];
        }
        return json_encode($items);
    }  

    public function actionAddSale($name, $amount)
    {
        $shift = Shift::getCurrent();
        return $shift->addSale($name, $amount);
    }

    public function actionRemoveSale($id)
    {
        if ($sale = Sale::findOne($id)) {
            return $sale->delete();
        }
        return false;
    }

    public function actionGetSale()
    {
        $shift = Shift::getCurrent();
        $items = [];
        foreach ($shift->sales as $sale) {
            $items[] = [
                'id' => $sale->id,
                'name' => $sale->name,
                'amount' => $sale->amount
            ];
        }
        return json_encode($items);
    }

    public function actionGetHaircut($id)
    {
        $haircut = Haircut::findOne($id);
        $result['materials'] = [];
        foreach ($haircut->materials as $material) {
            $result['materials'][] =  $material->attributes;
        }
        $result['id'] = $id;
        $result['note'] = $haircut->note;
        $result['haircut_bonus'] = $haircut->bonusData();
        $result['form_hide'] = $haircut->bonus || $haircut->discount ;
        return json_encode($result);
    }

    public function actionRemoveMaterial($id)
    {
        $material = Material::findOne(['id' => $id]);
        return $material->delete();
    }

    public function actionAddMaterial($id, $name, $price)
    {
        $haircut = Haircut::findOne(['id' => $id]);
        return json_encode($haircut->addMaterial($name, $price));
    }

    public function actionAddHaircut($masterID)
    {
        $shift = Shift::getCurrent();
        $haircut = new Haircut();
        $haircut->shift_id = $shift->id;
        $haircut->master_id = $masterID;
        $haircut->price = 0;
        $haircut->time = time();
        return ($haircut->save() ? json_encode(['haircut'=>$haircut->attributes,'input'=>$haircut->drawInputCell()]) : false);
    }

    public function actionRemoveHaircut($id)
    {
        $haircut = Haircut::findOne($id);
        $haircut->delete();
        return Json::encode(['status'=>'success','message'=>'Удалено']);

    }

    public function actionUpdateHaircut($id, $price = null, $note = null)
    {
        if ($haircut = Haircut::findOne($id)) {
            $haircut->price = ( $price ? $price : $haircut->price );
            $haircut->note = ( $note !== null ? $note : $haircut->note );
            return ($haircut->save() ? json_encode($haircut->attributes) : false);
        }
    }

    public function actionUpdateMasterPrepayment($id, $value)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->updatePrepayment($id, $value);
        }
        return false;
    }

    public function actionUpdateMasterPenalty($id, $value)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->updatePenalty($id, $value);
        }
        return false;
    }

    public function actionUpdateMasterBonus($id, $value)
    {
        if ($master = Master::findOne(['id' => $id])) {
            $shift = Shift::getCurrent();
            return $shift->updateBonus($id, $value);
        }
        return false;
    }

    public function actionGetFinalCash()
    {
        $shift = Shift::getCurrent();
        return $shift->finalCash;
    }

    public function actionGetClientCount()
    {
        $shift = Shift::getCurrent();
        return $shift->clientCount;
    }

    public function actionGetAdministratorPayment()
    {
        $shift = Shift::getCurrent();
        return $shift->administratorPayment;
    }
}
