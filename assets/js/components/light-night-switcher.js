(function () {
	const savedTheme = localStorage.getItem('theme') || 'dark-mode';
	document.documentElement.classList.add(savedTheme);
})();

document.addEventListener('DOMContentLoaded', function () {
	const toggleSwitch = document.querySelector('#checkbox');

	function applyTheme(theme) {
		if (theme === 'light-mode') {
			document.documentElement.classList.replace('dark-mode', 'light-mode');
			document.body.classList.remove('dark-mode');
			document.body.classList.add('light-mode');
		} else {
			document.documentElement.classList.replace('light-mode', 'dark-mode');
			document.body.classList.remove('light-mode');
			document.body.classList.add('dark-mode');
		}
	}

	const currentTheme = localStorage.getItem('theme') || 'dark-mode';
	if (toggleSwitch) {
		toggleSwitch.checked = (currentTheme === 'light-mode');
	}

	applyTheme(currentTheme);

	if (toggleSwitch) {
		toggleSwitch.addEventListener('change', (e) => {
			const newTheme = e.target.checked ? 'light-mode' : 'dark-mode';
			applyTheme(newTheme);
			localStorage.setItem('theme', newTheme);
		});
	}
});

(function () {
	const savedTheme = localStorage.getItem('theme');
	if (savedTheme === 'light-mode') {
		document.documentElement.classList.add('light-mode');
	}
})();