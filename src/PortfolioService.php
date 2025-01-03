<?php declare(strict_types = 1);

namespace Api\Portfolio;

use Api\Core\Exception\InvalidRequestException;
use Api\Core\RequestType;
use Api\Core\Service;
use Api\Core\ServiceRequest;

/**
 * Dates are in UTC
 *
 * @phpstan-type Transaction array{ symbol: string, shares: float, investment: float, date: string }
 */
final class PortfolioService extends Service
{

	public const GainsLink = '/portfolio/gains';
	public const TimeSeriesLink = '/portfolio/timeseries-value';

	public const ComparedTimeSeriesLink = '/portfolio/compared-timeseries';
	public const InvestmentTimeSeriesLink = '/portfolio/investment-timeseries';
	public const ValueLink = '/portfolio/value';
	public const PerformanceLink = '/portfolio/performance';
	public const PositionsLink = '/portfolio/positions';
	public const ImportLink = '/portfolio/import';
	public const ImportBrokerLink = '/brokers/%s/csv';
	public const DividendsLink = '/portfolio/dividends';

	/**
	 * @param Transaction[] $transactions
	 */
	public function dividends(array $transactions, int $year, bool $sensitive, ?string $currency = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::DividendsLink, [
			'year' => $year,
			'sensitive' => $sensitive ? 'true' : 'false',
			'currency' => $currency,
		]);
	}

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
	public function comparedTimeSeries(array $transactions, ?string $currency = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::ComparedTimeSeriesLink, [
			'currency' => $currency,
		]);
	}

	/**
	 * @param Transaction[] $transactions
	 */
	public function investmentTimeSeries(array $transactions, ?string $currency = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::InvestmentTimeSeriesLink, [
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
	 * @return array{ ticker: string, shares: float, value: float|null, date: string, type: 'sell'|'buy' }[]
	 * @throws InvalidRequestException
	 */
	public function importBroker(string $content, string $broker, bool $validate = false): array
	{
		$response = $this->requestBody(RequestType::Post, $content, sprintf(self::ImportBrokerLink, $broker), [
			'validate' => $validate ? '1' : '0',
		], [
			'Content-Type' => 'text/csv',
		])->request();

		$code = $response->getStatusCode();

		if ($code === 204) {
			return [];
		}

		if ($code === 200) {
			return $response->toArray();
		}

		throw InvalidRequestException::create($response);
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

		if ($code === 204) {
			return [];
		}

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

	/**
	 * @param Transaction[] $transactions
	 */
	public function positions(array $transactions, ?string $currency = null, ?string $symbol = null): ServiceRequest
	{
		return $this->requestJson(RequestType::Post, $transactions, self::PositionsLink, [
			'currency' => $currency,
			'symbol' => $symbol,
		]);
	}

}
