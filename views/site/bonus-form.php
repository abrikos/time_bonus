<div class="row bg-warning {% if (o.form_hide) { %} collapse {% } %} haircut-bonus-section" id="control-no-bonus">
    <input type="hidden" id="haircut-id" value="{%=o.id%}"/>
    <div class="col-md-3">
        <b>Карта:</b>
        <input class="form-control input-sm" placeholder="Введите часть номера карты" id="card-number" list="card_list" value="{%=o.card%}" oninput="cardChange({%=o.id%}, this.value)" />
        Бонусов: <span id="card-bonus-input" >{%=o.haircut_bonus.card_bonus%}</span>
    </div>
    <div class="col-md-3 {% if (o.haircut_bonus.card_number) { %} {% } else { %}collapse{% } %}" id="bonus-add">
        {%=o.haircut_bonus.card_number%}
        <button onclick="bonusAdd({%=o.id%})">Оформить бонус</button>
    </div>
    <div id="discount-add" class="col-md-3 {% if (o.haircut_bonus.card_bonus>100) { %} {% } else { %}collapse{% } %}">

        <input class="form-control input-sm" id="card-bonus-discount" placeholder="Введите сумму бонуса"/>
        <button id="card-submit" onclick="discountAdd({%=o.id%})" class="btn btn-xs btn-success">Вычесть из стоимости</button>
        от 100 до <span id="card-discount-max" >{%=o.haircut_bonus.card_bonus%}</span>
    </div>


</div>
<table id="card-info" class="{% if (!o.form_hide) { %} collapse {% } %} haircut-bonus-section table tab-content table-condensed bg-success">
    <tr>
        <td>Номер карты: </td>
        <td><b id="dialog-card-number">{%=o.haircut_bonus.card_number%}</b></td>
    </tr>

    <tr>
        <td> Бонусов на карте:</td>
        <td><b id="dialog-card-bonus" >{%=o.haircut_bonus.card_bonus%}</b></td>
    </tr>

    <tr>
        <td>Стоимость услуги:</td>
        <td><b id="dialog-haircut-price" >{%=o.haircut_bonus.price%}</b> руб.</td>
    </tr>

    <tr>
        <td>Оплачено бонусами: </td>
        <td><b id="dialog-haircut-discount">{%=o.haircut_bonus.discount%}</b> руб.</td>
    </tr>

    <tr>
        <td>К оплате с учетом бонусов: </td>
        <td><b id="dialog-haircut-payment">{%=o.haircut_bonus.price-o.haircut_bonus.discount%}</b> руб.</td>
    </tr>

    <tr>
        <td>Платеж добавил бонусов на карту</td>
        <td><b id="dialog-haircut-bonus">{%=o.haircut_bonus.bonus%}</b> </td>
    </tr>
</table>

<div id="card-status"></div>