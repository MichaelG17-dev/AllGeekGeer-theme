document.addEventListener('DOMContentLoaded', () => {
    const toggleSwitch = document.querySelector('#checkbox');
    
    // Функція застосування теми
    const applyTheme = (theme) => {
        if (theme === 'light-mode') {
            document.documentElement.classList.remove('dark-mode');
            document.documentElement.classList.add('light-mode');
            document.body.classList.remove('dark-mode');
            document.body.classList.add('light-mode');
        } else {
            document.documentElement.classList.remove('light-mode');
            document.documentElement.classList.add('dark-mode');
            document.body.classList.remove('light-mode');
            document.body.classList.add('dark-mode');
        }
    };

    // 1. Ініціалізація теми при завантаженні сторінки (щоб не було бліку)
    const savedTheme = localStorage.getItem('theme') || 'dark-mode';
    applyTheme(savedTheme);

    if (toggleSwitch) {
        toggleSwitch.checked = (savedTheme === 'light-mode');

        toggleSwitch.addEventListener('change', (e) => {
            const newTheme = e.target.checked ? 'light-mode' : 'dark-mode';
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
});