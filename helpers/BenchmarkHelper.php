<?php

declare(strict_types=1);

namespace app\helpers;

class BenchmarkHelper
{
	public static function getMicroTime(): float
	{
		return microtime(true);
	}

	public static function getMemoryUsage(): int
	{
		return memory_get_usage();
	}

	public static function getMemoryPeakUsage(): int
	{
		return memory_get_peak_usage();
	}

	public static function getExecutionTime(float $startTime): float
	{
		return self::getMicroTime() - $startTime;
	}

	public static function getMemoryUsageDiff(int $startMemoryUsage): int
	{
		return self::getMemoryUsage() - $startMemoryUsage;
	}

	public static function getMemoryPeakUsageDiff(int $startMemoryUsage): int
	{
		return self::getMemoryPeakUsage() - $startMemoryUsage;
	}

	public static function test(callable $callback, int $iterations = 10, bool $printExecutionTime = false, string $testName = ''): array
	{
		$times = [];

		for ($i = 0; $i < $iterations; $i++) {
			$startTime = self::getMicroTime();

			$callback();

			$times[] = self::getExecutionTime($startTime);
		}

		$executionTimeAvg    = array_sum($times) / count($times);
		$executionTimeMin    = min($times);
		$executionTimeMax    = max($times);
		$executionTimeStdDev = self::calculateStdDev($times);

		if ($printExecutionTime) {
			echo 'Test name: ' . $testName . PHP_EOL;
			echo 'Iterations: ' . $iterations . PHP_EOL;
			echo 'Execution time (avg): ' . self::formatTime($executionTimeAvg) . PHP_EOL;
			echo 'Execution time (min): ' . self::formatTime($executionTimeMin) . PHP_EOL;
			echo 'Execution time (max): ' . self::formatTime($executionTimeMax) . PHP_EOL;
			echo 'Execution time (std dev): ' . self::formatTime($executionTimeStdDev) . PHP_EOL;
		}

		return [
			'avg_time'     => $executionTimeAvg,
			'min_time'     => $executionTimeMin,
			'max_time'     => $executionTimeMax,
			'std_dev_time' => $executionTimeStdDev
		];
	}

	private static function calculateStdDev(array $values): float
	{
		$mean     = array_sum($values) / count($values);
		$variance = array_sum(array_map(static fn($x) => ($x - $mean) ** 2, $values)) / count($values);

		return sqrt($variance);
	}

	private static function formatTime(float $time): string
	{
		return number_format($time * 1000, 3) . ' ms';
	}

	private static function formatMemory(int $bytes): string
	{
		$units  = ['B', 'KB', 'MB', 'GB'];
		$factor = (int)floor(log($bytes, 1024));

		return number_format($bytes / (1024 ** $factor), 2) . ' ' . $units[$factor];
	}

	public static function createLog(array $results, string $filename = 'benchmark.log'): void
	{
		$log = "[" . date('Y-m-d H:i:s') . "] Benchmark results:\n";

		foreach ($results as $key => $value) {
			$log .= ucfirst(str_replace('_', ' ', $key)) . ": " . (is_float($value) ? self::formatTime($value) : self::formatMemory($value)) . "\n";
		}

		file_put_contents($filename, $log . "\n", FILE_APPEND);
	}

	public static function testWithArgs(callable $callback, array $args, int $iterations = 10, bool $printExecutionTime = false, string $testName = ''): array
	{
		return self::test(static fn() => $callback(...$args), $iterations, $printExecutionTime, $testName);
	}
}