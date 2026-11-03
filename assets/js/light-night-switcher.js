/**
 * AllGeekGear — перемикач теми (Шарінган ⇄ Ріннеган)
 * Шлях: assets/js/light-night-switcher.js
 *
 * ЩО ЗМІНИЛОСЬ ПОРІВНЯНО З ТВОЄЮ ВЕРСІЄЮ
 * ------------------------------------------------------------
 * 1. Прибрано console.log поза DOMContentLoaded — він виконувався
 *    ДО появи #checkbox і завжди друкував null.
 * 2. Додано підтримку кількох перемикачів на сторінці
 *    (десктопний у хедері + мобільний у меню).
 * 3. Додано клас .agg-no-transition на час перемикання, щоб
 *    кольори не «пливли» по всіх елементах одночасно.
 * 4. Подія 'agg:themechange' — щоб інші скрипти могли реагувати.
 * 5. Захист від приватного режиму, де localStorage кидає помилку.
 *
 * ВАЖЛИВО: щоб не було спалаху білим при завантаженні, інлайновий
 * скрипт у <head> має виставити клас ще до рендера —
 * див. agg_theme_no_flash_script() у inc/enqueue.php
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'theme';
	var root = document.documentElement;

	function readTheme() {
		try {
			return localStorage.getItem(STORAGE_KEY);
		} catch (e) {
			return null;
		}
	}

	function saveTheme(theme) {
		try {
			localStorage.setItem(STORAGE_KEY, theme);
		} catch (e) {
			/* приватний режим — просто не зберігаємо */
		}
	}

	function applyTheme(theme, animate) {
		var isLight = theme === 'light-mode';

		if (animate === false) {
			root.classList.add('agg-no-transition');
		}

		root.classList.toggle('light-mode', isLight);
		root.classList.toggle('dark-mode', !isLight);

		document.querySelectorAll('.agg-switch__input, #checkbox').forEach(function (input) {
			input.checked = isLight;
			input.setAttribute('aria-label', isLight ? 'Увімкнути нічну тему' : 'Увімкнути денну тему');
		});

		saveTheme(theme);

		if (animate === false) {
			/* повертаємо transition наступного кадру */
			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					root.classList.remove('agg-no-transition');
				});
			});
		}

		document.dispatchEvent(new CustomEvent('agg:themechange', { detail: { theme: theme } }));
	}

	function init() {
		var saved = readTheme() || 'dark-mode';
		applyTheme(saved, false);

		document.addEventListener('change', function (e) {
			var input = e.target.closest('.agg-switch__input, #checkbox');
			if (!input) return;
			applyTheme(input.checked ? 'light-mode' : 'dark-mode', true);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	window.aggSetTheme = applyTheme;
})();
