document.addEventListener('DOMContentLoaded', () => {

    const body = document.body;
    const boutonModeSombre = document.getElementById('btn-dark-mode');

    function getCookie(nom) {
        const cookies = document.cookie.split(';');

        for (let cookie of cookies) {
            cookie = cookie.trim();

            if (cookie.startsWith(nom + '=')) {
                return cookie.substring(nom.length + 1);
            }
        }

        return null;
    }

    if (getCookie('modeSombre') === 'true') {
        body.classList.add('dark-mode');
    }
    if (boutonModeSombre) {

        boutonModeSombre.addEventListener('click', () => {

            body.classList.toggle('dark-mode');

            document.cookie =
                "modeSombre=" +
                body.classList.contains('dark-mode') +
                "; path=/; max-age=31536000";
        });
    }
});