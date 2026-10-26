# ogger-club/api-mock

A Symfony web application.

Mock of the Discogs API that applies the same rate limiting headers and logs every request, for testing how a client handles rate limits.

- Requires: PHP 8.4+, [ddev](https://ddev.com/) (or Composer and the Symfony CLI)
- Uses: `webservco/log` for the request log, Symfony RateLimiter and Lock

---

## Installation

```shell
# Start project.
ddev start
# Install dependencies.
ddev c u
```

The base URL is `https://api-mock.ddev.site`.

### Without ddev

```shell
composer update
# One time setup.
symfony server:ca:install
# The base URL is in the output.
symfony serve
```

---

## Usage

Point the client's Discogs API base URL at the mock and send requests as usual. Authorization and user agent headers are accepted but not checked.

```text
> GET /api/discogs/releases/1 HTTP/2
> Host: api-mock.ddev.site
> Accept: application/vnd.discogs.v2.discogs+json
> Authorization: Discogs token=ANYTHING

< HTTP/2 200
< content-type: application/json
< x-discogs-ratelimit: 60
< x-discogs-ratelimit-remaining: 59
< x-discogs-ratelimit-used: 1
```

### Endpoints

| Endpoint | Method | Response |
| --- | --- | --- |
| `/api/discogs/releases/{releaseId}` | `GET` | The data of release 1 for any numeric ID, from [resources/api/discogs/releases/1.json](resources/api/discogs/releases/1.json). |

When the limit is reached, the response is `429 Too Many Requests` with an empty body.

### Request log

Each request writes a line to `var/log/{YYYYMMDD}/api_discogs_release.log`:

```text
[20241022.215709.254326] debug 127.0.0.1 api_discogs_release 1 200 {"content-type":"application\/json","x-discogs-ratelimit":60,"x-discogs-ratelimit-remaining":59,"x-discogs-ratelimit-used":1}
```

The fields are the log ID, log level, client IP, route, release ID, response status and response headers.

---

## Configuration

Keys read from `.env`. Override them in `.env.local`.

```ini
# Requests allowed per minute, per client IP.
RATE_LIMIT_TOTAL_DISCOGS_API=60
```

---

## Notes

The limiter uses a sliding window of 60 seconds per client IP. Its state lives in the default Symfony cache (files in `var/cache`), with `flock` locks.

---

## Development

```shell
# Run all validations.
ddev c c
# Fix code style.
ddev c fix:phpcs
```

---

## License

[MIT](LICENSE)
