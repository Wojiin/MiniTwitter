document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('post-composer-toggle');
    const formWrapper = document.getElementById('post-composer-panel');

    if (!btn || !formWrapper) {
        return;
    }

    btn.addEventListener('click', () => {
        formWrapper.classList.toggle('open');
        btn.setAttribute(
            'aria-expanded',
            formWrapper.classList.contains('open') ? 'true' : 'false'
        );
    });
});
