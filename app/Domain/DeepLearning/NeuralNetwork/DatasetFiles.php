<?php

namespace App\Domain\DeepLearning\NeuralNetwork;


class DatasetFiles
{
    private const FILENAME_TRAIN = 'train_dataset';
    private const FILENAME_TEST = 'test_dataset';

    private static function save(string $datasetName, array $data): bool
    {
        return \file_put_contents(\storage_path('app/private/' . $datasetName . '.json'), \json_encode($data/*, JSON_PRETTY_PRINT*/)) !== false;
    }

    public static function saveTrain(array $data): bool
    {
        return static::save(static::FILENAME_TRAIN, $data);
    }

    public static function saveTest(array $data): bool
    {
        return static::save(static::FILENAME_TEST, $data);
    }

    private static function get(string $datasetName): array
    {
        return \json_decode(\file_get_contents(\storage_path('app/private/' . $datasetName . '.json')), true);
    }

    public static function getTrain(): array
    {
        return static::get(static::FILENAME_TRAIN);
    }

    public static function getTest(): array
    {
        return static::get(static::FILENAME_TEST);
    }
}
