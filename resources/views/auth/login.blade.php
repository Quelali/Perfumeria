<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Productos</title>
    @vite(['resources/css/app.css'])
</head>
<body class="login-body">
    @php
        $error = session('error', request()->query('error', ''));
        $success = session('success', '');
        if ($error && str_starts_with($error, 'success:')) {
            $success = substr($error, 8);
            $error = '';
        }
    @endphp

    <div class="login-container">
        <div class="login-box">
            <h1>Sistema de Productos</h1>

            @if($error)
                <div class="alert alert-error">{{ $error }}</div>
            @endif

            @if($success)
                <div class="alert alert-success">{{ $success }}</div>
            @endif

            <div class="login-toggle">
                <button type="button" class="toggle-btn active" id="loginBtn">Iniciar Sesión</button>
                <button type="button" class="toggle-btn" id="registerBtn">Registrarse</button>
            </div>

            <form id="loginForm" action="{{ route('login.submit') }}" method="POST" class="form-auth">
                @csrf
                <div class="form-group">
                    <label for="login-user">Email:</label>
                    <input type="email" id="login-user" name="usuario" placeholder="Ingrese su email" required>
                </div>
                <div class="form-group">
                    <label for="login-pass">Contraseña:</label>
                    <input type="password" id="login-pass" name="password" placeholder="Ingrese su contraseña" required>
                </div>
                <button type="submit" class="btn btn-login">Iniciar Sesión</button>
            </form>

            <form id="registerForm" action="{{ route('register.submit') }}" method="POST" class="form-auth hidden">
                @csrf
                <div class="form-group">
                    <label for="reg-email">Email:</label>
                    <input type="email" id="reg-email" name="email" placeholder="Ingrese su email" required>
                </div>
                <div class="form-group">
                    <label for="reg-nombre">Nombre de Usuario:</label>
                    <input type="text" id="reg-nombre" name="nombre_usuario" placeholder="Ingrese su nombre" required>
                </div>
                <div class="form-group">
                    <label for="reg-pass">Contraseña:</label>
                    <input type="password" id="reg-pass" name="password" placeholder="Ingrese una contraseña" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="reg-pass-confirm">Confirmar Contraseña:</label>
                    <input type="password" id="reg-pass-confirm" name="password_confirmation" placeholder="Confirme su contraseña" required minlength="6">
                </div>
                <button type="submit" class="btn btn-login">Registrarse</button>
            </form>
        </div>
    </div>

    <script>
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        loginBtn.addEventListener('click', () => {
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
            loginBtn.classList.add('active');
            registerBtn.classList.remove('active');
        });

        registerBtn.addEventListener('click', () => {
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
            registerBtn.classList.add('active');
            loginBtn.classList.remove('active');
        });

        registerForm.addEventListener('submit', function(e) {
            const pass = document.getElementById('reg-pass').value;
            const passConfirm = document.getElementById('reg-pass-confirm').value;
            if (pass !== passConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>
