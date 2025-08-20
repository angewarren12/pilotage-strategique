<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Plateforme de Pilotage Stratégique')</title>
    
    @livewireStyles
    
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
            --success-green: #28A745;
            --warning-orange: #FFC107;
            --danger-red: #DC3545;
            --info-blue: #17A2B8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-gray);
            line-height: 1.6;
        }

        /* Header */
        .navbar {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
            height: 80px; /* Hauteur fixe pour la navbar */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--white) !important;
        }

        .navbar-nav .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .navbar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.3);
        }

        /* Sidebar */
        .sidebar {
            background: var(--white);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            height: calc(100vh - 80px);
            position: fixed;
            top: 80px; /* Commencer juste en dessous de la navbar */
            left: 0;
            width: 280px;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(0);
        }

        .sidebar.collapsed {
            width: 70px;
            transform: translateX(0);
        }

        .sidebar.hidden {
            transform: translateX(-100%);
            box-shadow: none;
        }

        /* Animation de la sidebar */
        .sidebar {
            will-change: transform;
        }

        /* Style du bouton toggle quand la sidebar est masquée */
        .sidebar-toggle.sidebar-hidden {
            background-color: rgba(255,255,255,0.1);
        }

        .sidebar-toggle.sidebar-hidden:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--light-gray);
            background: var(--very-light-green);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu .nav-item {
            margin: 0.25rem 1rem;
        }

        .sidebar-menu .nav-link {
            color: var(--dark-gray);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-menu .nav-link:hover {
            background-color: var(--very-light-green);
            color: var(--primary-green);
            transform: translateX(5px);
        }

        .sidebar-menu .nav-link.active {
            background-color: var(--primary-green);
            color: var(--white);
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            padding-top: calc(2rem + 80px); /* Ajouter l'espace pour la navbar */
            min-height: calc(100vh - 80px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        .main-content.full-width {
            margin-left: 0;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            background: var(--white);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
        }

        .btn-success {
            background-color: var(--success-green);
            border: none;
            border-radius: 8px;
        }

        .btn-warning {
            background-color: var(--warning-orange);
            border: none;
            border-radius: 8px;
        }

        .btn-danger {
            background-color: var(--danger-red);
            border: none;
            border-radius: 8px;
        }

        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .table thead th {
            background: var(--very-light-green);
            border: none;
            font-weight: 600;
            color: var(--dark-gray);
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid var(--light-gray);
        }

        .table tbody tr:hover {
            background-color: var(--very-light-green);
        }

        /* Progress Bars */
        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: var(--light-gray);
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-green) 0%, var(--light-green) 100%);
            border-radius: 4px;
        }

        /* Status Badges */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .badge-success {
            background-color: var(--success-green);
            color: var(--white);
        }

        .badge-warning {
            background-color: var(--warning-orange);
            color: var(--dark-gray);
        }

        .badge-danger {
            background-color: var(--danger-red);
            color: var(--white);
        }

        .badge-info {
            background-color: var(--info-blue);
            color: var(--white);
        }

        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, var(--white) 0%, var(--very-light-green) 100%);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .stats-card .icon {
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .stats-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }

        .stats-card .label {
            color: var(--gray);
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .navbar-brand {
                font-size: 1.25rem;
            }
        }

        /* Toggle Button */
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background-color: rgba(255,255,255,0.2);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background-color: var(--very-light-green);
            color: var(--success-green);
        }

        .alert-danger {
            background-color: #FFEBEE;
            color: var(--danger-red);
        }

        /* Forms */
        .form-control, .form-select {
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        /* Modal */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-weight: 600;
        }

        /* Modal Styles */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7) !important;
        }
        
        .modal-xl {
            max-width: 95% !important;
        }
        
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }
        
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            border-bottom: 2px solid var(--primary-green);
            border-radius: 15px 15px 0 0;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-footer {
            border-top: 2px solid var(--light-gray);
            border-radius: 0 0 15px 15px;
        }
        
        /* Card Styles */
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        
        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        /* Progress Bar Styles */
        .progress {
            border-radius: 10px;
            background-color: var(--light-gray);
        }
        
        .progress-bar {
            border-radius: 10px;
        }
        
        /* Badge Styles */
        .badge {
            border-radius: 6px;
            font-weight: 500;
        }
        
        /* Animation pour les modales */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translate(0, -50px);
        }
        
        .modal.show .modal-dialog {
            transform: none;
        }

        /* Styles pour la vue générale */
        .modal-fullscreen {
            max-width: 100% !important;
            margin: 0 !important;
        }
        
        .modal-fullscreen .modal-content {
            border-radius: 0;
            min-height: 100vh;
        }
        
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        
        /* Styles pour le modal de vue générale amélioré */
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
        }
        
        .modal-toolbar {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        #vueGeneraleModal .modal-content {
            border: none;
            border-radius: 0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        #vueGeneraleModal .modal-header {
            padding: 1.5rem 2rem;
        }
        
        #vueGeneraleModal .modal-toolbar {
            padding: 1rem 2rem;
        }
        
        #vueGeneraleModal .modal-footer {
            padding: 1rem 2rem;
        }
        
        /* Amélioration du tableau */
        #tableauVueGenerale {
            font-size: 0.9rem;
        }
        
        #tableauVueGenerale th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            border: none;
            padding: 1rem 0.5rem;
        }
        
        #tableauVueGenerale td {
            vertical-align: middle;
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        #tableauVueGenerale tr:hover {
            background-color: rgba(76, 175, 80, 0.05);
            transform: scale(1.001);
            transition: all 0.2s ease;
        }
        
        /* Styles pour les niveaux hiérarchiques */
        .hierarchy-level-1 {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);
            font-weight: 600;
        }
        
        .hierarchy-level-2 {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
            font-weight: 500;
        }
        
        .hierarchy-level-3 {
            background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
        }
        
        .hierarchy-level-4 {
            background: linear-gradient(135deg, rgba(13, 202, 240, 0.1) 0%, rgba(13, 202, 240, 0.05) 100%);
        }
        
        .hierarchy-level-5 {
            background: linear-gradient(135deg, rgba(248, 249, 250, 0.5) 0%, rgba(248, 249, 250, 0.3) 100%);
        }
        
        /* Indentation progressive */
        .indent-1 { padding-left: 1rem !important; }
        .indent-2 { padding-left: 2rem !important; }
        .indent-3 { padding-left: 3rem !important; }
        .indent-4 { padding-left: 4rem !important; }
        .indent-5 { padding-left: 5rem !important; }
        
        /* Badges améliorés */
        .badge-progress {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        /* Boutons d'action */
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        
        /* Vue cartes */
        .card-hierarchy {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .card-hierarchy:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .card-hierarchy .card-header {
            border-radius: 12px 12px 0 0;
            font-weight: 600;
        }
        
        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
            opacity: 0;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            #vueGeneraleModal .modal-header,
            #vueGeneraleModal .modal-toolbar,
            #vueGeneraleModal .modal-footer {
                padding: 1rem;
            }
            
            #tableauVueGenerale {
                font-size: 0.8rem;
            }
            
            .indent-1, .indent-2, .indent-3, .indent-4, .indent-5 {
                padding-left: 0.5rem !important;
            }
        }
        
        /* Ensure content doesn't overlap with navbar */
        body {
            padding-top: 80px; /* Fallback pour le body */
        }

        /* Styles pour l'animation de défilement du modal hiérarchique */
        .hierarchy-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        .hierarchy-view {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding: 1.5rem;
            overflow-y: auto;
            background: white;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transform: translateX(100%);
        }
        
        .hierarchy-view.active {
            transform: translateX(0);
        }
        
        .hierarchy-view.previous {
            transform: translateX(-100%);
        }
        
        .hierarchy-view.slide-in {
            animation: slideInFromRight 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }
        
        .hierarchy-view.slide-out {
            animation: slideOutToLeft 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }
        
        .hierarchy-view.slide-back-in {
            animation: slideInFromLeft 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }
        
        .hierarchy-view.slide-back-out {
            animation: slideOutToRight 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }
        
        @keyframes slideInFromRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutToLeft {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(-100%);
                opacity: 0;
            }
        }
        
        @keyframes slideInFromLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutToRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        /* Amélioration des cartes dans le modal hiérarchique */
        .hierarchy-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        
        .hierarchy-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .hierarchy-card .card-header {
            border-radius: 12px 12px 0 0;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }
        
        .hierarchy-card .card-body {
            padding: 1.5rem;
        }
        
        /* Boutons d'action améliorés */
        .btn-hierarchy-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
        }
        
        .btn-hierarchy-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-hierarchy-action.btn-view {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }
        
        .btn-hierarchy-action.btn-edit {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
        }
        
        .btn-hierarchy-action.btn-add {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        /* Indicateur de navigation */
        .navigation-indicator {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(0,0,0,0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #6c757d;
            z-index: 10;
        }
        
        /* Animation pour les éléments qui apparaissent */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive pour le modal hiérarchique */
        @media (max-width: 768px) {
            .hierarchy-view {
                padding: 1rem;
            }
            
            .hierarchy-card .card-body {
                padding: 1rem;
            }
            
            .btn-hierarchy-action {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <button class="sidebar-toggle me-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
                </button>

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-chart-line me-2"></i>
                Pilotage Stratégique
            </a>

            <div class="navbar-nav ms-auto">
                <!-- Centre de validations -->
                <div class="nav-item me-3">
                    <livewire:validation-center />
                </div>
                
                <!-- Centre de notifications -->
                <div class="nav-item me-3">
                    <livewire:notification-center />
                </div>
                
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Navigation</h6>
        </div>
        
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('piliers.*') ? 'active' : '' }}" href="{{ route('piliers.index') }}">
                        <i class="fas fa-layer-group"></i>
                        <span>Piliers</span>
                    </a>
                </li>
                
                {{-- Menus masqués pour tous les utilisateurs --}}
                {{-- 
                @if(Auth::user()->isAdminGeneral() || Auth::user()->isOwnerOS())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('objectifs-strategiques.*') ? 'active' : '' }}" href="{{ route('objectifs-strategiques.index') }}">
                        <i class="fas fa-bullseye"></i>
                        <span>Objectifs Stratégiques</span>
                    </a>
                </li>
                @endif
                
                @if(Auth::user()->isAdminGeneral() || Auth::user()->isOwnerOS() || Auth::user()->isOwnerPIL())
                                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('objectifs-specifiques.*') ? 'active' : '' }}" href="{{ route('objectifs-specifiques.index') }}">
                        <i class="fas fa-crosshairs"></i>
                        <span>Objectifs Spécifiques</span>
                    </a>
                                </li>
                            @endif

                @if(Auth::user()->isAdminGeneral() || Auth::user()->isOwnerOS() || Auth::user()->isOwnerPIL() || Auth::user()->isOwnerAction())
                                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('actions.*') ? 'active' : '' }}" href="{{ route('actions.index') }}">
                        <i class="fas fa-tasks"></i>
                        <span>Actions</span>
                    </a>
                                </li>
                            @endif
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sous-actions.*') ? 'active' : '' }}" href="{{ route('sous-actions.index') }}">
                        <i class="fas fa-list-check"></i>
                        <span>Sous-Actions</span>
                    </a>
                </li>
                --}}
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reporting') ? 'active' : '' }}" href="{{ route('reporting') }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reporting</span>
                    </a>
                </li>
                
                                {{-- Menu Utilisateurs masqué pour tous les utilisateurs --}}
                {{-- 
                @if(Auth::user()->isAdminGeneral())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                @endif
                --}}
                    </ul>
                </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

            @yield('content')
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i id="toastIcon" class="fas fa-info-circle me-2"></i>
                <strong id="toastTitle" class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div id="toastBody" class="toast-body">
                <!-- Le contenu sera défini par JavaScript -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Fonction pour afficher les toasts
        function showToast(type, message, title = null) {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastBody = document.getElementById('toastBody');
            
            // Définir les styles selon le type
            let iconClass, titleText, bgClass;
            
            switch(type) {
                case 'success':
                    iconClass = 'fas fa-check-circle text-success';
                    titleText = title || 'Succès';
                    bgClass = 'bg-success text-white';
                    break;
                case 'error':
                    iconClass = 'fas fa-exclamation-circle text-danger';
                    titleText = title || 'Erreur';
                    bgClass = 'bg-danger text-white';
                    break;
                case 'warning':
                    iconClass = 'fas fa-exclamation-triangle text-warning';
                    titleText = title || 'Attention';
                    bgClass = 'bg-warning text-dark';
                    break;
                case 'info':
                default:
                    iconClass = 'fas fa-info-circle text-info';
                    titleText = title || 'Information';
                    bgClass = 'bg-info text-white';
                    break;
            }
            
            // Mettre à jour le contenu
            toastIcon.className = iconClass;
            toastTitle.textContent = titleText;
            toastBody.textContent = message;
            
            // Appliquer la classe de couleur
            toast.className = `toast ${bgClass}`;
            
            // Afficher le toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
        
        // Fonction pour afficher un toast de chargement
        function showLoadingToast(message = 'Chargement en cours...') {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastBody = document.getElementById('toastBody');
            
            toastIcon.className = 'fas fa-spinner fa-spin text-primary';
            toastTitle.textContent = 'Chargement';
            toastBody.textContent = message;
            toast.className = 'toast bg-primary text-white';
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            return bsToast;
        }

        // Gestion de la sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            // Récupérer l'état de la sidebar depuis le localStorage
            const sidebarState = localStorage.getItem('sidebarState') || 'visible';
            
            // Appliquer l'état initial
            if (sidebarState === 'hidden') {
                sidebar.classList.add('hidden');
                mainContent.classList.add('full-width');
            }
            
            // Gérer le clic sur le bouton toggle
            sidebarToggle.addEventListener('click', function() {
                // Toggle de la sidebar
                if (sidebar.classList.contains('hidden')) {
                    // Afficher la sidebar
                    sidebar.classList.remove('hidden');
                    mainContent.classList.remove('full-width');
                    sidebarToggle.classList.remove('sidebar-hidden');
                    localStorage.setItem('sidebarState', 'visible');
                    
                    // Animation d'entrée fluide
                    sidebar.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    sidebar.style.transform = 'translateX(0)';
                    
                    // Changer l'icône avec animation
                    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
                    
                    // Ajouter une classe pour l'animation
                    sidebar.classList.add('sidebar-visible');
                } else {
                    // Masquer la sidebar
                    sidebar.classList.add('hidden');
                    mainContent.classList.add('full-width');
                    sidebarToggle.classList.add('sidebar-hidden');
                    localStorage.setItem('sidebarState', 'hidden');
                    
                    // Animation de sortie fluide
                    sidebar.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    sidebar.style.transform = 'translateX(-100%)';
                    
                    // Changer l'icône avec animation
                    sidebarToggle.innerHTML = '<i class="fas fa-chevron-right"></i>';
                    
                    // Retirer la classe d'animation
                    sidebar.classList.remove('sidebar-visible');
                }
            });
            
            // Gestion responsive
            function handleResize() {
                if (window.innerWidth <= 768) {
                    // Sur mobile, la sidebar est masquée par défaut
                    sidebar.classList.add('hidden');
                    mainContent.classList.add('full-width');
                    sidebarToggle.classList.add('sidebar-hidden');
                    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
                } else {
                    // Sur desktop, restaurer l'état sauvegardé
                    if (sidebarState === 'visible') {
                        sidebar.classList.remove('hidden');
                        mainContent.classList.remove('full-width');
                        sidebarToggle.classList.remove('sidebar-hidden');
                        sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
                    } else {
                        sidebar.classList.add('hidden');
                        mainContent.classList.add('full-width');
                        sidebarToggle.classList.add('sidebar-hidden');
                        sidebarToggle.innerHTML = '<i class="fas fa-chevron-right"></i>';
                    }
                }
            }
            
            // Écouter les changements de taille d'écran
            window.addEventListener('resize', handleResize);
            
            // Appliquer le comportement initial
            handleResize();
        });
    </script>

    @stack('scripts')
    
    @livewireScripts
</body>
</html>
