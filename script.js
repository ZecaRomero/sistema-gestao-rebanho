document.addEventListener('DOMContentLoaded', function() {
    const sexoBtns = document.querySelectorAll('.sexo-btn');
    const sexoInput = document.getElementById('sexo-input');
    const sexoFeedback = document.getElementById('sexo-feedback');

    sexoBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            sexoBtns.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            sexoInput.value = btn.getAttribute('data-value');
            sexoFeedback.textContent = btn.textContent + ' selecionado!';
            sexoFeedback.style.color = btn.classList.contains('sexo-macho') ? '#2196f3' : '#e75480';
        });
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                let idx = Array.from(sexoBtns).indexOf(document.activeElement);
                if (e.key === 'ArrowRight') idx = (idx + 1) % sexoBtns.length;
                if (e.key === 'ArrowLeft') idx = (idx - 1 + sexoBtns.length) % sexoBtns.length;
                sexoBtns[idx].focus();
            }
        });
    });

    // Feedback de seleção para ativo
    const ativoBtns = document.querySelectorAll('.ativo-btn');
    const ativoInput = document.getElementById('ativo-input');
    const ativoFeedback = document.getElementById('ativo-feedback');
    ativoBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            ativoBtns.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            ativoInput.value = btn.getAttribute('data-value');
            ativoFeedback.textContent = btn.textContent + ' selecionado!';
            ativoFeedback.style.color = btn.classList.contains('ativo-sim') ? '#28a745' : '#6c757d';
        });
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                let idx = Array.from(ativoBtns).indexOf(document.activeElement);
                if (e.key === 'ArrowRight') idx = (idx + 1) % ativoBtns.length;
                if (e.key === 'ArrowLeft') idx = (idx - 1 + ativoBtns.length) % ativoBtns.length;
                ativoBtns[idx].focus();
            }
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

    // Emoji e cor dinâmica no resumo
    function atualizarResumoValor() {
        function parseBRL(str) {
            if (!str) return 0;
            return parseFloat(str.replace(/\./g, '').replace(',', '.') || '0');
        }
        const compra = parseBRL(document.querySelector('[name="valor_adquirido"]').value);
        const venda = parseBRL(document.querySelector('[name="valor_venda"]').value);
        const resultado = compra - venda;
        const valorFinal = document.getElementById('valor-final');
        const emoji = document.getElementById('emoji-valor');
        const resumoDiv = document.querySelector('.resumo-valor');
        valorFinal.textContent = resultado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        if (resultado > 0) {
            resumoDiv.classList.add('positivo');
            resumoDiv.classList.remove('negativo');
            emoji.textContent = '😊';
        } else if (resultado < 0) {
            resumoDiv.classList.remove('positivo');
            resumoDiv.classList.add('negativo');
            emoji.textContent = '😟';
        } else {
            resumoDiv.classList.remove('positivo', 'negativo');
            emoji.textContent = '😐';
        }
    }
    document.querySelectorAll('[name="valor_adquirido"], [name="valor_venda"]').forEach(function(input) {
        input.addEventListener('input', atualizarResumoValor);
    });
    atualizarResumoValor();

    // Idade em anos ao lado de meses
    const mesesInput = document.querySelector('[name="meses"]');
    const idadeAnosSpan = document.getElementById('idade-anos');
    function atualizarIdadeAnos() {
        const meses = parseInt(mesesInput.value, 10);
        if (!isNaN(meses) && meses > 0) {
            const anos = (meses / 12).toFixed(1);
            idadeAnosSpan.textContent = `(${anos} anos)`;
        } else {
            idadeAnosSpan.textContent = '';
        }
    }
    mesesInput.addEventListener('input', atualizarIdadeAnos);
    atualizarIdadeAnos();

    // Calcular meses automaticamente ao digitar nascimento
    const nascimentoInput = document.querySelector('[name="nascimento"]');
    if (nascimentoInput && mesesInput) {
        nascimentoInput.addEventListener('input', function() {
            const nasc = new Date(nascimentoInput.value);
            const hoje = new Date();
            if (!isNaN(nasc.getTime())) {
                let anos = hoje.getFullYear() - nasc.getFullYear();
                let meses = hoje.getMonth() - nasc.getMonth();
                let totalMeses = anos * 12 + meses;
                if (hoje.getDate() < nasc.getDate()) totalMeses--;
                mesesInput.value = totalMeses > 0 ? totalMeses : 0;
                // Atualiza idade em anos também
                atualizarIdadeAnos();
            }
        });
    }
}); 

// Dark Mode Toggle
const darkToggle = document.getElementById("darkModeToggle");
if (darkToggle) {
    darkToggle.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
    });
}

// Alerta de envio
const form = document.querySelector("form");
form.addEventListener("submit", (e) => {
    e.preventDefault();
    alert("✅ Dados salvos com sucesso!");
    form.submit(); // Prossiga com envio real
});

// Botão de limpar
const resetBtn = document.getElementById("resetForm");
resetBtn?.addEventListener("click", () => {
    if (!confirm("Tem certeza que deseja limpar todos os dados do formulário?")) {
        event.preventDefault();
    }
});
