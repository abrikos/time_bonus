$(function () {
	$('#master-arrive').click(function() {
		var master = $(this).parent().siblings('.modal-body').find('[name=master]')
		$.get('site/master-arrive',
			{'id': master.val()},
			function(result) {
				if (result) {
					$('#master-modal').modal('hide')
					masterArrive(master.val(), master.find('option:selected').text())
				}
			})
	});
	$('.master-leave').click(masterLeave);
	$('.add-haircut').click(addHaircut)
	$('.remove-haircut').click(removeHaircut)
	$('.editable').click(toggleEditable)

	$('#haircut-modal').on('shown.bs.modal', function() {
		$('.new-material').find('input[name=name]').focus()
	})

	redrawTotalTable();
})

function redrawTotalTable() {
	$.get('/site/get-total-table', null, function(result) {
		var body = $('<tbody id="total-table">');

		$.each(result.rowname,function (rowid,row) {
			body.append($('<tr>',{id:'total-row-'+rowid,class:'total-row'}).append($('<td>').text(row)));
		});
		$('#total-table').replaceWith(body);
		$.each(result.data,function (colid,col) {
			$.each(col,function (rowid,row) {
				$('#total-row-'+rowid).append($('<td>',{class:'total-cell-'+rowid+'-'+colid + ' total-column-'+colid}).html(row));
			})
		});


		$('.price-input').focusin(function () {
			var input = $(this);
			input.addClass("hpi-focused").select();
		}).focusout(function () {
			var input = $(this);
			input.removeClass("hpi-focused");
		})

	}, 'JSON')
	$.get('/site/get-final-cash', null, function(result) {
		$('.final-cash').text(result)
	})
	$.get('/site/get-client-count', null, function(result) {
		$('.client-count').text(result)
	})
	$.get('/site/get-administrator-payment', null, function(result) {
		$('.administrator-payment').text(result)
	});
}


function masterArrive(id, name) {
	var table = $('#shift-table')
	//TO-DO: add master column without reloading
	window.location.reload()
}



function masterLeave() {
	if (confirm('Вы уверены, что хотите произвести расчет?')) {
		var master = $(this).closest('table').find('th').eq($(this).closest('td').index())
		$.get('site/master-leave', {'id': master.data('master-id')}, function(result) {
			if (result) {
				window.location.reload()
			}
		})
	}
}



function addHaircut() {
	if ($('.dismissable').length == 0) {
		var isLast = $(this).closest('tr').is(':last-child')
		var td = $(this).closest('td')
		var masterID = $(this).closest('table').find('th').eq(td.index()).data('master-id')
		var table = $(this).closest('table')
		$.get('site/add-haircut', {'masterID': masterID}, function(json) {
			var haircut = json.haircut;

			if (haircut) {
				var input = json.input;
				if (isLast) {
					var tr = td.closest('tr').clone()
					$.each(tr.find('td'), function(i, ui) {
						switch (i) {
							case 0:
								var id = parseInt(td.closest('tr').find('td:first-child').text()) + 1;
								$(ui).text(id)
								break;
							case td.index():
								$(ui).html(td.html())
								break;
							default:
								$(ui).text('')
								break;
						}
					})
					var inp = td.html(input);
					table.find('tbody').append(tr)
				} else  {
					td.closest('tr').next().find('td').eq(td.index()).html(td.html())
					var inp = td.html(input)
				}
				inp.find('input').addClass("hpi-focused")
				$('.add-haircut').off('click').bind('click', addHaircut)
				$('.remove-haircut').off('click').bind('click', removeHaircut)
				$('.master-leave').off('click').bind('click', masterLeave)
				$('.dismissable').off('click').bind('click', toggleDismissable)
				$('#add-master-to-stack').parent().siblings('td:last').children('select').val(masterID)
				$('#add-master-to-stack').click()
				$('#haircut-price-'+haircut.id).focus().select()
				redrawTotalTable()
			}
		}, 'JSON')
	}
}



function removeHaircut() {
	var isFirst = $(this).closest('tr').is(':first-child')
	if (!isFirst) {
		var isLast = $(this).closest('tr').is(':last-child')
		var controls = $(this).closest('td')
		var td = $(this).closest('tr').prev().find('td').eq(controls.index());
		console.log(td)
		var id = td.find('.price-input').data('id')
		$.getJSON('/site/remove-haircut', {'id': id}, function(result) {
			if (result.status == 'success' ) {
				td.html(controls.html())
				controls.empty()
				$('.add-haircut').off('click').bind('click', addHaircut)
				$('.remove-haircut').off('click').bind('click', removeHaircut)
				$('.master-leave').off('click').bind('click', masterLeave)
				redrawTotalTable()
			}else{
				alert(result.message);
			}
		})
	}
}


function toggleEditable() {
	if ($('.dismissable').length == 0) {
		$(this).parent().html(
			$('<input>', {class: 'form-control input-sm dismissable'})
				.data('haircut-id', $(this).data('haircut-id'))
				.val($(this).text())
				.focusout(toggleDismissable)
				.keyup(function(e) { if (e.keyCode == 13) { $(this).blur() } })
		)
		$('input.dismissable').focus().select()
	}
}

function toggleDismissable() {
	var input = $(this)
	var value = input.val()
	if (value && $.isNumeric(value)) {
		input.parent().removeClass('has-error')
		$.get('site/update-haircut', {'id': input.data('haircut-id'), 'price': value}, function(haircut) {
			if (haircut) {
				var date = new Date(haircut.time * 1000)
				var shortTime = date.getHours() + ':' + date.getMinutes()
				var price = $('<span>', {class: 'editable', title: shortTime, style: 'float: left'})
					.data('haircut-id', haircut.id)
					.text(haircut.price)
					.click(toggleEditable)
				input.replaceWith(price)
				$('.haircut-edit-template').clone(true, true)
					.removeClass('haircut-edit-template')
					.insertAfter(price)
				redrawTotalTable()
			} else {
				input.parent().addClass('has-error')
				input.focus()
			}
		}, 'JSON')
	} else {
		input.parent().addClass('has-error')
		input.focus()
	}
}



function removeMaterial() {
	var row = $(this).closest('tr')
	$.get('/site/remove-material', {'id': row.data('material-id')}, function() {
		redrawTotalTable()
		row.fadeOut('fast', function() {
			$(this).remove()
		})
	})
}

function addMaterial() {
	var form = $('.new-material').closest('tr')
	var id = form.data('haircut-id')
	var name = form.find('[name=name]')
	var price = form.find('[name=price]')
	var validate = true
	if (!name.val()) {
		name.parent().addClass('has-error')
		validate = false
	}
	if (!price.val()) {
		price.parent().addClass('has-error')
		validate = false
	}
	if (validate) {
		$.get('site/add-material',
			{
				'id': id,
				'name': name.val(),
				'price': price.val(),
			},
			function(material) {
				redrawTotalTable()
				name.val('')
				price.val('')
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
					.insertBefore(form)
				$('.new-material').find('input[name=name]').focus()
			}, 'JSON')
	}
}
