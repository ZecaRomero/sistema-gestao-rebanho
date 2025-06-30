document.addEventListener('DOMContentLoaded', function() {
    function parseBRL(str) {
        if (!str) return 0;
        return parseFloat(str.replace(/\./g, '').replace(',', '.') || '0');
    }

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

    // --- Lógica do Status Ativo/Inativo ---

    const ativoBtns = document.querySelectorAll('.ativo-btn');
    const ativoInput = document.getElementById('ativo-input');
    const animalStatusTexto = document.getElementById('animal-status-texto');
    const inativacaoContainer = document.getElementById('inativacao-container');

    function updateAnimalStatusText() {
        const status = ativoInput.value;
        if (status === 'S') {
            animalStatusTexto.textContent = 'ANIMAL ATIVO ';
            animalStatusTexto.style.color = '#28a745'; // Verde
            animalStatusTexto.classList.remove('status-inativo-animado');
        } else {
            animalStatusTexto.textContent = 'ANIMAL INATIVO';
            animalStatusTexto.style.color = '#dc3545'; // Vermelho
            animalStatusTexto.classList.add('status-inativo-animado');
        }
    }

    function setInitialAtivoState() {
        const selectedBtn = document.querySelector('.ativo-btn.selected');
        if (!selectedBtn) {
            const simBtn = document.querySelector('.ativo-btn.ativo-sim');
            simBtn.classList.add('selected');
            ativoInput.value = 'S';
        }
        updateAnimalStatusText();
    }
    
    ativoBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            ativoBtns.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            ativoInput.value = btn.getAttribute('data-value');
            updateAnimalStatusText();
        });
    });

    function handleInativacao() {
        const valorVendaInput = document.querySelector('[name="valor_venda"]');
        const valorAbateInput = document.querySelector('[name="valor_abate"]');
        const dataInativacaoInput = document.querySelector('[name="data_inativacao"]');
        
        const vendaValue = parseBRL(valorVendaInput.value);
        const abateValue = parseBRL(valorAbateInput.value);

        const ativoNaoBtn = document.querySelector('.ativo-btn.ativo-nao');
        const ativoSimBtn = document.querySelector('.ativo-btn.ativo-sim');

        if (vendaValue > 0 || abateValue > 0) {
            ativoNaoBtn.classList.add('selected');
            ativoSimBtn.classList.remove('selected');
            ativoInput.value = 'N';
            inativacaoContainer.style.display = 'block';
            if (!dataInativacaoInput.value) {
                const today = new Date().toISOString().split('T')[0];
                dataInativacaoInput.value = today;
            }
        } else {
            ativoSimBtn.classList.add('selected');
            ativoNaoBtn.classList.remove('selected');
            ativoInput.value = 'S';
            inativacaoContainer.style.display = 'none';
            dataInativacaoInput.value = '';
        }
        updateAnimalStatusText();
    }
    
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
        const compra = parseBRL(document.querySelector('[name="valor_adquirido"]').value);
        const venda = parseBRL(document.querySelector('[name="valor_venda"]').value);
        const abate = parseBRL(document.querySelector('[name="valor_abate"]').value);

        let saida = 0;
        if (venda > 0) {
            saida = venda;
        } else {
            saida = abate;
        }

        const resultado = compra - saida;

        const valorFinal = document.getElementById('valor-final');
        const resumoDiv = document.querySelector('.resumo-valor');
        
        valorFinal.textContent = resultado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        
        if (resultado > 0) {
            resumoDiv.classList.add('positivo');
            resumoDiv.classList.remove('negativo');
        } else if (resultado < 0) {
            resumoDiv.classList.remove('positivo');
            resumoDiv.classList.add('negativo');
        } else {
            resumoDiv.classList.remove('positivo', 'negativo');
        }
    }
    document.querySelectorAll('[name="valor_adquirido"], [name="valor_venda"], [name="valor_abate"]').forEach(function(input) {
        input.addEventListener('input', function() {
            atualizarResumoValor();
            handleInativacao();
        });
    });
    atualizarResumoValor();
    setInitialAtivoState();

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
    
    // --- Automação para Raça Receptoras ---
    const racaSelect = document.querySelector('[name="raca"]');
    const sexoMachoBtn = document.querySelector('.sexo-btn.sexo-macho');
    const sexoFemeaBtn = document.querySelector('.sexo-btn.sexo-femea');
    if (racaSelect && sexoMachoBtn && sexoFemeaBtn && sexoInput) {
        function handleReceptoras() {
            if (racaSelect.value === 'Receptoras') {
                sexoFemeaBtn.classList.add('selected');
                sexoMachoBtn.classList.remove('selected');
                sexoInput.value = 'F';
                sexoMachoBtn.disabled = true;
                sexoMachoBtn.style.opacity = 0.5;
            } else {
                sexoMachoBtn.disabled = false;
                sexoMachoBtn.style.opacity = 1;
            }
        }
        racaSelect.addEventListener('change', handleReceptoras);
        // Também aplica ao carregar a página
        handleReceptoras();
        // Garante que ao clicar nos botões, o valor do input escondido seja atualizado
        sexoMachoBtn.addEventListener('click', function() {
            if (!sexoMachoBtn.disabled) {
                sexoInput.value = 'M';
            }
        });
        sexoFemeaBtn.addEventListener('click', function() {
            sexoInput.value = 'F';
        });
    }

    // --- Sincronizar valor e origem de entrada com resumo ---
    const entradaValor = document.getElementById('entrada-valor');
    const valorAdquirido = document.getElementById('valor-adquirido');
    const entradaOrigem = document.getElementById('entrada-origem');
    const origemCompra = document.getElementById('origem-compra');
    if (entradaValor && valorAdquirido) {
        entradaValor.addEventListener('input', function() {
            valorAdquirido.value = entradaValor.value;
        });
    }
    if (entradaOrigem && origemCompra) {
        entradaOrigem.addEventListener('input', function() {
            origemCompra.value = entradaOrigem.value;
        });
    }
}); 