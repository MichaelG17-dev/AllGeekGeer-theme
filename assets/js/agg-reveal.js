/**
 * AllGeekGear — поява елементів при скролі
 * Шлях: assets/js/agg-reveal.js
 *
 * <div class="agg-reveal" style="--agg-delay:180ms">…</div>
 * Коли елемент входить у в'юпорт, додається клас .is-visible.
 */
(function () {
	'use strict';

	function init(root) {
		var nodes = (root || document).querySelectorAll('.agg-reveal:not(.is-visible)');
		if (!nodes.length) return;

		if (!('IntersectionObserver' in window)) {
			nodes.forEach(function (n) {
				n.classList.add('is-visible');
			});
			return;
		}

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -60px 0px' }
		);

		nodes.forEach(function (n) {
			io.observe(n);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			init();
		});
	} else {
		init();
	}

	document.addEventListener('agg:refresh', function (e) {
		init(e.detail && e.detail.root);
	});

	window.aggInitReveal = init;
})();
