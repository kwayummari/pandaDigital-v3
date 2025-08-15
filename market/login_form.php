<form method="POST" id="login-form">
    <div class="form-group">
        <label for="login-email">Anwani ya barua pepe:</label>
        <input type="email" class="form-control" id="login-email" name="email" required>
    </div>
    <div class="form-group">
        <label for="login-password">Nenosiri:</label>
        <input type="password" class="form-control" id="login-password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Ingia</button>
    <p>Don't have an account? <a href="#" id="show-register-form">Jisajili hapa</a></p>
</form>

<script>
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        console.log('Starting login request...');
        console.log('Email:', email);

        fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
            .then(response => {
                console.log('Response received:', response);
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return response.text(); // Get as text first to see what we're getting
            })
            .then(text => {
                console.log('Response text:', text);

                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);

                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    console.error('Response was not valid JSON:', text);
                    alert('Server returned invalid response: ' + text.substring(0, 100));
                }
            })
            .catch(error => {
                console.error('Fetch error details:', error);
                console.error('Error name:', error.name);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
                alert('Network error: ' + error.message);
            });
    });
</script>