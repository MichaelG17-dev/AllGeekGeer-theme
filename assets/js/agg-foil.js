/**
 * AllGeekGear — інтерактив фольгованих карток (3D-нахил + джерело світла)
 * Шлях: assets/js/agg-foil.js
 *
 * Скрипт не знає нічого про конкретні ефекти — він лише виставляє
 * CSS-змінні на елементі .agg-foil, а решту робить CSS:
 *
 *   --agg-px / --agg-py  позиція курсора 0…100 (числом, для calc)
 *   --agg-mx / --agg-my  те саме у відсотках
 *   --agg-rx / --agg-ry  кут нахилу картки
 *   data-active="true"   увімкнути ефект
 *
 * Кут нахилу задається атрибутом data-tilt (градуси, типово 14):
 *   <article class="agg-foil" data-tilt="18">
 */
(function () {
	'use strict';

	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var clamp = function (v) {
		return Math.min(1, Math.max(0, v));
	};

	function applyVars(card, x, y, max, active) {
		var s = card.style;
		s.setProperty('--agg-px', String(x * 100));
		s.setProperty('--agg-py', String(y * 100));
		s.setProperty('--agg-mx', x * 100 + '%');
		s.setProperty('--agg-my', y * 100 + '%');

		if (!reduced) {
			s.setProperty('--agg-rx', (0.5 - y) * max * 2 + 'deg');
			s.setProperty('--agg-ry', (x - 0.5) * max * 2 + 'deg');
		}

		card.dataset.active = active ? 'true' : 'false';
	}

	function initCard(card) {
		if (card.dataset.aggFoilReady === '1') return;
		card.dataset.aggFoilReady = '1';

		var max = parseFloat(card.dataset.tilt || '14');
		var frame = 0;

		card.addEventListener(
			'pointermove',
			function (e) {
				var rect = card.getBoundingClientRect();
				var x = clamp((e.clientX - rect.left) / rect.width);
				var y = clamp((e.clientY - rect.top) / rect.height);

				cancelAnimationFrame(frame);
				frame = requestAnimationFrame(function () {
					applyVars(card, x, y, max, true);
				});
			},
			{ passive: true }
		);

		var reset = function () {
			cancelAnimationFrame(frame);
			applyVars(card, 0.5, 0.5, max, false);
		};

		card.addEventListener('pointerleave', reset, { passive: true });
		card.addEventListener('pointercancel', reset, { passive: true });

		/* Тачскрін: тап вмикає ефект на 2.5 с */
		card.addEventListener(
			'touchstart',
			function () {
				card.dataset.active = 'true';
				clearTimeout(card._aggTimer);
				card._aggTimer = setTimeout(reset, 2500);
			},
			{ passive: true }
		);
	}

	function initAll(root) {
		(root || document).querySelectorAll('.agg-foil').forEach(initCard);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initAll();
		});
	} else {
		initAll();
	}

	/* Нові картки після AJAX-фільтра / підвантаження */
	document.addEventListener('agg:refresh', function (e) {
		initAll(e.detail && e.detail.root);
	});

	window.aggInitFoil = initAll;
})();
