document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('login.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect; // Usa a URL do servidor
        } else {
            alert(data.message || 'Erro no login');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro de conexão com o servidor');
    });
});