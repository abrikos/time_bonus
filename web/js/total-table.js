function savePrepayment(obj) {
	var input = $(obj)
	var value = input.val()
	if (value && $.isNumeric(value)) {
		input.parent().removeClass('has-error')
		$.get('site/update-master-prepayment', {'id': input.data('master-id'), 'value': value}, function(result) {
			if (result) {
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



function savePenalty(obj) {
	var input = $(obj)
	var value = input.val()
	if (value && $.isNumeric(value)) {
		input.parent().removeClass('has-error')
		$.get('site/update-master-penalty', {'id': input.data('master-id'), 'value': value}, function(result) {
			if (result) {
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


function saveBonus(obj) {
	var input = $(obj)
	var value = input.val()
	if (value && $.isNumeric(value)) {
		input.parent().removeClass('has-error')
		$.get('site/update-master-bonus', {'id': input.data('master-id'), 'value': value}, function(result) {
			if (result) {
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
