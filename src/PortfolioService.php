<?php declare(strict_types = 1);

namespace Api\Portfolio;

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
	 * @param Transaction[] $transactions
	 */
	public function performance(array $transactions, ?PortfolioPerformanceRange $range = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::PerformanceLink, [
			'range' => $range?->value,
		]);
	}

}
