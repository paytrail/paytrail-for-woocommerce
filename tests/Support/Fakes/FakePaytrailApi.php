<?php

declare(strict_types=1);

namespace Tests\Support\Fakes;

use Paytrail\SDK\Exception\ClientException;
use Paytrail\SDK\Exception\RequestException;
use Paytrail\SDK\Response\CurlResponse;
use Paytrail\SDK\Util\Signature;

/**
 * Stands in for the Paytrail SDK's `RequestClient`, swapped in by
 * `CanInterceptPaytrailApi`. The SDK uses Guzzle or curl rather than
 * `wp_remote_request()`, so `pre_http_request` never sees any of its traffic.
 */
final class FakePaytrailApi
{
    /** @var int */
    private $merchantId;

    /** @var string */
    private $secretKey;

    /**
     * Every intercepted request, in order.
     *
     * @var array<int, array<string, mixed>>
     */
    private $requests = [];

    /**
     * Responses queued by willRespondWith() and friends, consumed in order.
     *
     * @var array<int, array<string, mixed>>
     */
    private $queued = [];

    public function __construct(int $merchantId, string $secretKey)
    {
        $this->useCredentials($merchantId, $secretKey);
    }

    /**
     * Signs later responses as another merchant. Called when the gateway is rebuilt, so
     * a settings change does not cost the recording and the queue.
     */
    public function useCredentials(int $merchantId, string $secretKey): void
    {
        $this->merchantId = $merchantId;
        $this->secretKey  = $secretKey;
    }

    /**
     * The SDK's entry point. Records the request and answers with the next queued
     * response that matches its URI.
     *
     * @param array<string, mixed> $options     Guzzle-style request options.
     * @param bool                 $formRequest Set for the add-card form request.
     * @return CurlResponse
     * @throws ClientException|RequestException
     */
    public function request(string $method, string $uri, array $options, bool $formRequest = false)
    {
        $method = strtoupper($method);
        $body   = $options['body'] ?? null;

        $this->requests[] = [
            'method'      => $method,
            'uri'         => $uri,
            'url'         => $this->urlOf($uri, $options),
            'query'       => $options['query'] ?? [],
            'headers'     => $options['headers'] ?? [],
            'body'        => $body,
            'json'        => $this->decodeBody($body),
            'formRequest' => $formRequest,
        ];

        $index = $this->matchQueued($uri);

        if (null !== $index) {
            $queued = $this->queued[$index];
            unset($this->queued[$index]);

            return $this->answer($queued, $method);
        }

        throw new RequestException(sprintf(
            'The Paytrail API is not reachable from this suite. Queue a response with willRespondWith() if this call is expected. Request: %s %s',
            $method,
            $uri
        ));
    }

    /**
     * Queues a successful response. A null `$uriContains` answers the next request
     * whatever its endpoint.
     *
     * @param array<string, mixed>|array<int, mixed> $body    Encoded as the response body.
     * @param array<string, string>                  $headers Extra response headers.
     */
    public function willRespondWith(array $body, int $status = 200, ?string $uriContains = null, array $headers = []): void
    {
        $this->queued[] = [
            'uriContains' => $uriContains,
            'body'        => $body,
            'status'      => $status,
            'headers'     => $headers,
            'throw'       => null,
        ];
    }

    /**
     * Queues a rejection, shaped the way the SDK's own `RequestClient` reports one: a
     * `ClientException` carrying the message, with the status on `getResponseCode()`
     * rather than as the exception code, which stays 0. The body matters, because the
     * SDK reads it back on some statuses.
     *
     * @param array<string, mixed> $body Response body, defaulting to the message.
     */
    public function willRejectWith(string $uriContains, string $message, int $status = 400, array $body = []): void
    {
        $exception = new ClientException($message);
        $exception->setResponseBody((string) json_encode([] === $body ? ['message' => $message] : $body));
        $exception->setResponseCode($status);

        $this->queued[] = [
            'uriContains' => $uriContains,
            'throw'       => $exception,
        ];
    }

    /** Queues a connection failure, which the SDK reports as a `RequestException`. */
    public function willFailToConnect(string $uriContains, string $message = 'Connection refused'): void
    {
        $this->queued[] = [
            'uriContains' => $uriContains,
            'throw'       => new RequestException($message),
        ];
    }

    /**
     * Every request intercepted so far.
     *
     * @return array<int, array<string, mixed>>
     */
    public function requests(): array
    {
        return $this->requests;
    }

    /**
     * The requests whose URI contains the given fragment.
     *
     * @return array<int, array<string, mixed>>
     */
    public function requestsTo(string $uriContains): array
    {
        return array_values(array_filter(
            $this->requests,
            static fn(array $request): bool => false !== strpos((string) $request['uri'], $uriContains)
        ));
    }

    /** How many responses are still queued, so a test can assert it used them all. */
    public function pendingResponses(): int
    {
        return count($this->queued);
    }

    /** Forgets recorded requests and queued responses. */
    public function reset(): void
    {
        $this->requests = [];
        $this->queued   = [];
    }

    /** The requests made so far, for a failure message. */
    public function describe(): string
    {
        if ([] === $this->requests) {
            return 'none';
        }

        return implode(', ', array_map(
            static fn(array $request): string => $request['method'] . ' ' . $request['uri'],
            $this->requests
        ));
    }

    /**
     * The queued response to answer a URI with: the longest matching fragment wins, so
     * `/payments/token/cit/charge` beats a `/payments/` queued alongside it.
     * Equally specific fragments are consumed in the order they were queued.
     */
    private function matchQueued(string $uri): ?int
    {
        $match = null;
        $length = -1;

        foreach ($this->queued as $index => $queued) {
            $fragment = $queued['uriContains'];

            if (null !== $fragment && false === strpos($uri, $fragment)) {
                continue;
            }

            $candidate = null === $fragment ? 0 : strlen($fragment);

            if ($candidate > $length) {
                $match  = $index;
                $length = $candidate;
            }
        }

        return $match;
    }

    /**
     * @param array<string, mixed> $queued
     * @throws ClientException|RequestException
     */
    private function answer(array $queued, string $method): CurlResponse
    {
        if (isset($queued['throw'])) {
            throw $queued['throw'];
        }

        $body    = (string) json_encode($queued['body'], JSON_UNESCAPED_SLASHES);
        $headers = $this->responseHeaders($queued['headers'], $method, $body);
        $lines   = ['HTTP/1.1 ' . $queued['status']];

        foreach ($headers as $name => $value) {
            $lines[] = $name . ': ' . $value;
        }

        return new CurlResponse(implode("\n", $lines), $body, $queued['status']);
    }

    /**
     * The response headers, signed. Names are lower case because that is how
     * `CurlResponse` reports them back, and the signature covers what the SDK reads.
     *
     * @param array<string, string> $extra
     * @return array<string, string>
     */
    private function responseHeaders(array $extra, string $method, string $body): array
    {
        $headers = [
            'checkout-account'   => (string) $this->merchantId,
            'checkout-algorithm' => 'sha256',
            'checkout-method'    => $method,
            'content-type'       => 'application/json; charset=utf-8',
        ];

        foreach ($extra as $name => $value) {
            $headers[strtolower($name)] = (string) $value;
        }

        $headers['signature'] = Signature::calculateHmac($headers, $body, $this->secretKey);

        return $headers;
    }

    /** @param array<string, mixed> $options */
    private function urlOf(string $uri, array $options): string
    {
        $url = \Paytrail\SDK\Client::API_ENDPOINT . $uri;

        if (! empty($options['query'])) {
            $url .= '?' . http_build_query($options['query']);
        }

        return $url;
    }

    /**
     * @param mixed $body
     * @return array<string, mixed>|null
     */
    private function decodeBody($body): ?array
    {
        if (is_array($body)) {
            return $body;
        }

        if (! is_string($body)) {
            return null;
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : null;
    }
}
