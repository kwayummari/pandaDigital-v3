<form method="POST" id="register-form" action="register.php" style="display: none;">
    <div class="form-group">
        <label for="first_name">Jina la kwanza:</label>
        <input type="text" class="form-control" id="first_name" name="first_name" required>
    </div>
    <div class="form-group">
        <label for="last_name">Jina la mwisho:</label>
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
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Funga</button>
            <button type="submit" name="register" class="btn btn-primary">JISAJILI</button>
        </div>
        <p>Je, tayari una akaunti? <a href="#" id="show-login-form">Ingia hapa</a></p>
</form>