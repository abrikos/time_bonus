<?php
use yii\helpers\Html;
use app\models\Master;
use app\models\Administrator;
use app\assets\ArchiveAsset;

/* @var $this yii\web\View */

ArchiveAsset::register($this);
$this->title = 'Архив';
?>
<div class="archive-view">
	<div class="col-sm-9">
		<div class="panel panel-primary">
			<div class="panel-heading">
				Смена №<?= $shift->id ?> от <?= $shift->startedAt ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Касса: <span class="final-cash"><?= $shift->finalCash ?></span> руб. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Клиенты: <span class="client-count"><?= $shift->clientCount ?></span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Администратору: <span class="administrator-payment"><?= $shift->administratorPayment ?></span>
			</div>
			<table id="shift-table" class="panel-body table table-condensed table-bordered">
				<thead><tr>
					<th width="130px" style="max-width: 130px">
						<span class="text-uppercase" title="<?= $shift->administratorArriveShort ?>"> <?= $shift->administrator->name ?></span>
					</th>
					<?php foreach ($masters as $master): ?>
						<th width="90px" style="max-width: 90px" title="<?= $master->name ?> - <?= $master->getArriveTime($shift->id) ?>/<?= $master->getLeaveTime($shift->id) ?>" data-master-id="<?= $master->id; ?>">
							<?= $master->name ?> - <?= $master->getArriveTime($shift->id) ?>/<?= $master->getLeaveTime($shift->id) ?>
						</th>
					<?php endforeach ?>
					<th></th>
				</tr></thead>
				<tbody>
					<?php foreach ($haircutTable as $i => $row): ?>
						<tr>
							<td><?= $i + 1 ?></td>
							<?php foreach ($row as $value) {
								echo "<td>$value</td>";
							} ?>
							<td></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<table class="table table-condensed table-bordered table-total">
				<thead>
					<tr>
						<td width="130px" style="max-width: 130px; padding: 0px;"></td>
						<?php for ($i = 0; $i < count($masters); $i++): ?>
							<td width="90px" style="max-width: 90px; padding: 0px;"></td>
						<?php endfor; ?>
						<td style="padding: 0px;"></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($totalTable as $id => $row): ?>
						<tr>
							<?php foreach ($row as $value) {
								echo "<td>$value</td>";
							} ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="col-sm-3">
		<div class="panel panel-primary">
			<div class="panel-heading">
				Продажи
			</div>
			<table class="panel-body table table-condensed sale-table">
				<tbody>
					<?php foreach ($shift->sales as $sale): ?>
						<tr data-sale-id="<?= $sale->id ?>">
							<td><?= $sale->name ?></td>
							<td width="40px"><?= $sale->amount ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="panel panel-primary">
			<div class="panel-heading">
				Доходы
			</div>
			<table class="panel-body table table-condensed income-table">
				<tbody>
					<?php foreach ($shift->incomes as $income): ?>
						<tr data-income-id="<?= $income->id ?>">
							<td><?= $income->name ?></td>
							<td width="40px"><?= $income->amount ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="panel panel-primary">
			<div class="panel-heading">
				Расходы
			</div>
			<table class="panel-body table table-condensed expense-table">
				<tbody>
					<?php foreach ($shift->expenses as $expense): ?>
						<tr data-expense-id="<?= $expense->id ?>">
							<td><?= $expense->name ?></td>
							<td width="40px"><?= $expense->amount ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		
	</div>
</div>

<!-- Modal forms -->

<div class="modal fade" tabindex="-1" role="dialog" id="haircut-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Детальный просмотр</h4>
			</div>
			<div class="modal-body">
				<div style="text-align: center">
					<img src="/images/ajax-loading.gif" alt="Загрузка..." width="50px">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>