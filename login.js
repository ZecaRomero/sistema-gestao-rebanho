document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio tradicional do formulário

    const formData = new FormData(this);

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('A resposta da rede não foi boa');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Redireciona para o dashboard se o login for bem-sucedido
            window.location.href = 'dashboard.php';
        } else {
            // Exibe uma mensagem de erro
            alert(data.message || 'Ocorreu um erro.');
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Não foi possível conectar ao servidor.');
    });
}); 