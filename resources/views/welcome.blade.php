<!DOCTYPE html>
<html lang="ru" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mini Trello — канбан для ваших задач</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-mesh-bg min-h-screen text-zinc-100 antialiased">
        <div class="landing-grid relative min-h-screen">
            <header class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-600 shadow-lg shadow-brand-600/30">
                        <svg class="size-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="5" height="18" rx="1" />
                            <rect x="10" y="3" width="5" height="12" rx="1" />
                            <rect x="17" y="3" width="5" height="15" rx="1" />
                        </svg>
                    </span>
                    <span class="text-lg font-semibold tracking-tight">Mini Trello</span>
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-500">
                                Дашборд
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-300 transition hover:text-white">
                                Войти
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-500">
                                    Регистрация
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="relative z-10 mx-auto grid max-w-6xl gap-12 px-6 pb-20 pt-8 lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-8 lg:pt-16">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 rounded-full border border-brand-500/30 bg-brand-500/10 px-4 py-1.5 text-sm text-brand-300">
                        <span class="size-2 rounded-full bg-brand-400 animate-pulse"></span>
                        Канбан без лишнего
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                            Задачи в порядке —
                            <span class="bg-gradient-to-r from-brand-300 to-amber-glow bg-clip-text text-transparent">доска за доской</span>
                        </h1>
                        <p class="max-w-lg text-lg leading-relaxed text-zinc-400">
                            Mini Trello помогает организовать проекты, спринты и личные дела. Перетаскивайте карточки, работайте в команде и держите всё под контролем.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3 font-medium text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-500">
                                Открыть дашборд
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3 font-medium text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-500">
                                    Начать бесплатно
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                </a>
                            @endif
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-zinc-600 px-6 py-3 font-medium text-zinc-300 transition hover:border-zinc-500 hover:text-white">
                                {{ Route::has('register') ? 'Уже есть аккаунт' : 'Войти' }}
                            </a>
                        @endauth
                    </div>

                    <ul class="grid gap-4 sm:grid-cols-3">
                        <li class="rounded-xl border border-zinc-800 bg-zinc-900/50 p-4">
                            <div class="mb-2 text-2xl">📋</div>
                            <p class="font-medium text-zinc-200">Доски</p>
                            <p class="mt-1 text-sm text-zinc-500">Неограниченное число проектов</p>
                        </li>
                        <li class="rounded-xl border border-zinc-800 bg-zinc-900/50 p-4">
                            <div class="mb-2 text-2xl">↔️</div>
                            <p class="font-medium text-zinc-200">Drag & Drop</p>
                            <p class="mt-1 text-sm text-zinc-500">Перетаскивание задач</p>
                        </li>
                        <li class="rounded-xl border border-zinc-800 bg-zinc-900/50 p-4">
                            <div class="mb-2 text-2xl">👥</div>
                            <p class="font-medium text-zinc-200">Команда</p>
                            <p class="mt-1 text-sm text-zinc-500">Совместная работа</p>
                        </li>
                    </ul>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 rounded-3xl bg-gradient-to-br from-brand-600/20 to-amber-glow/10 blur-2xl"></div>
                    <div class="relative rounded-2xl border border-white/10 bg-zinc-900/80 p-5 shadow-2xl backdrop-blur-xl sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-zinc-300">Разработка Mini Trello</p>
                                <p class="text-xs text-zinc-500">Спринт #4</p>
                            </div>
                            <span class="rounded-full bg-brand-500/20 px-3 py-1 text-xs font-medium text-brand-300">3 колонки</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="kanban-preview-col">
                                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">К выполнению</p>
                                <div class="kanban-preview-card">Настроить CI/CD</div>
                                <div class="kanban-preview-card">Написать README</div>
                                <div class="kanban-preview-card border-dashed border-white/20 bg-transparent text-zinc-500">+ задача</div>
                            </div>
                            <div class="kanban-preview-col ring-1 ring-brand-500/30">
                                <p class="text-xs font-semibold uppercase tracking-wider text-brand-300">В работе</p>
                                <div class="kanban-preview-card bg-brand-500/20 border-brand-500/30">Дизайн главной</div>
                                <div class="kanban-preview-card">Русское сидирование</div>
                            </div>
                            <div class="kanban-preview-col">
                                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Готово</p>
                                <div class="kanban-preview-card opacity-60 line-through">Модели данных</div>
                                <div class="kanban-preview-card opacity-60 line-through">Авторизация</div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="relative z-10 border-t border-zinc-800/80 py-6 text-center text-sm text-zinc-600">
                Mini Trello · Laravel + Livewire
            </footer>
        </div>
    </body>
</html>
