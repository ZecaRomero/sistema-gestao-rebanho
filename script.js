document.addEventListener('DOMContentLoaded', function() {
    const sexoBtns = document.querySelectorAll('.sexo-btn');
    const sexoInput = document.getElementById('sexo-input');

    sexoBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            sexoBtns.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            sexoInput.value = btn.getAttribute('data-value');
        });
    });
});

// Máscara para valores em reais (formato brasileiro)
document.querySelectorAll('.valor-reais').forEach(function(input) {
    input.addEventListener('input', function(e) {
        let value = input.value.replace(/\D/g, '');
        value = value.padStart(3, '0');
        let intPart = value.slice(0, -2);
        let decPart = value.slice(-2);
        intPart = intPart.replace(/^0+/, '') || '0';
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        input.value = intPart + ',' + decPart;
    });
    input.addEventListener('blur', function(e) {
        if (input.value === ',00') input.value = '';
    });
});

// Atualiza o resumo do valor final
function atualizarResumoValor() {
    function parseBRL(str) {
        if (!str) return 0;
        return parseFloat(str.replace(/\./g, '').replace(',', '.') || '0');
    }
    const compra = parseBRL(document.querySelector('[name="valor_adquirido"]').value);
    const venda = parseBRL(document.querySelector('[name="valor_venda"]').value);
    const resultado = compra - venda;
    const formatado = resultado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    document.getElementById('valor-final').textContent = formatado;
}
document.querySelectorAll('[name="valor_adquirido"], [name="valor_venda"]').forEach(function(input) {
    input.addEventListener('input', atualizarResumoValor);
});
// Atualiza ao carregar
atualizarResumoValor(); 