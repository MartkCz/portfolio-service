<?php declare(strict_types = 1);

namespace Api\Portfolio;

use Api\Core\Exception\InvalidRequestException;
use Api\Core\Exception\UnrecoverableRequestException;
use Api\Core\RequestType;
use Api\Core\Service;
use Api\Core\ServiceRequest;
use App\Portfolio\Transaction;

/**
 * Dates are in UTC
 *
 * @phpstan-type Transaction array{ symbol: string, shares: float, investment: float, date: string }
 */
final class PortfolioService extends Service
{

	public const GainsLink = '/portfolio/gains';
	public const TimeSeriesLink = '/portfolio/timeseries-value';
	public const ValueLink = '/portfolio/value';
	public const PerformanceLink = '/portfolio/performance';
	public const ImportLink = '/portfolio/import';

	/**
	 * @param Transaction[] $transactions
	 */
	public function timeSeries(array $transactions, ?string $currency = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::TimeSeriesLink, [
			'currency' => $currency,
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function gains(array $transactions): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::GainsLink);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function value(array $transactions, ?string $currency = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::ValueLink, [
			'currency' => $currency,
		]);
	}

	/**
	 * @param mixed[] $transactions
	 * @return array{ symbol: string, shares: float, investment: float, date: string }[]
	 * @throws InvalidRequestException
	 */
	public function import(array $transactions, bool $validate = false, bool $splits = false): array
	{
		$response = $this->requestJson(RequestType::Post, $transactions, self::ImportLink, [
			'splits' => $splits ? '1' : '0',
			'validate' => $validate ? '1' : '0',
		])->request();

		$code = $response->getStatusCode();

		if ($code === 200) {
			return $response->toArray();
		}

		throw InvalidRequestException::create($response);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function performance(array $transactions, ?PortfolioPerformanceRange $range = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::PerformanceLink, [
			'range' => $range?->value,
		]);
	}

}
