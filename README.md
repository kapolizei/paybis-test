# CryptoRateTracker

Этот проект предназначен для отслеживания курсов криптовалют с помощью API CoinMarketCap и сохранения их в базу данных. Проект построен на Symfony.

## Установка

1. Клонируйте репозиторий:

    ```bash
    git clone https://github.com/kapolizei/paybis-test
    ```

2. Установите зависимости:

    ```bash
    composer install
    ```

3. Настройте файл `.env`:

    Создайте файл `.env` и добавьте ваш API ключ CoinMarketCap:

    ```dotenv
    API_KEY=ваш_ключ_апи
    DATABASE_URL="mysql://username:password@127.0.0.1:3306/dbname?serverVersion=5.7"
    ```

4. Создайте базу данных:

    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

## Использование

### Запросы для получения курсов криптовалют

#### Получение курса одной криптовалюты

Вы можете получить курс для одной криптовалюты, передав в запросе название криптовалюты и валюту котировки.

## GET /fetch/{symbol}/{quote} 
### GET /fetch/BTC/USD
```bash
{
  "BTC": {
    "id": 513,
    "pair": "BTC",
    "price": "94331.664720961",
    "quoteCurrency": "USD",
    "time": {
      "date": "2025-01-08 19:51:00.000000",
      "timezone_type": 2,
      "timezone": "Z"
    }
  }
}
```

#### Получение курсов нескольких криптовалют

Для получения курсов нескольких популярных криптовалют (например, BTC, ETH, XRP, SOL) используйте следующий запрос:

## GET /fetch/all/USD

```bash
{
    "BTC": {
        "id": 62,
        "pair": "BTC",
        "price": "95185.112149896",
        "quoteCurrency": "USD",
        "time": {
            "date": "2025-11-15 21:48:00.000000",
            "timezone_type": 2,
            "timezone": "Z"
        }
    },
    "ETH": {
        "id": 63,
        "pair": "ETH",
        "price": "3158.3860404151",
        "quoteCurrency": "USD",
        "time": {
            "date": "2025-11-15 21:48:00.000000",
            "timezone_type": 2,
            "timezone": "Z"
        }
    },
    "XRP": {
        "id": 65,
        "pair": "XRP",
        "price": "2.2206062671414",
        "quoteCurrency": "USD",
        "time": {
            "date": "2025-11-15 21:48:00.000000",
            "timezone_type": 2,
            "timezone": "Z"
        }
    },
    "SOL": {
        "id": 64,
        "pair": "SOL",
        "price": "138.99856189229",
        "quoteCurrency": "USD",
        "time": {
            "date": "2025-11-15 21:47:00.000000",
            "timezone_type": 2,
            "timezone": "Z"
        }
    }
}
```


## Структура проекта

- `src/Controller/ApiController.php` — Контроллер для API, который предоставляет курсы криптовалют.
- `src/Entity/CryptoRate.php` — Entity для хранения информации о курсах криптовалют.
- `src/Service/CryptoRateFetcher.php` — Сервис для получения данных о курсах криптовалют с API CoinMarketCap, и дальнейшее сохранение в бд.
