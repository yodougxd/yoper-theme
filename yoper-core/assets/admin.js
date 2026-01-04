document.addEventListener('DOMContentLoaded', function () {
    var copyBtn = document.querySelector('[data-yoper-copy-list]');
    if (copyBtn) {
        copyBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var text = copyBtn.getAttribute('data-yoper-copy-list') || '';
            if (!text) {
                return;
            }
            navigator.clipboard.writeText(text).then(function () {
                copyBtn.textContent = 'Copiado!';
                setTimeout(function () {
                    copyBtn.textContent = 'Copiar para WhatsApp';
                }, 1500);
            });
        });
    }
});
