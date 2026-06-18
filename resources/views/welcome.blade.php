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
        <div class="relative flex min-h-screen flex-col">
            <header class="sticky top-0 z-20 border-b border-white/5 bg-zinc-950/60 backdrop-blur-xl">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-white">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="5" height="18" rx="1" />
                                <rect x="10" y="3" width="5" height="12" rx="1" />
                                <rect x="17" y="3" width="5" height="15" rx="1" />
                            </svg>
                        </span>
                        <span class="font-semibold tracking-tight">Mini Trello</span>
                    </a>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-2">
                            @auth
                                <a href="{{ route('dashboard') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-500">
                                    Дашборд
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-400 transition hover:text-white">
                                        Войти
                                    </a>
                                    <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-500">
                                        Регистрация
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </header>

            <main class="relative z-10 mx-auto w-full max-w-6xl flex-1 px-6 lg:px-8">
                <section class="grid items-center gap-12 py-16 lg:grid-cols-2 lg:gap-16 lg:py-24">
                    <div class="space-y-8">
                        <div class="space-y-5">
                            <p class="text-sm font-medium tracking-wide text-brand-400">Канбан без лишнего</p>

                            <h1 class="text-4xl font-bold leading-[1.1] tracking-tight sm:text-5xl">
                                Задачи в порядке
                                <span class="mt-1 block bg-gradient-to-r from-brand-300 to-brand-500 bg-clip-text text-transparent">
                                    доска за доской
                                </span>
                            </h1>

                            <p class="max-w-md text-base leading-relaxed text-zinc-400 sm:text-lg">
                                Организуйте проекты, спринты и личные дела. Перетаскивайте карточки между колонками и держите всё под контролем.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-500">
                                    Открыть дашборд
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-500">
                                        Начать бесплатно
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                    </a>
                                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-zinc-700 px-5 py-2.5 text-sm font-medium text-zinc-300 transition hover:border-zinc-600 hover:text-white">
                                        Уже есть аккаунт
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-500">
                                        Войти
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="landing-preview">
                        <div class="landing-preview-window">
                            <div class="flex items-center justify-between border-b border-white/5 px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-zinc-200">Разработка Mini Trello</p>
                                    <p class="text-xs text-zinc-500">Спринт #4</p>
                                </div>
                                <span class="rounded-md bg-brand-500/15 px-2 py-0.5 text-xs font-medium text-brand-300">3 колонки</span>
                            </div>

                            <div class="grid grid-cols-3 gap-2.5 p-4">
                                <div class="kanban-preview-col">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">К выполнению</p>
                                    <div class="kanban-preview-card">Настроить CI/CD</div>
                                    <div class="kanban-preview-card">Написать README</div>
                                    <div class="kanban-preview-card border-dashed border-zinc-700 bg-transparent text-zinc-600">+ задача</div>
                                </div>
                                <div class="kanban-preview-col ring-1 ring-brand-500/25">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-brand-400">В работе</p>
                                    <div class="kanban-preview-card bg-brand-500/15 border-brand-500/20 text-brand-100">Дизайн главной</div>
                                    <div class="kanban-preview-card">Русское сидирование</div>
                                </div>
                                <div class="kanban-preview-col">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500">Готово</p>
                                    <div class="kanban-preview-card opacity-50 line-through">Модели данных</div>
                                    <div class="kanban-preview-card opacity-50 line-through">Авторизация</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-3 pb-20 sm:grid-cols-3">
                    <div class="landing-feature">
                        <span class="landing-feature-icon">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <rect x="3" y="3" width="5" height="18" rx="1" />
                                <rect x="10" y="3" width="5" height="12" rx="1" />
                                <rect x="17" y="3" width="5" height="15" rx="1" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-medium text-zinc-200">Доски</p>
                            <p class="mt-0.5 text-sm text-zinc-500">Неограниченное число проектов</p>
                        </div>
                    </div>

                    <div class="landing-feature">
                        <span class="landing-feature-icon text-violet-400 bg-violet-500/10">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-medium text-zinc-200">Drag & Drop</p>
                            <p class="mt-0.5 text-sm text-zinc-500">Перетаскивание задач</p>
                        </div>
                    </div>

                    <div class="landing-feature">
                        <span class="landing-feature-icon text-amber-400 bg-amber-500/10">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-medium text-zinc-200">Команда</p>
                            <p class="mt-0.5 text-sm text-zinc-500">Совместная работа</p>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-white/5 py-6 text-center text-sm text-zinc-600">
                Mini Trello · Laravel + Livewire
            </footer>
        </div>
    </body>
</html>
