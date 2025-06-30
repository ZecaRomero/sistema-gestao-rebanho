document.addEventListener('DOMContentLoaded', function() {
    function parseBRL(str) {
        if (!str) return 0;
        return parseFloat(str.replace(/\./g, '').replace(',', '.') || '0');
    }

    // --- Lógica do Status Ativo/Inativo ---

    const ativoBtns = document.querySelectorAll('.ativo-btn');
    const ativoInput = document.getElementById('ativo-input');
    const animalStatusTexto = document.getElementById('animal-status-texto');
    const inativacaoContainer = document.getElementById('inativacao-container');

    function updateAnimalStatusText() {
        if (!ativoInput) return;
        const status = ativoInput.value;
        if (!animalStatusTexto) return;
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
        if (!ativoInput) return;
        const selectedBtn = document.querySelector('.ativo-btn.selected');
        if (!selectedBtn) {
            const simBtn = document.querySelector('.ativo-btn.ativo-sim');
            if (simBtn) {
                simBtn.classList.add('selected');
                ativoInput.value = 'S';
            }
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
        const compraInput = document.querySelector('[name="valor_adquirido"]');
        const vendaInput = document.querySelector('[name="valor_venda"]');
        const abateInput = document.querySelector('[name="valor_abate"]');
        if (!compraInput || !vendaInput || !abateInput) return;

        const compra = parseBRL(compraInput.value);
        const venda = parseBRL(vendaInput.value);
        const abate = parseBRL(abateInput.value);

        let saida = 0;
        if (venda > 0) {
            saida = venda;
        } else {
            saida = abate;
        }

        const resultado = compra - saida;

        const valorFinal = document.getElementById('valor-final');
        const resumoDiv = document.querySelector('.resumo-valor');
        if (!valorFinal || !resumoDiv) return;
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
        if (!mesesInput || !idadeAnosSpan) return;
        const meses = parseInt(mesesInput.value, 10);
        if (!isNaN(meses) && meses > 0) {
            const anos = (meses / 12).toFixed(1);
            idadeAnosSpan.textContent = `(${anos} anos)`;
        } else {
            idadeAnosSpan.textContent = '';
        }
    }
    if (mesesInput && idadeAnosSpan) {
        mesesInput.addEventListener('input', atualizarIdadeAnos);
        atualizarIdadeAnos();
    }

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
    
    // --- Automação para Raça Receptoras e Sincronização de campos ---
    const racaSelect = document.querySelector('[name="raca"]');
    const sexoBtns = document.querySelectorAll('.sexo-btn');
    const sexoMachoBtn = document.querySelector('.sexo-btn.sexo-macho');
    const sexoFemeaBtn = document.querySelector('.sexo-btn.sexo-femea');
    const sexoInput = document.getElementById('sexo-input');
    const sexoFeedback = document.getElementById('sexo-feedback');

    function setSexo(value) {
        sexoBtns.forEach(b => b.classList.remove('selected'));
        if (value === 'M') {
            sexoMachoBtn.classList.add('selected');
            sexoInput.value = 'M';
            sexoFeedback.textContent = 'Macho selecionado!';
            sexoFeedback.style.color = '#2196f3';
            sexoFeedback.style.display = 'inline-block';
        } else if (value === 'F') {
            sexoFemeaBtn.classList.add('selected');
            sexoInput.value = 'F';
            sexoFeedback.textContent = 'Fêmea selecionada!';
            sexoFeedback.style.color = '#e75480';
            sexoFeedback.style.display = 'inline-block';
        }
    }

    function handleReceptoras() {
        if (racaSelect.value === 'Receptoras') {
            setSexo('F');
            sexoMachoBtn.disabled = true;
            sexoMachoBtn.style.opacity = 0.5;
        } else {
            sexoMachoBtn.disabled = false;
            sexoMachoBtn.style.opacity = 1;
        }
    }
    if (racaSelect && sexoMachoBtn && sexoFemeaBtn && sexoInput) {
        racaSelect.addEventListener('change', handleReceptoras);
        handleReceptoras();
        sexoMachoBtn.addEventListener('click', function() {
            if (!sexoMachoBtn.disabled) setSexo('M');
        });
        sexoFemeaBtn.addEventListener('click', function() {
            setSexo('F');
        });
    }

    // Sincronizar valor e origem de entrada com resumo (também ao carregar a página)
    const entradaValor = document.getElementById('entrada-valor');
    const valorAdquirido = document.getElementById('valor-adquirido');
    const entradaOrigem = document.getElementById('entrada-origem');
    const origemCompra = document.getElementById('origem-compra');
    if (entradaValor && valorAdquirido) {
        function syncValor() {
            valorAdquirido.value = entradaValor.value;
        }
        entradaValor.addEventListener('input', syncValor);
        syncValor();
    }
    if (entradaOrigem && origemCompra) {
        function syncOrigem() {
            origemCompra.value = entradaOrigem.value;
        }
        entradaOrigem.addEventListener('input', syncOrigem);
        syncOrigem();
    }

    // Mostrar mensagem ao clicar nos botões de sexo
    sexoBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            sexoBtns.forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            sexoInput.value = btn.getAttribute('data-value');
            if (btn.classList.contains('sexo-macho')) {
                sexoFeedback.textContent = 'Macho selecionado';
                sexoFeedback.style.color = '#2196f3';
                sexoFeedback.style.display = 'inline-block';
            } else if (btn.classList.contains('sexo-femea')) {
                sexoFeedback.textContent = 'Fêmea selecionada';
                sexoFeedback.style.color = '#e75480';
                sexoFeedback.style.display = 'inline-block';
            }
        });
    });

    // --- Automação para Série RPT ---
    const serieInput = document.querySelector('[name="serie"]');
    if (serieInput && racaSelect && sexoFemeaBtn && sexoInput) {
        serieInput.addEventListener('input', function() {
            if (serieInput.value.trim().toLowerCase().startsWith('rpt')) {
                racaSelect.value = 'Receptoras';
                const event = new Event('change', { bubbles: true });
                racaSelect.dispatchEvent(event); // dispara evento para lógica já existente
                racaSelect.blur(); // fecha o select
                sexoFemeaBtn.classList.add('selected');
                sexoMachoBtn.classList.remove('selected');
                sexoInput.value = 'F';
            }
        });
    }

    // Função para atualizar o valor real do animal
    function atualizarValorReal() {
        const entradaInput = document.getElementById('entrada-valor');
        const saidaInput = document.getElementById('saida-valor');
        const valorRealSpan = document.getElementById('valor-real-feedback');
        const graficoDiv = document.getElementById('grafico-valor-real');
        if (!entradaInput || !saidaInput || !valorRealSpan || !graficoDiv) return;
        const entrada = parseBRL(entradaInput.value);
        const saida = parseBRL(saidaInput.value);
        if (entrada || saida) {
            const real = entrada - saida;
            valorRealSpan.textContent = `Valor adquirido R$ ${entrada.toLocaleString('pt-BR')} - R$ ${saida.toLocaleString('pt-BR')}, valor real: R$ ${real.toLocaleString('pt-BR')}`;
            // Gráfico de barra melhorado e mais bonito
            let cor = real >= 0 ? '#28a745' : '#e74c3c';
            let largura = Math.min(Math.abs(real) / (entrada || 1) * 100, 100);
            graficoDiv.innerHTML = `<div style="background:${cor};height:40px;width:${largura}%;max-width:100%;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:1.3em;font-weight:bold;color:#fff;white-space:nowrap;overflow:hidden;padding:0 32px;margin:18px auto 0 auto;box-shadow:0 2px 8px rgba(0,0,0,0.10);letter-spacing:1px;">${real >= 0 ? 'Ganho' : 'Perda'}: R$ ${real.toLocaleString('pt-BR')}</div>`;
        } else {
            valorRealSpan.textContent = '';
            graficoDiv.innerHTML = '';
        }
    }

    // Atualizar ao digitar nos campos de valor
    const entradaInput = document.getElementById('entrada-valor');
    const saidaInput = document.getElementById('saida-valor');
    if (entradaInput) entradaInput.addEventListener('input', atualizarValorReal);
    if (saidaInput) saidaInput.addEventListener('input', atualizarValorReal);
    atualizarValorReal();

    // Função para atualizar status ativo/inativo automaticamente ao preencher saída
    function atualizarStatusPorSaida() {
        const saidaInput = document.getElementById('saida-valor');
        const ativoInput = document.getElementById('ativo-input');
        const statusAnimal = document.getElementById('status-animal');
        if (!saidaInput || !ativoInput || !statusAnimal) return;
        const valorSaida = parseBRL(saidaInput.value);
        if (valorSaida > 0) {
            ativoInput.value = 'N';
            statusAnimal.value = 'INATIVO';
            statusAnimal.style.color = '#dc3545';
        } else {
            ativoInput.value = 'S';
            statusAnimal.value = 'ATIVO';
            statusAnimal.style.color = '#28a745';
        }
        updateAnimalStatusText && updateAnimalStatusText();
    }
    // Atualizar status ao digitar no campo de saída
    const saidaInputAuto = document.getElementById('saida-valor');
    if (saidaInputAuto) saidaInputAuto.addEventListener('input', atualizarStatusPorSaida);
    atualizarStatusPorSaida();

    // Atualizar status ao digitar no campo de destino de saída
    const saidaDestinoInput = document.getElementById('saida-destino');
    if (saidaDestinoInput) {
        saidaDestinoInput.addEventListener('input', function() {
            const ativoInput = document.getElementById('ativo-input');
            const statusAnimal = document.getElementById('status-animal');
            if (!ativoInput || !statusAnimal) return;
            if (saidaDestinoInput.value.trim() !== '') {
                ativoInput.value = 'N';
                statusAnimal.value = 'INATIVO';
                statusAnimal.style.color = '#dc3545';
            } else {
                ativoInput.value = 'S';
                statusAnimal.value = 'ATIVO';
                statusAnimal.style.color = '#28a745';
            }
            updateAnimalStatusText && updateAnimalStatusText();
        });
    }
}); 