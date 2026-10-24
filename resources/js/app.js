const themeToggleButtons = document.querySelectorAll('[data-theme-toggle]');

const applyTheme = (theme) => {
	const isDark = theme === 'dark';

	document.documentElement.dataset.theme = isDark ? 'dark' : 'light';
	document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';

	themeToggleButtons.forEach((button) => {
		const nextThemeLabel = isDark ? 'Ativar modo claro' : 'Ativar modo escuro';

		button.setAttribute('aria-pressed', String(isDark));
		button.setAttribute('aria-label', nextThemeLabel);
		button.setAttribute('title', nextThemeLabel);
		button.innerHTML = isDark
			? '<i class="bi bi-sun" aria-hidden="true"></i>'
			: '<i class="bi bi-moon-stars" aria-hidden="true"></i>';
	});
};

let savedTheme = document.documentElement.dataset.theme || 'light';

try {
	savedTheme = localStorage.getItem('nexstep-theme') || savedTheme;
} catch {
	savedTheme = document.documentElement.dataset.theme || 'light';
}

applyTheme(savedTheme);

themeToggleButtons.forEach((button) => {
	button.addEventListener('click', () => {
		const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

		applyTheme(nextTheme);

		try {
			localStorage.setItem('nexstep-theme', nextTheme);
		} catch {
			// The theme remains active for the current page if storage is unavailable.
		}
	});
});
