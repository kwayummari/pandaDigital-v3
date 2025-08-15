<form id="register-form">
    <div class="form-group">
        <label for="first_name">Jina la kwanza:</label>
        <input type="text" class="form-control" id="first_name" name="first_name" required>
    </div>
    <div class="form-group">
        <label for="last_name">Jina la Mwisho:</label>
        <input type="text" class="form-control" id="last_name" name="last_name" required>
    </div>
    <div class="form-group">
        <label for="email">Anwani ya barua pepe:</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="password">Nenosiri:</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="phone">Nambari ya Simu:</label>
        <input type="text" class="form-control" id="phone" name="phone" required>
    </div>
    <div class="form-group">
        <label for="region">Mkoa:</label>
        <input type="text" class="form-control" id="region" name="region" required>
    </div>
    <div class="form-group">
        <label for="business">Biashara:</label>
        <input type="text" class="form-control" id="business" name="business" required>
    </div>
    <div class="form-group">
        <label for="gender">Jinsia:</label>
        <select class="form-control" id="gender" name="gender" required>
            <option value="male">Mwanaume</option>
            <option value="female">Mwanamke</option>
        </select>
    </div>
    <div class="form-group">
        <label for="gender">Je, unatafuta kuuza bidhaa?</label>
        <select class="form-control" id="isSeller" name="isSeller" required>
            <option value=false>Hapana</option>
            <option value=true>Ndio</option>
        </select>
    </div>
    <div class="form-group">
        <label for="date_of_birth">Tarehe ya Kuzaliwa:</label>
        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Funa</button>
        <button type="submit" class="btn btn-primary">JISAJILI</button>
    </div>
        <p>Je, tayari una akaunti? <a href="#" id="show-login-form">LIngia hapa</a></p>
    </div>
</form>

<script>
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        region: document.getElementById('region').value,
        business: document.getElementById('business').value,
        gender: document.getElementById('gender').value,
        date_of_birth: document.getElementById('date_of_birth').value,
        isSeller: document.getElementById('isSeller').value,
        password: document.getElementById('password').value
    };

    // Create query string
    const queryString = Object.keys(formData)
        .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(formData[key])}`)
        .join('&');

    fetch(`register.php?${queryString}`)
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            alert(data.message);
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during registration.');
        });
});
</script>