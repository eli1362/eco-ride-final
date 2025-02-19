<section class="container-login">
    <div class="login-container">
        <h2 class="login__title">insérez votre email et votre mot de passe !!</h2>

        <form class="login-form" method="POST" action="auth/login.php">
            <div class="login-form-group">

                <label for="email"> Email </label>
                <input type="email" name="email" id="email" placeholder="Email" required>

            </div>
            <div class="login-form-group">

                <label for="password">Password </label>

                <input type="password" name="password" id="password" placeholder="Password" required>

            </div>
            <div class="login-form-options">
                <label class="login-checkbox-container">
                    <input type="checkbox" id="remember-me" name="remember-me">
                    <span class="login-checkmark"></span>
                    Remember me
                </label>
                <a href="forgotPassword.html" class="forgot-password">Forgot password</a>
            </div>

            <button type="submit" class="login-btn">Login</button>

        </form>
        <p class="notLogin">
            Vous n'êtes pas encore membre ?<br> <a href="register.php" class="register__link">Inscrivez-vous</a>
        </p>
    </div>
</section>
