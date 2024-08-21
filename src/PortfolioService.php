<?php declare(strict_types = 1);

namespace Api\Portfolio;

use App\Portfolio\Transaction;
use DateTimeInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Dates are in UTC
 *
 * @phpstan-type Transaction array{ symbol: string, shares: float, investment: float, date: string }
 */
final class PortfolioService
{

	public const GainsLink = '/portfolio/gains';
	public const TimeSeriesLink = '/portfolio/timeseries-value';
	public const ValueLink = '/portfolio/value';
	public const PerformanceLink = '/portfolio/performance';

	private HttpClientInterface $httpClient;

	public function __construct(
		private string $baseUrl,
		?HttpClientInterface $httpClient,
	)
	{
		$this->httpClient = $httpClient ?? HttpClient::create();
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function requestTimeSeries(
		array $transactions,
		?DateTimeInterface $portfolioLastUpdate = null,
		?DateTimeInterface $ifModifiedSince = null,
	): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::TimeSeriesLink), [
			'json' => $transactions,
			'headers' => $this->createHeaders($portfolioLastUpdate, $ifModifiedSince),
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function requestGains(
		array $transactions,
		?DateTimeInterface $portfolioLastUpdate = null,
		?DateTimeInterface $ifModifiedSince = null,
	): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::GainsLink), [
			'json' => $transactions,
			'headers' => $this->createHeaders($portfolioLastUpdate, $ifModifiedSince),
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function requestValue(
		array $transactions,
		?DateTimeInterface $portfolioLastUpdate = null,
		?DateTimeInterface $ifModifiedSince = null,
	): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::ValueLink), [
			'json' => $transactions,
			'headers' => $this->createHeaders($portfolioLastUpdate, $ifModifiedSince),
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function requestPerformance(
		array $transactions,
		?DateTimeInterface $portfolioLastUpdate = null,
		?DateTimeInterface $ifModifiedSince = null,
	): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::PerformanceLink), [
			'json' => $transactions,
			'headers' => $this->createHeaders($portfolioLastUpdate, $ifModifiedSince),
		]);
	}

	/**
	 * @param array<string, scalar> $params
	 */
	private function buildUrl(string $path, array $params = []): string
	{
		$url = $this->baseUrl . $path;

		if (count($params) > 0) {
			$url .= '?' . http_build_query($params);
		}

		return $url;
	}

	/**
	 * @return array<string, string>
	 */
	private function createHeaders(?DateTimeInterface $lastUpdate, ?DateTimeInterface $ifModifiedSince): array
	{
		$headers = [];

		if ($lastUpdate) {
			$headers['X-Last-Update'] = $lastUpdate->format('D, d M Y H:i:s \G\M\T');
		}

		if ($ifModifiedSince) {
			$headers['If-Modified-Since'] = $ifModifiedSince->format('D, d M Y H:i:s \G\M\T');
		}

		return $headers;
	}

}
