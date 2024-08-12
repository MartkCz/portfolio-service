<?php declare(strict_types = 1);

namespace Api\Portfolio;

use App\Portfolio\Transaction;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
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
	public function requestTimeSeries(array $transactions): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::TimeSeriesLink), [
			'json' => $transactions,
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function requestGains(array $transactions): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::GainsLink), [
			'json' => $transactions,
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function requestValue(array $transactions): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::ValueLink), [
			'json' => $transactions,
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function requestPerformance(array $transactions): ResponseInterface
	{
		return $this->httpClient->request('POST', $this->buildUrl(self::PerformanceLink), [
			'json' => $transactions,
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

}
