
function redrawBonus(haircut) {
    return ;
    if(haircut.bonus_id){
        $('#haircut-price-'+haircut.id).addClass('hasBonus').html( haircut.price*1>0 ? haircut.price:'Бонус'  );
    }else{
        $('#haircut-price-'+haircut.id).removeClass('hasBonus');
    }
}


function haircutNoteChange(obj) {
    var input = $(obj);
    var id = input.data('id');
    $.get('/haircut/note-change',{id:id,note:input.val()},function (result) {
        console.log(result);
    })
};



function haircutDialog(id) {
    var modal = $('#haircut-modal');
    modal.modal();
    $.get('site/get-haircut', {'id': id}, function(result) {
        var modalbody = modal.find('.modal-body');
        modalbody.empty()
            .append(
                $('<input>', {class: 'form-control', placeholder: 'Пометки', name: 'note', onchange:'haircutNoteChange(this)', 'data-id':id}).val(result.note)
            )
            .append(tmpl("bonus-form", result))
            .append('<br>')
            .append(
                $('<table>', {class: 'table-condensed', id:'table-materials'}).append(
                    $('<tr>')
                        .append($('<th>', {width: '70%'}).text('Материал'))
                        .append($('<th>', {width: '20%'}).text('Цена'))
                        .append($('<th>', {width: '10%'}).text(''))
                )
            );

        $.each(result.materials, function(i, material) {
            $('<tr>')
                .data('material-id', material.id)
                .append($('<td>').text(material.name))
                .append($('<td>').text(material.price))
                .append($('<td>')
                    .append(
                        $('<i>', {class: 'btn btn-warning btn-xs glyphicon glyphicon-remove'})
                            .click(removeMaterial)
                    )
                )
                .appendTo($('#table-materials'))
        })
        $('<tr class="new-material">')
            .data('haircut-id', id)
            .append($('<td>', {style: 'padding-right: 10px'}).append(
                $('<input>', {class: 'form-control', name: 'name'}))
                .focusin(function(e) { $(e.target).parent().removeClass('has-error') })
                .keyup(function(e) { $(e.target).parent().removeClass('has-error'); if (e.keyCode == 13) { addMaterial() } })
            )
            .append($('<td>', {style: 'padding-right: 10px'}).append(
                $('<input>', {class: 'form-control', name: 'price'}))
                .focusin(function(e) { $(e.target).parent().removeClass('has-error') })
                .keyup(function(e) { $(e.target).parent().removeClass('has-error'); if (e.keyCode == 13) { addMaterial() } })
            )
            .append($('<td>')
                .append($('<i>', {class: 'btn btn-primary btn-xs glyphicon glyphicon-plus'}))
                .click(addMaterial)
            )
            .appendTo($('#table-materials'))
    }, 'JSON')
}




function restoreHaircutPriceInput(obj,e) {
     if (e.keyCode == 13) {
         var input = $(obj);
         input.blur();
     }
}

function saveHaircutPrice(obj) {
    var input = $(obj);
    input.removeClass("hpi-focused").blur();
    $.getJSON('/haircut/change-price',{id:input.data('id'),price:input.val()},function (json) {
        redrawTotalTable();
        if(json.error){
            //alert(json.error);
        }
    })


}



function haircutRemove(id) {
    $.get('/haircut/delete',{id:id},function () {
        $('#container-haircut-'+id).fadeOut();
        redrawTotalTable();
    })
}

function bonusAdd(id) {
    $.getJSON('/haircut/bonus-add',{id:id},function (json) {
        haircutFillBonusTable(json,id);
    })
}

function haircutFillBonusTable(haircut,id) {
    $('#control-no-bonus').fadeOut();
    $('#card-info').fadeIn();
    $('#dialog-haircut-bonus').html(haircut.bonus);
    $('#dialog-card-bonus').html(haircut.card_bonus);
    $('#dialog-card-number').html(haircut.card_number);
    if(haircut.discount) {
        $('#haircut-discount-' + id).text(haircut.discount);
        $('#dialog-haircut-payment').text(haircut.price - haircut.discount);
        $('#dialog-haircut-discount').text(haircut.discount);
    }
    $('#haircut-price-' + id).addClass('hasBonus');
    redrawTotalTable();
}

function discountAdd(id) {
    var sum=$('#card-bonus-discount').val();
    $.getJSON('/haircut/discount-add',{id:id,reduce:sum},function (json) {
        $('#card-bonus-sum').text(json.c_bonus);
        $('#card-status').html('<div class="text-'+json.status.class+'">'+json.status.message+'</div>');
        if(json.status.class=='success'){
            haircutFillBonusTable(json.bonusData,id);
        }
    })
}

function cardChange(id,cardnum) {
    $.getJSON('/haircut/card-change',{cardnum:cardnum,id:id},function (json) {
        if(json) {
            $('#card-bonus-input').html(json.bonus);
            $('#card-discount-max').html(json.bonus);
            $('#bonus-add').fadeIn();
            if(json.bonus>100){
                $('#discount-add').fadeIn();
            }else {
                $('#discount-add').fadeOut();
            }
        }
    })
}