# Mini Trello

Минималистичный канбан-менеджер задач на **Laravel 13**, **Livewire 4** и **Flux UI**. Аналог Trello для личных проектов и небольших команд: доски, колонки, задачи с drag-and-drop и комментарии.

## Возможности

- **Канбан-доски** — создание, редактирование и удаление досок с описанием
- **Колонки по умолчанию** — «К выполнению», «В работе», «Готово»
- **Задачи** — заголовок, описание, перетаскивание между колонками
- **Совместный доступ** — приглашение участников на доску
- **Комментарии** — обсуждение задач
- **Авторизация** — регистрация, вход, двухфакторная аутентификация, passkeys
- **Тёмная тема** — встроенная поддержка через Flux

## Стек

| Компонент | Технология |
|-----------|------------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Livewire 4, Flux UI, Tailwind CSS 4 |
| Auth | Laravel Fortify |
| БД | SQLite (по умолчанию) |
| Сборка | Vite 8 |

## Быстрый старт

### Требования

- PHP 8.3+
- Composer
- Node.js 20+
- npm

### Установка

```bash
# Клонировать репозиторий и перейти в каталог
cd mini-trello

# Установить зависимости и настроить окружение
composer setup
```

Или вручную:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
```

### Демо-данные

Заполнить базу русскоязычными демо-данными:

```bash
php artisan db:seed
```

**Тестовые аккаунты** (пароль для всех: `password`):

| Email | Имя | Роль |
|-------|-----|------|
| `demo@mini-trello.test` | Алексей Смирнов | Владелец досок |
| `maria@mini-trello.test` | Мария Петрова | Участник доски «Разработка Mini Trello» |

После сидирования доступны 3 доски с задачами и комментариями.

### Запуск для разработки

```bash
composer dev
```

Команда одновременно запускает сервер (`localhost:8000`), очередь, логи и Vite.

Или по отдельности:

```bash
php artisan serve
npm run dev
```

Откройте [http://localhost:8000](http://localhost:8000).

## Структура проекта

```
app/
  Models/          Board, Column, Task, Comment, User
  Policies/        Политики доступа к доскам
database/
  factories/       Фабрики для тестов
  seeders/         DemoSeeder — русские демо-данные
resources/views/
  welcome.blade.php       Главная страница
  dashboard.blade.php     Дашборд
  pages/boards/           Список досок и канбан-доска
routes/
  web.php          Главная и дашборд
  boards.php       Маршруты досок
```

## Тесты

```bash
composer test
```

Запускает Pint (линтер) и Pest-тесты.

## Лицензия

MIT
