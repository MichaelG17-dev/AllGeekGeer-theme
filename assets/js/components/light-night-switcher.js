document.addEventListener('DOMContentLoaded', () => {
	const toggleSwitch = document.querySelector('#checkbox');
	const rootElement = document.documentElement;

	const applyTheme = (theme) => {
		if (theme === 'light-mode') {
			rootElement.classList.remove('dark-mode');
			rootElement.classList.add('light-mode');
			if (toggleSwitch) toggleSwitch.checked = true;
		} else {
			rootElement.classList.remove('light-mode');
			rootElement.classList.add('dark-mode');
			if (toggleSwitch) toggleSwitch.checked = false;
		}
		localStorage.setItem('theme', theme);
	};

	// 1. Зчитуємо збережену тему при завантаженні (за замовчуванням — dark-mode)
	const savedTheme = localStorage.getItem('theme') || 'dark-mode';
	applyTheme(savedTheme);

	// 2. Слухач кліку по перемикачу
	if (toggleSwitch) {
		toggleSwitch.addEventListener('change', (e) => {
			const newTheme = e.target.checked ? 'light-mode' : 'dark-mode';
			applyTheme(newTheme);
		});
	}
});