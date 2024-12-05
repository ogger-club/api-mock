# ogger-club/api-mock

A rudimentary API mock project. 

> An API mock is a web service that simulates a real, functional service
 
Initial use case: Simulate Discogs API rate limiting.

## Setup

- Requires: [DDEV](https://ddev.com/)

```shell
# Start project.
ddev start
# Install dependencies
ddev composer update
```

### Standalone

If using DDEV is not an option, the project can also be used standalone.

Requirements:

- PHP >= 8.3
- Composer
- Symfony CLI

```shell
# Install dependencies
composer update
# One time setup
symfony server:ca:install
# Start project.
symfony serve
# Check CLI output for the base URL.
``` 

---

## Usage

Base URL: `https://api-mock.ddev.site`

If not using DDEV, check CLI output to get the exact URL.

Perform requests to the mock API and check the logs in the directory '/var/logs/{DATE}'.

Each request is logged in the following format:

`[timestamp] environment client_ip route parameters response_code response_headers`

Example for Discogs API release endpoint:

```text
[20241022.215709.254326] debug 127.0.0.1 api_discogs_release 1 200 {"Content-Type":"application\/json","x-discogs-ratelimit":60,"x-discogs-ratelimit-remaining":59,"x-discogs-ratelimit-used":1}
[20241022.215725.788502] debug 127.0.0.1 api_discogs_release 2 200 {"Content-Type":"application\/json","x-discogs-ratelimit":60,"x-discogs-ratelimit-remaining":58,"x-discogs-ratelimit-used":2}
[20241022.215728.989264] debug 127.0.0.1 api_discogs_release 3 200 {"Content-Type":"application\/json","x-discogs-ratelimit":60,"x-discogs-ratelimit-remaining":57,"x-discogs-ratelimit-used":3}
```


### Discogs API

#### Endpoints

##### `/api/discogs/releases/{releaseId}`

- input: any number
- response: always returns the data for release id 1;

###### Example

```text
> GET /api/discogs/releases/1 HTTP/2
Host: api-mock.ddev.site
Accept-Encoding: deflate, gzip, br
Accept: application/vnd.discogs.v2.discogs+json
Authorization: Discogs token=CAN_BE_ANYTHING
User-Agent: CAN_BE_ANYTHING
Content-Length: 0

< HTTP/2 200 
< cache-control: no-cache, private
< content-type: application/json
< date: Fri, 25 Oct 2024 18:07:03 GMT
< server: Apache/2.4.62 (Debian)
< vary: Authorization
< x-discogs-ratelimit: 60
< x-discogs-ratelimit-remaining: 59
< x-discogs-ratelimit-used: 1
< x-robots-tag: noindex
< 
```

#### Customize rate limit

Default rate limit: 60 requests per minute. 

In order to customize it, create `.env.local` with contents:

```text
RATE_LIMIT_TOTAL_DISCOGS_API=60
```

Where `60` is the total number to requests allowed per minute.

---

## Development

```shell
# run code validation
ddev composer check
```

## TODO

- [ ] Solve `phan` errors;

