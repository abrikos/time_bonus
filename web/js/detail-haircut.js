$(function () {


$('#haircut-modal').on('show.bs.modal', function(e) {
	var id = $(e.relatedTarget).siblings('.price').data('haircut-id')
	var modal = $(this)
	modal.data('haircut-id', id)
	$.get('/site/get-haircut', {'id': id}, function(result) {
		modal.find('.modal-body').empty()
			.append(
				$('<div>', {class: 'form-control'}).text(result.note)
			)
			.append('<br>')
			.append(
				$('<table>', {class: 'table-condensed'}).append(
					$('<tr>')
						.append($('<th>', {width: '70%'}).text('Материал'))
						.append($('<th>', {width: '20%'}).text('Цена'))
						.append($('<th>', {width: '10%'}).text(''))
				)
			)
		$.each(result.materials, function(i, material) {
			$('<tr>')
				.data('material-id', material.id)
				.append($('<td>').text(material.name))
				.append($('<td>').text(material.price))
				.appendTo(modal.find('table'))
		})
	}, 'JSON')
})

$('#haircut-modal').on('hidden.bs.modal', function() {
	$(this).find('.modal-body').html(
		$('<div>', {style: 'text-align: center'}).html(
			$('<img>', {src: '/images/ajax-loading.gif', alt: 'Загрузка...', width: '50px'})
		)
	)
})
});