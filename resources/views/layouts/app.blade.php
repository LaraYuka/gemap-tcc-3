<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GEMAP') }}</title>

    <link rel="manifest" href="/manifest.json">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4F46E5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Gestão Materiais">

    <!-- Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Ícones para iOS -->
    <link rel="apple-touch-icon" href="/images/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/icons/icon-72x72.png">
    <link rel="apple-touch-icon" sizes="96x96" href="/images/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="128x128" href="/images/icons/icon-128x128.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/icons/icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/images/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="384x384" href="/images/icons/icon-384x384.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/images/icons/icon-512x512.png">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-72x72.png">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">GEMAP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button id="installButton" style="display: none;" class="dropdown-item">
                                    📱 Instalar App
                                    </button>
                                </li>
                                    <!-- Botão de DEBUG - remover depois -->

                                <li>

                                    <button id="debugButton" class="dropdown-item text-warning">
                                        🔍 Status PWA
                                    </button>

                                </li>

                                <!--
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a>
                                </li>
                            -->
                            <!--
                                <li><hr class="dropdown-divider"></li>
                            -->
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                    <!--
                        <i class="ri-h-1"></i>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrar</a>
                        </li>
                    -->
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
                <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            @if(Auth::user()->isAdmin())
                                <li class="nav-item mb-2">
                                    <h6 class="text-muted px-3">ADMINISTRADOR</h6>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                        🏠 Home
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                        👥 Usuários
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.materials.*') ? 'active' : '' }}" href="{{ route('admin.materials.index') }}">
                                        📦 Materiais
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.agendamentos.*') ? 'active' : '' }}" href="{{ route('admin.agendamentos.index') }}">
                                        📅 Agendamentos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.solicitacoes.*') ? 'active' : '' }}" href="{{ route('admin.solicitacoes.index') }}">
                                        📋 Solicitações
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.doacoes.*') ? 'active' : '' }}" href="{{ route('admin.doacoes.index') }}">
                                        🎁 Doações
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('relatorio.index') ? 'active' : '' }}" href="{{ route('relatorio.index') }}">
                                        📊 Gerar Relatórios
                                    </a>
                                </li>


                                <!-- NOVO: Histórico de Relatórios (APENAS ADMIN) -->
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.relatorios.*') ? 'active' : '' }}" href="{{ route('admin.relatorios.historico') }}">
                                        📜 Histórico de Relatórios
                                    </a>
                                </li>
                                <li class="nav-item"><hr></li>
                            @endif

                            @if(Auth::user()->isProfessor() || Auth::user()->isAdmin())
                                <li class="nav-item mb-2">
                                    <h6 class="text-muted px-3">PROFESSOR</h6>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('professor.dashboard') ? 'active' : '' }}" href="{{ route('professor.dashboard') }}">
                                        🏠 Home
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('professor.materials.*') ? 'active' : '' }}" href="{{ route('professor.materials.index') }}">
                                        🔍 Buscar Materiais
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('professor.agendamentos.*') ? 'active' : '' }}" href="{{ route('professor.agendamentos.index') }}">
                                        📅 Meus Agendamentos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('professor.solicitacoes.*') ? 'active' : '' }}" href="{{ route('professor.solicitacoes.index') }}">
                                        📝 Minhas Solicitações
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('relatorio.index') ? 'active' : '' }}" href="{{ route('relatorio.index') }}">
                                        📊 Gerar Relatórios
                                    </a>
                                </li>
                                <li class="nav-item"><hr></li>
                            @endif

                            @if(Auth::user()->isVisitante() || Auth::user()->isAdmin())
                                <li class="nav-item mb-2">
                                    <h6 class="text-muted px-3">VISITANTE</h6>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('visitante.dashboard') ? 'active' : '' }}" href="{{ route('visitante.dashboard') }}">
                                        🏠 Home
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('visitante.doacoes.*') ? 'active' : '' }}" href="{{ route('visitante.doacoes.index') }}">
                                        🎁 Minhas Doações
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('relatorio.index') ? 'active' : '' }}" href="{{ route('relatorio.index') }}">
                                        📊 Gerar Relatórios
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </nav>
            @endauth

            <main class="col-md-{{ auth()->check() ? '10' : '12' }} ms-sm-auto px-md-4 py-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>


    <!-- Registro do Service Worker -->
    <!--
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registrado com sucesso:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Falha ao registrar Service Worker:', error);
                    });
            });
        }
    </script>
-->

<!--
    <script>
        let deferredPrompt;
        const installButton = document.getElementById('installButton');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Previne o prompt automático
            e.preventDefault();
            // Armazena o evento
            deferredPrompt = e;
            // Mostra o botão
            installButton.style.display = 'block';
        });

        installButton.addEventListener('click', async () => {
            if (deferredPrompt) {
                // Mostra o prompt de instalação
                deferredPrompt.prompt();
                // Aguarda a escolha do usuário
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`Usuário ${outcome === 'accepted' ? 'aceitou' : 'recusou'} a instalação`);
                // Limpa o prompt
                deferredPrompt = null;
                // Esconde o botão
                installButton.style.display = 'none';
            }
        });

        // Esconde o botão após a instalação
        window.addEventListener('appinstalled', () => {
            console.log('PWA instalado com sucesso!');
            installButton.style.display = 'none';
        });
    </script>
    -->

    @auth
<script>

    if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registrado com sucesso:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Falha ao registrar Service Worker:', error);
                    });
            });
        }

    let deferredPrompt;
    const installButton = document.getElementById('installButton');
    const debugButton = document.getElementById('debugButton');

    // Debug: verificar status do PWA
    debugButton.addEventListener('click', async () => {
        let status = '📊 Status do PWA:\n\n';

        // Verifica Service Worker
        if ('serviceWorker' in navigator) {
            const registration = await navigator.serviceWorker.getRegistration();
            status += `✅ Service Worker: ${registration ? 'Registrado' : '❌ Não registrado'}\n`;
        } else {
            status += '❌ Service Worker não suportado\n';
        }

        // Verifica se já está instalado
        if (window.matchMedia('(display-mode: standalone)').matches) {
            status += '✅ App já está instalado!\n';
        } else {
            status += '⚠️ App não instalado ainda\n';
        }

        // Verifica se o prompt está disponível
        if (deferredPrompt) {
            status += '✅ Prompt de instalação disponível\n';
        } else {
            status += '⚠️ Prompt de instalação não disponível ainda\n';
            status += '   (pode já estar instalado ou aguardando critérios)\n';
        }

        alert(status);
        console.log(status);
    });

    // Captura o evento de instalação
    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('🎉 Evento beforeinstallprompt capturado!');
        e.preventDefault();
        deferredPrompt = e;
        installButton.style.display = 'block';
        console.log('✅ Botão de instalação ativado');
    });

    // Instalação
    installButton.addEventListener('click', async () => {
        if (deferredPrompt) {
            console.log('🚀 Mostrando prompt de instalação...');
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`👤 Usuário ${outcome === 'accepted' ? 'aceitou ✅' : 'recusou ❌'} a instalação`);

            if (outcome === 'accepted') {
                alert('App instalado com sucesso! 🎉');
            }

            deferredPrompt = null;
            installButton.style.display = 'none';
        }
    });

    // Após instalação
    window.addEventListener('appinstalled', () => {
        console.log('✅ PWA instalado com sucesso!');
        installButton.style.display = 'none';
        alert('App instalado! Você pode abri-lo pela tela inicial. 📱');
    });

    // Log inicial
    console.log('🔍 Script PWA carregado. Aguardando evento beforeinstallprompt...');
</script>
@endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






