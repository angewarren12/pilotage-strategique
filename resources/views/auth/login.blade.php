<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - Plateforme de Pilotage Stratégique</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #4CAF50;
            --light-green: #81C784;
            --lighter-green: #A5D6A7;
            --very-light-green: #C8E6C9;
            --white: #FFFFFF;
            --light-gray: #F8F9FA;
            --gray: #6C757D;
            --dark-gray: #343A40;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 600px;
        }

        .login-sidebar {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex: 1;
        }

        .login-form {
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }

        .login-logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .login-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .feature-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .feature-list li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .feature-list i {
            font-size: 1.2rem;
            color: var(--very-light-green);
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--gray);
            margin-bottom: 2rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid var(--light-gray);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .form-floating label {
            padding: 1rem 1.25rem;
            color: var(--gray);
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--primary-green);
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }

        .form-label {
            color: var(--gray);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--white);
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
            color: var(--white);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: #FFEBEE;
            color: #DC3545;
        }

        .alert-success {
            background-color: var(--very-light-green);
            color: var(--primary-green);
        }

        .input-group-text {
            background: var(--light-gray);
            border: 2px solid var(--light-gray);
            border-radius: 12px 0 0 12px;
            color: var(--gray);
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .password-toggle {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            background-color: var(--light-gray);
            color: var(--primary-green);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid var(--light-gray);
            border-radius: 6px;
        }

        .form-check-input:checked {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .form-check-label {
            color: var(--gray);
            font-weight: 500;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 1rem;
        }

        .forgot-password a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--light-green);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--gray);
        }

        .login-footer a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            color: var(--light-green);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .login-sidebar {
                padding: 2rem 1.5rem;
            }
            
            .login-form {
                padding: 2rem 1.5rem;
            }
            
            .login-logo {
                font-size: 2rem;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
        }

        /* Animation d'entrée */
        .login-container {
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Effet de focus amélioré */
        .form-floating > .form-control:focus {
            transform: scale(1.02);
        }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top: 2px solid var(--white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Sidebar avec informations -->
        <div class="login-sidebar">
            <div class="login-logo">
                <i class="fas fa-chart-line me-2"></i>
                Pilotage Stratégique
            </div>
            <div class="login-subtitle">
                Plateforme de gestion et suivi des objectifs stratégiques
            </div>
            <ul class="feature-list">
                <li>
                    <i class="fas fa-sitemap"></i>
                    <span>Gestion hiérarchique complète</span>
                </li>
                <li>
                    <i class="fas fa-chart-bar"></i>
                    <span>Suivi en temps réel</span>
                </li>
                <li>
                    <i class="fas fa-users"></i>
                    <span>Gestion des permissions</span>
                </li>
                <li>
                    <i class="fas fa-bell"></i>
                    <span>Notifications automatiques</span>
                </li>
                <li>
                    <i class="fas fa-file-export"></i>
                    <span>Reporting avancé</span>
                </li>
            </ul>
        </div>

        <!-- Formulaire de connexion -->
        <div class="login-form">
            <div class="form-title">Connexion</div>
            <div class="form-subtitle">Accédez à votre espace de pilotage</div>

            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erreur de connexion :</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                <div class="form-floating">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" 
                           placeholder="nom@exemple.com" required autofocus>
                    <label for="email">
                        <i class="fas fa-envelope me-2"></i>Adresse email
                    </label>
                                @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Mot de passe
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Entrez votre mot de passe" required>
                        <button type="button" class="input-group-text password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-me">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                        Se souvenir de moi
                                    </label>
                        </div>

                <div class="forgot-password">
                                @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            <i class="fas fa-key me-1"></i>Mot de passe oublié ?
                                    </a>
                                @endif
                </div>

                <button type="submit" class="btn btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Se connecter
                </button>
            </form>

            <div class="login-footer">
                <p>Plateforme de Pilotage Stratégique &copy; {{ date('Y') }}</p>
                <p>Développé avec <i class="fas fa-heart text-danger"></i> et Laravel</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            loginBtn.classList.add('loading');
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Connexion en cours...';
        });

        // Auto-focus on email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        // Smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-floating, .btn-login, .alert');
            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    element.style.transition = 'all 0.6s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
