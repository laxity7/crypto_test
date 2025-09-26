# API курсов криптовалют на Symfony

Это веб-приложение предоставляет API для получения курсов криптовалют (EUR к BTC, ETH, LTC), используя данные из публичного API Binance.

## Технологический стек

- **Бэкенд:** PHP 8.4 / Symfony 7
- **База данных:** MySQL 8.0
- **Окружение:** Docker, Docker Compose
- **Менеджер процессов:** Supervisor
- **Веб-сервер:** Nginx

## Начало работы

### Установка и первый запуск

Выполните команда:

       make init

Команда сделает все необходимые шаги по установке и запуску проекта. После завершения установки проект будет доступен по адресу `http://localhost:8000`, а фоновый процесс сбора данных уже будет работать.

Для просмотра веб-интерфейса Supervisor:

- **URL:** `http://localhost:9001`
- **Логин:** `user`
- **Пароль:** `123`

### Конфигурация

Валютные пары можно настроить в файле `[currency_pairs.yaml](src/config/packages/currency_pairs.yaml)`.
Интервал сбора данных можно настроить в файле `[scheduler.yaml](src/config/packages/scheduler.yaml)`.

## API Эндпоинты

### 1. Получить курсы за последние 24 часа

**Запрос:**
`GET /api/rates/last-24h?pair=EUR/BTC`

- `pair` (обязательный): Валютная пара. Доступные значения: `EUR/BTC`, `EUR/ETH`, `EUR/LTC`.

**Пример `curl`:**

    curl -X GET "http://localhost:8000/api/rates/last-24h?pair=EUR/BTC"

**Пример успешного ответа (200 OK):**

    {
      "data": [
        {
          "price": "62100.5",
          "timestamp": "2025-09-25 12:40:00"
        },
        {
          "price": "62105.75",
          "timestamp": "2025-09-25 12:45:00"
        }
      ]
    }

### 2. Получить курсы за конкретный день

**Запрос:**
`GET /api/rates/day?pair=EUR/BTC&date=YYYY-MM-DD`

- `pair` (обязательный): Валютная пара.
- `date` (обязательный): Дата в формате `YYYY-MM-DD`.

**Пример `curl`:**

    curl -X GET "http://localhost:8000/api/rates/day?pair=EUR/ETH&date=2025-09-25"

### Обработка ошибок (Пример)

При неверном запросе API вернет ошибку в формате JSON.

**Запрос без параметра `pair`:**

    curl -X GET "http://localhost:8000/api/rates/last-24h"

**Ответ (400 Bad Request):**

    {
        "error": "Query parameter \"pair\" is required.",
        "code": 400
    }

Запустить запросы можно в файле `requests.http`