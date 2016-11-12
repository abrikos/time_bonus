<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shift".
 *
 * @property integer $id
 * @property string $started_at
 * @property string $finished_at
 */
class Shift extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shift';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['administrator_id'], 'integer'],
            [['started_at', 'finished_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'administratorArrive' => 'Начало смены',
            'finishedAt' => 'Конец смены',
            'total' => 'Выручка',
            'final_cash' => 'Касса',
            'clientCount' => 'Клиенты'
        ];
    }

    public function getAdministrator()
    {
        return $this->hasOne(Administrator::className(), ['id' => 'administrator_id']);
    }

    public function getMasters()
    {
        return $this->hasMany(Master::className(), ['id' => 'master_id'])
            ->viaTable(MasterToShift::tableName(), ['shift_id' => 'id'], function ($query) {
                $query->orderBy(['arrive_time' => SORT_DESC]);
            });
    }

    public function getHaircuts()
    {
        return $this->hasMany(Haircut::className(), ['shift_id' => 'id']);
    }

    public function getExpenses()
    {
        return $this->hasMany(Expense::className(), ['shift_id' => 'id']);
    }

    public function getIncomes()
    {
        return $this->hasMany(Income::className(), ['shift_id' => 'id']);
    }

    public function getSales()
    {
        return $this->hasMany(Sale::className(), ['shift_id' => 'id']);
    }

    public static function getCurrent()
    {
        $current = static::findOne(['finished_at' => null]);
        if (!$current) {
            $prev = Shift::find()->orderBy(['id' => SORT_DESC])->one();
            $shift = new Shift();
            $shift->started_at = time();
            $shift->cash = ( $prev ? $prev->final_cash : 0 );
            $shift->save();
            $current = $shift;
        }
        return $current;
    }

    public function getStartedAt()
    {
        return strftime('%H:%M %d %B %Y', $this->started_at);
    }

    public function getFinishedAt()
    {
        return strftime('%H:%M %d %B %Y', $this->finished_at);
    }

    public function getAdministratorArrive()
    {
        return strftime('%H:%M %d %B %Y', $this->administrator_arrive);
    }

    public function getAdministratorArriveShort()
    {
        return date('H:i', $this->administrator_arrive);
    }

    public function getTotal()
    {
        $table = $this->getTotalTable();
        return $table['data']['last']['total'];
        $sum = 0;
        foreach ($table as $cols){
            $sum += $cols['total'];
        }
        return $sum;
        return array_sum(end($table));
    }

    public function getSalesAmount()
    {
        $sum = 0;
        foreach ($this->sales as $sale) {
            $sum += $sale->amount;
        }
        return $sum;
    }

    public function getIncomeAmount()
    {
        $sum = 0;
        foreach ($this->incomes as $income) {
            $sum += $income->amount;
        }
        return $sum;
    }

    public function getExpenseAmount()
    {
        $sum = 0;
        foreach ($this->expenses as $expense) {
            $sum += $expense->amount;
        }
        return $sum;
    }

    public function getClientCount()
    {
        return count($this->haircuts);
    }

    public function getFinalCash()
    {
        return $this->cash + $this->total + $this->salesAmount + $this->incomeAmount - $this->administratorPayment - $this->expenseAmount - $this->bonuses + $this->discounts;
    }

    public function getDiscounts()
    {
        $table = $this->getTotalTable();
	    //print '<plaintext>';print_r($table);exit;
        return $table['data']['last']['discount'];
    }

    public function getAdministratorPayment()
    {
        $clientBonus = floor(($this->clientCount - 80) / 10) * 200;
        $clientBonus = ( $clientBonus < 0 ? 0 : $clientBonus);
        $sales = ( $this->total >= 9000 ? ($this->total + $this->salesAmount) * 0.05 : 0 );
        $payment = round(1200 + $sales + $clientBonus);
        $round = $payment % 100;
        if ($round <= 25) {
            $payment -= $round;
        } else if ($round <= 50) {
            $payment += 50 - $round;
        } else if ($round <= 75) {
            $payment -= $round - 50;
        } else {
            $payment += 100 - $round;
        }
        return $payment;
    }

    public function close()
    {
        MasterStack::clear();
        $time = time();
        foreach ($this->masters as $master) {
            if (!$master->shift->leave_time) {
                $this->removeMaster($master->id);
            }
        }
        $this->final_cash = $this->finalCash;
        $this->finished_at = $time;
        return $this->save();
    }

    public function addExpense($name, $amount)
    {
        $expense = new Expense();
        $expense->shift_id = $this->id;
        $expense->name = $name;
        $expense->amount = $amount;
        return $expense->save();
    }

    public function addIncome($name, $amount)
    {
        $income = new Income();
        $income->shift_id = $this->id;
        $income->name = $name;
        $income->amount = $amount;
        return $income->save();
    }

    public function addSale($name, $amount)
    {
        $sale = new Sale();
        $sale->shift_id = $this->id;
        $sale->name = $name;
        $sale->amount = $amount;
        return $sale->save();
    }

    public function addMaster($id)
    {
        $assignment = new MasterToShift();
        $assignment->shift_id = $this->id;
        $assignment->master_id = $id;
        $assignment->arrive_time = time();
        return $assignment->save();
    }

    public function removeMaster($id)
    {
        if ($assignment = MasterToShift::findOne(['shift_id' => $this->id, 'master_id' => $id])) {
            $assignment->leave_time = time();
            return $assignment->save();
        }
        return false;
    }

    public function updatePrepayment($id, $value)
    {
        if ($assignment = MasterToShift::findOne(['shift_id' => $this->id, 'master_id' => $id])) {
            $assignment->prepayment = $value;
            return $assignment->save();
        }
        return false;
    }

    public function updatePenalty($id, $value)
    {
        if ($assignment = MasterToShift::findOne(['shift_id' => $this->id, 'master_id' => $id])) {
            $assignment->penalty = $value;
            return $assignment->save();
        }
        return false;
    }

    public function updateBonus($id, $value)
    {
        if ($assignment = MasterToShift::findOne(['shift_id' => $this->id, 'master_id' => $id])) {
            $assignment->bonus = $value;
            return $assignment->save();
        }
        return false;
    }

    public function getHaircutTable($archive = false)
    {
        $table = [];
        $masters = $this->masters;
        foreach ($masters as $master) {
            $time = $master->getTime($this->id);
            $table["{$time}{$master->id}"] = [];
            foreach ($master->getHaircuts($this->id)->all() as $haircut) {
                if ($archive) {
                    $table["{$time}{$master->id}"][] =
                        "<span class='price' data-haircut-id='{$haircut->id}' title='{$haircut->shortTime}' style='float: left'>{$haircut->price}</span>
                        <span class='btn btn-xs btn-default glyphicon glyphicon-eye-open' style='float: right' data-toggle='modal' data-target='#haircut-modal'></span>";
                } else {
                    $table["{$time}{$master->id}"][] =$haircut->drawInputCell();
                }
            }
        }
        if (!$archive) {
            foreach ($table as $id => $row) {
                $table[$id][] = "
                    <button class='btn btn-xs btn-primary add-haircut'><span class='glyphicon glyphicon-scissors' title='Стрижка'></span></button>
                    <button class='btn btn-xs btn-danger remove-haircut'><span class='glyphicon glyphicon-trash' title='Удалить стрижку'></span></button>
                    <button class='btn btn-xs btn-warning master-leave'><span class='glyphicon glyphicon-rub' title='Расчет'></span></button>
                ";
            }
        }

        if ($table) {
            ksort($table);
            $table = call_user_func_array(
                'array_map',
                [-1 => null] + $table
            );
            foreach ($table as $id => $row) {
                if (!is_array($row)) {
                    $table[$id] = [0 => $row];
                }
            }
        }
        return $table;
    }

    public function getBonuses()
    {
        $bonus = 0;
        foreach ($this->masters as $master) {
            $bonus += $master->getBonus($this->id);
        }
        return $bonus;
    }

    public function getTotalTable()
    {
        $table = [];

        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $sum = 0;
            foreach ($master->getHaircuts($this->id)->all() as $haircut) {
                $sum += $haircut->price;
            }
            $table[$id]['sum'] = $sum;
        }

        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $sum = 0;
            foreach ($master->getMaterials($this->id)->all() as $material) {
                $sum += $material->price;
            }
            $table[$id]['material'] = $sum;
        }

        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $table[$id]['half'] = ($table[$id]['sum'] - $table[$id]['material']) / 2;
        }

        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $prepayment = $master->getPrepayment($this->id);
            $table[$id]['prepayment'] = "<input id='prepayment-{$master->id}' value='{$prepayment}' data-master-id='{$master->id}' class='price-input' onchange='savePrepayment(this)'/>";
        }
        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $penalty = $master->getPenalty($this->id);
            $table[$id]['penalty'] = "<input id='penalty-{$master->id}' value='{$penalty}' data-master-id='{$master->id}' class='price-input' onchange='savePenalty(this)'/>";
            //$table[$id]['penalty'] = "<span class='penalty'>{$penalty}</span>";
        }

        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $bonus = $master->getBonus($this->id);
            $table[$id]['bonus'] = "<input id='bonus-{$master->id}' value='{$bonus}' data-master-id='{$master->id}' class='price-input' onchange='saveBonus(this)'/>";;
        }

        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $table[$id]['salary'] = $table[$id]['half'] - $master->getPenalty($this->id) - $master->getPrepayment($this->id) + $master->getBonus($this->id);
        }

        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $table[$id]['cash'] = $table[$id]['material'] + $master->getPenalty($this->id);
           
        }



        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $sum2 = 0;
            foreach ($master->haircuts as $haircut) {
                $sum2 -= $haircut->discount?$haircut->discount->price:0;
            }
            $table[$id]['discount'] = $sum2;
        }



        foreach ($this->masters as $master) {
            $time = $master->getTime($this->id);
            $id = "{$time}{$master->id}";
            $table[$id]['total'] = $table[$id]['half'] + $table[$id]['cash'];
        }



        $totalSum = 0;
        $totalDiscount = 0;
        foreach ($table as $row) {
            $totalSum += $row['total'] + $row['discount']*0;
            $totalDiscount += $row['discount'];
        }

	    ksort($table);

        $table2['rowname']['sum'] = 'Сумма';
        $table2['rowname']['material'] = 'Материалы';
        $table2['rowname']['half'] = '1/2';
        $table2['rowname']['prepayment'] = 'Аванс';
        $table2['rowname']['penalty'] = 'Вычеты';
        $table2['rowname']['bonus'] = 'Премия';
        $table2['rowname']['salary'] = 'Мастеру';
        $table2['rowname']['cash'] = 'Касса';
        $table2['rowname']['discount'] = 'Бонусы';
        $table2['rowname']['total'] = 'Итого';



        $table['last'] = [
            'sum'=> '',
            'material'=> '',
            'half'=> '',
            'prepayment'=> '',
            'penalty'=> '',
            'bonus'=> '',
            'salary'=> '',
            'cash'=> '',
            'discount'=> $totalDiscount,
            'total'=> $totalSum,
        ];

		$table2['data'] = $table;
        return $table2;
    }
}
