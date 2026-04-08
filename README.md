# Events CSV App

## Data

CSV file is located in `/data/orders.csv` and can be replaced if needed.

## Run with Docker

```bash
./run.sh
```

Alternatively:

```bash
docker compose up --build
```

Then open:

[http://localhost:8000](http://localhost:8000)

Note: Composer dependencies are installed during the Docker image build.

### Run with custom port

```bash
PORT=8080 docker compose up
```

Then open:

[http://localhost:8080](http://localhost:8080)

### Run Tests:

```bash
docker compose --profile test run --rm tests
```

## Run Locally (without Docker)

This project requires PHP >= 8.5. If your local PHP version is older, run tests using Docker (see above).

1. Install dependencies:

```bash
composer install
```

2. Run tests:

```bash
composer test
```

3. Run full verification (metadata + tests + code style):

```bash
composer verify
```

4. Start the application:

```bash
php -S localhost:8000 -t public
```
