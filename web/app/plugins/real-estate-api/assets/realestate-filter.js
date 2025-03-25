jQuery(document).ready(function ($) {
	function fetchFilteredResults(page = 1) {
		const form = $('#realestate-filter-form');
		const resultsContainer = $('#realestate-results');
		const listContainer = $('#realestate-list');

		const params = form.serializeArray();
		params.push({name: 'paged', value: page});

		$.ajax({
			url       : '/wp-json/realestate/v1/objects',
			method    : 'GET',
			data      : params,
			beforeSend: function () {
				if (page === 1) {
					resultsContainer.html('<div id="realestate-list" class="row"></div>');
				}
				$('.load-more-btn').remove();
			},
			success   : function (response) {
				renderResults(response.items, $('#realestate-list'), response.current_page, response.total_pages);
			},
			error     : function () {
				resultsContainer.html('<p>Помилка при завантаженні результатів.</p>');
			}
		});
	}

	function renderResults(items, container, currentPage, totalPages) {
		if (!items || items.length === 0) {
			container.append('<p>Нічого не знайдено.</p>');
			return;
		}

		items.forEach(item => {
			const imageUrl = item.fields?.image?.url || '';
			const cardHtml = `
				<div class="col-md-6 mb-4">
					<div class="card h-100">
						${imageUrl ? `<img src="${imageUrl}" class="card-img-top" alt="">` : ''}
						<div class="card-body">
							<h5 class="card-title">${item.title}</h5>
							<p><strong>Назва будинку:</strong> ${item.fields.house_name || ''}</p>
							<a href="${item.link}" class="btn btn-sm btn-outline-primary">Переглянути</a>
						</div>
					</div>
				</div>
			`;
			container.append(cardHtml);
		});

		if (currentPage < totalPages) {
			container.after(`
				<div class="pagination text-center mt-3">
					<button class="btn btn-secondary load-more-btn" data-page="${currentPage + 1}">Показати більше</button>
				</div>
			`);
		}
	}

	$('#realestate-filter-form').on('submit', function (e) {
		e.preventDefault();
		fetchFilteredResults(1);
	});

	$(document).on('click', '.load-more-btn', function () {
		const page = parseInt($(this).data('page'));
		fetchFilteredResults(page);
	});

	fetchFilteredResults(1);
});

