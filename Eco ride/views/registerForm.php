<section class="container-login">
    <div class="login-container">
        <h1 class="login__title">
            Créez votre compte
        </h1>
        <form class="login-form" method="POST" action="auth/register.php" onsubmit="return validateForm()">
            <!-- Full Name -->
            <div class="login-form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
                <span class="error-message" id="full_name_error"></span>
            </div>
            <!-- Email -->
            <div class="login-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <span class="error-message" id="email_error"></span>
            </div>
            <!-- Password -->
            <div class="login-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error-message" id="password_error"></span>
            </div>
            <!-- Confirm Password -->
            <div class="login-form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
                <span class="error-message" id="confirm_password_error"></span>
            </div>
            <button type="submit" class="login-btn">Register</button>
            <!-- Already have an account -->
             </form>

        <p class="notLogin">
            Vous avez déjà un compte ?<br> <a href="login.php" class="register__link">Connectez-vous</a>
        </p>
    </div>

</section>
