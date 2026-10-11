<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | REBANT BOMS</title>

    {{-- CSS --}}
    @vite(['resources/css/login.css'])

    <style>
        .alert-error {
            margin-bottom: 18px;
            padding: 12px 14px;
            background: #FDECEA;
            color: #C0392B;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    <main class="login-page">

        {{-- =========================
             LEFT SIDE
        ========================== --}}
        <section class="login-visual">

            <div class="visual-content">

                <div class="brand-badge">
                    REBANT BOMS
                </div>

                <h1>
                    Manage your business
                    <span>with ease.</span>
                </h1>

                <p>
                    Kelola pesanan, produksi, dan persediaan bisnis
                    REBANT dalam satu tempat yang lebih terorganisir.
                </p>

            </div>

            {{-- Illustration --}}
            <div class="login-illustration">
                <img
                    src="{{ asset('images/login-illustration.png') }}"
                    alt="REBANT Business Illustration"
                >
            </div>

            {{-- Background Decoration --}}
            <div class="circle circle-yellow"></div>
            <div class="circle circle-green"></div>

        </section>


        {{-- =========================
             RIGHT SIDE
        ========================== --}}
        <section class="login-section">

            <div class="login-container">

                {{-- BRAND --}}
                <div class="brand">

                    <div class="brand-logo">
                        <img
                            src="{{ asset('images/rebant-logo.png') }}"
                            alt="REBANT Logo"
                            class="logo"
                        >
                    </div>

                    <div class="brand-name">
                        <strong>REBANT</strong>
                        <span>BOMS</span>
                    </div>

                </div>


                {{-- LOGIN HEADING --}}
                <div class="login-heading">

                    <span class="eyebrow">
                        WELCOME BACK
                    </span>

                    <h2>
                        Login to your account
                    </h2>

                    <p>
                        Masukkan akun Anda untuk mengakses sistem REBANT.
                    </p>

                </div>


                {{-- =========================
                     LOGIN FORM
                ========================== --}}
                <form
                    id="loginForm"
                    method="POST"
                    action="{{ route('login.process') }}"
                >
                    @csrf

                    {{-- PESAN ERROR --}}
                    @if ($errors->any())
                        <div class="alert-error">
                            {{ $errors->first() }}
                        </div>
                    @endif


                    {{-- EMAIL --}}
                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <div class="input-box">

                            <span class="input-icon">

                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />

                                    <path
                                        d="M22 6L12 13L2 6"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email"
                                autocomplete="email"
                                required
                                autofocus
                            >

                        </div>

                    </div>


                    {{-- PASSWORD --}}
                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <div class="input-box">

                            <span class="input-icon">

                                <svg
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <rect
                                        x="4"
                                        y="10"
                                        width="16"
                                        height="11"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />

                                    <path
                                        d="M8 10V7C8 4.79 9.79 3 12 3C14.21 3 16 4.79 16 7V10"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>

                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >

                            {{-- Show / Hide Password --}}
                            <button
                                type="button"
                                class="password-toggle"
                                id="passwordToggle"
                                aria-label="Show password"
                            >

                                <svg
                                    id="eyeIcon"
                                    width="19"
                                    height="19"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M2 12C2 12 5.5 5 12 5C18.5 5 22 12 22 12C22 12 18.5 19 12 19C5.5 19 2 12 2 12Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="3"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                </svg>

                            </button>

                        </div>

                    </div>

                    {{-- LOGIN BUTTON --}}
                    <button
                        type="submit"
                        class="login-button"
                    >

                        <span>Login</span>

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M5 12H19"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                            <path
                                d="M13 6L19 12L13 18"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                    </button>

                </form>


                {{-- FOOTER --}}
                <div class="login-footer">
                    © 2026 REBANT Business Operation Management System
                </div>

            </div>

        </section>

    </main>


    {{-- =========================
         JAVASCRIPT
    ========================== --}}
    <script>
        const passwordInput  = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');

        passwordToggle.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';

            passwordInput.type = isHidden ? 'text' : 'password';

            passwordToggle.setAttribute(
                'aria-label',
                isHidden ? 'Hide password' : 'Show password'
            );
        });
    </script>

</body>

</html>