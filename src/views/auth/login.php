<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Iniciar sesión - UnionDental</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: #172033;
        }

        .login-page {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                radial-gradient(
                    circle at center,
                    #c7e5ff 0%,
                    #dcedfb 45%,
                    #f5faff 100%
                );
        }

        .login-container {
            width: 100%;
            max-width: 360px;
            padding: 30px;
        }

        .logo-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 55px;
        }

        .tooth {
            width: 64px;
            height: 64px;
        }

        h1 {
            margin: 0 0 28px;
            font-size: 28px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            color: #475569;
        }

        input {
            width: 100%;
            height: 44px;
            border: 0;
            border-radius: 4px;
            padding: 0 13px;
            font-size: 15px;

            background: rgba(167, 207, 239, 0.55);
            color: #172033;
        }

        input:focus {
            outline: 2px solid #67aefb;
            background: rgba(180, 218, 247, 0.70);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            padding: 0;
            background: transparent;
            cursor: pointer;
            color: #64748b;
            font-size: 18px;
        }

        .btn-login {
            width: 100%;
            height: 44px;
            margin-top: 6px;

            border: 0;
            border-radius: 4px;

            background: #0878f9;
            color: white;

            font-size: 15px;
            font-weight: 600;

            cursor: pointer;
        }

        .btn-login:hover {
            background: #006ce4;
        }

        .error {
            margin-bottom: 20px;
            padding: 12px 14px;

            background: #fee2e2;
            color: #991b1b;

            border-radius: 6px;
            font-size: 14px;
        }

        @media (max-width: 500px) {
            .login-container {
                max-width: 330px;
                padding: 20px;
            }

            .logo-icon {
                margin-bottom: 40px;
            }
        }
    </style>
</head>

<body>

<div class="login-page">

    <div class="login-container">

        <div class="logo-icon">

            <svg
                class="tooth"
                viewBox="0 0 100 100"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="
                        M50 17
                        C39 8 20 12 15 28
                        C10 46 18 70 27 82
                        C32 89 38 87 41 77
                        L45 63
                        C47 56 53 56 55 63
                        L59 77
                        C62 87 68 89 73 82
                        C82 70 90 46 85 28
                        C80 12 61 8 50 17Z
                    "
                    stroke="#075bb5"
                    stroke-width="3"
                />

                <path
                    d="M50 17 C57 17 64 20 69 27"
                    stroke="#075bb5"
                    stroke-width="3"
                    stroke-linecap="round"
                />
            </svg>

        </div>

        <h1>Iniciar sesión</h1>

        <?php if (!empty($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            action="?url=auth/autenticar"
        >

            <div class="form-group">

                <label for="email">
                    Usuario
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    placeholder="admin@clinicadental.local"
                    autocomplete="username"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Contraseña
                </label>

                <div class="password-wrapper">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword"
                        aria-label="Mostrar contraseña"
                    >
                        ◉
                    </button>

                </div>

            </div>

            <button
                type="submit"
                class="btn-login"
            >
                Ingresar
            </button>

        </form>

    </div>

</div>

<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function () {

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }

    });
</script>

</body>
</html>