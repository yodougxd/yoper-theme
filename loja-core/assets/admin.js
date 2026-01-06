document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-loja-copy]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var text = btn.getAttribute('data-loja-copy') || '';
            if (!text) {
                return;
            }
            navigator.clipboard.writeText(text).then(function () {
                btn.textContent = 'Copiado!';
                setTimeout(function () {
                    btn.textContent = 'Copiar';
                }, 1500);
            });
        });
    });
});
