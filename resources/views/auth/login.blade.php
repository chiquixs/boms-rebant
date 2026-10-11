<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | REBANT BOMS</title>

    @vite(['resources/css/login.css'])
</head>

<body>

<div class="login-page">

    <!-- LEFT -->
    <section class="login-visual">

        <div class="visual-content">
            <div class="brand-badge">REBANT BOMS</div>

            <h1>
                Manage your business<br>
                <span>with ease.</span>
            </h1>

            <p>
                Kelola pesanan, produksi, dan persediaan
                bisnis REBANT dalam satu tempat.
            </p>
        </div>

        <div class="wall-decoration">

            <div class="frame frame-small">
                <div class="leaf">🌿</div>
            </div>

            <div class="frame frame-main">
                <span>REBANT</span>
                <small>HOME DECOR</small>
            </div>

            <div class="frame frame-small">
                <div class="sun">☀</div>
            </div>

            <div class="table-decoration">
                <div class="vase">
                    <div class="plant">🌱</div>
                </div>

                <div class="books"></div>
            </div>

        </div>

        <div class="circle circle-yellow"></div>
        <div class="circle circle-green"></div>

    </section>


    <!-- RIGHT -->
    <section class="login-section">

        <div class="login-container">

            <div class="brand">
                <div class="brand-logo">R</div>

                <div class="brand-name">
                    <strong>REBANT</strong>
                    <span>BOMS</span>
                </div>
            </div>

            <div class="login-heading">
                <span class="eyebrow">WELCOME BACK</span>

                <h2>Login to your account</h2>

                <p>
                    Masukkan akun Anda untuk mengakses sistem REBANT.
                </p>
            </div>


            <!-- STATIC FRONTEND FORM -->
            <form>

                <div class="form-group">
                    <label>Email</label>

                    <div class="input-box">
                        <span class="input-icon">✉</span>

                        <input
                            type="email"
                            placeholder="Masukkan email"
                        >
                    </div>
                </div>


                <div class="form-group">
                    <label>Password</label>

                    <div class="input-box">
                        <span class="input-icon">⌑</span>

                        <input
                            type="password"
                            id="password"
                            placeholder="Masukkan password"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                        >
                            ◉
                        </button>
                    </div>
                </div>


                <div class="form-options">

                    <label class="remember">
                        <input type="checkbox">
                        <span>Remember me</span>
                    </label>

                    <a href="#">Forgot password?</a>

                </div>


                <button
                    type="button"
                    class="login-button"
                >
                    Login
                    <span>→</span>
                </button>

            </form>


            <div class="login-footer">
                © 2026 REBANT Business Operation Management System
            </div>

        </div>

    </section>

</div>


<script>
function togglePassword() {
    const password = document.getElementById('password');

    password.type =
        password.type === 'password'
            ? 'text'
            : 'password';
}
</script>

</body>
</html>