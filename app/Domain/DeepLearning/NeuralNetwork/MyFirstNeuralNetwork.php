<?php

namespace App\Domain\DeepLearning\NeuralNetwork;

use Rubix\ML\Classifiers\MultilayerPerceptron;
use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\CrossValidation\Metrics\FBeta;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\NeuralNet\ActivationFunctions\Sigmoid;
use Rubix\ML\NeuralNet\CostFunctions\CrossEntropy;
use Rubix\ML\NeuralNet\Initializers\Uniform;
use Rubix\ML\NeuralNet\Layers\Activation;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\NeuralNet\Optimizers\Adam;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Serializers\RBX;
use App\Domain\Loggers\LoggerHtml;
use App\Domain\DeepLearning\NeuralNetwork\Visualizers\DatasetVisualizer;

class MyFirstNeuralNetwork
{
    public function generateLinearTrainDataset()
    {
        $data = $this->generateLinearDataset(10000, true);
        DatasetFiles::saveTrain($data);
        $this->visualizeData($data);
    }

    public function generateLinearTestDataset()
    {
        $data = $this->generateLinearDataset(1000, false);
        DatasetFiles::saveTest($data);
        $this->visualizeData($data);
    }


    private function generateLinearDataset(int $nbData, bool $randomize): array
    {
        $data = [];
        for ($i = 0; $i < $nbData; ++$i) {
            $x = \rand(-1000, 1000) / 1000 * 6;
            $y = \rand(-1000, 1000) / 1000 * 6;

            $fnA = 1 / (1 + \exp(-1 * ($x * 0.7 + $y * -1.2 + 1.42)));
            $a = $fnA > 0.5;

            if ($randomize && $fnA > 0.4 && $fnA < 0.6 && \rand(0, 10) == 0) {
                $a = !$a;
            }

            $data[] = ['x' => $x, 'y' => $y, 'v' => $a ? 'TOXIC' : 'EDIBLE'];
        }
        return $data;
    }

    private function isInCircle(float $x, float $y, float $activation, float $randomActivation, float $bias1, float $bias2, float $bias3, float $bias4, float $bias5, float $bias6, float $bias7, float $bias8, bool $randomize): bool
    {
        $fnA1 = 1 / (1 + \exp(-1 * ($x * 1 + $y * 0 + $bias1)));
        $fnA2 = 1 / (1 + \exp(-1 * ($x * -1 + $y * 0 + $bias2)));
        $fnA3 = 1 / (1 + \exp(-1 * ($x * 0 + $y * -1 + $bias3)));
        $fnA4 = 1 / (1 + \exp(-1 * ($x * 0 + $y * 1 + $bias4)));
        $fnA5 = 1 / (1 + \exp(-1 * ($x * 0.5 + $y * -0.5 + $bias5)));
        $fnA6 = 1 / (1 + \exp(-1 * ($x * -0.5 + $y * 0.5 + $bias6)));
        $fnA7 = 1 / (1 + \exp(-1 * ($x * -0.5 + $y * -0.5 + $bias7)));
        $fnA8 = 1 / (1 + \exp(-1 * ($x * 0.5 + $y * 0.5 + $bias8)));
        $fnA = $fnA1 * $fnA2 * $fnA3 * $fnA4 * $fnA5 * $fnA6 * $fnA7 * $fnA8;
        $a = $fnA > $activation;

        if ($randomize && !$a && $fnA > $randomActivation && \rand(0, 10) == 0) {
            $a = true;
        }
        return $a;
    }

    public function generateCircleTrainDataset()
    {
        $data = $this->generateCircleDataset(10000, true);
        DatasetFiles::saveTrain($data);
        $this->visualizeData($data);
    }

    public function generateCircleTestDataset()
    {
        $data = $this->generateCircleDataset(1000, false);
        DatasetFiles::saveTest($data);
        $this->visualizeData($data);
    }

    private function generateCircleDataset(int $nbData, bool $randomize): array
    {
        $data = [];
        for ($i = 0; $i < $nbData; ++$i) {
            $x = \rand(-1000, 1000) / 1000 * 6;
            $y = \rand(-1000, 1000) / 1000 * 6;

            $data[] = [
                'x' => $x,
                'y' => $y,
                'v' => $this->isInCircle($x, $y, 0.15, 0.12, 3, 3, 3, 3, 2, 2, 2, 2, $randomize) ? 'TOXIC' : 'EDIBLE'
            ];
        }

        return $data;
    }

    public function generateTwoCircleTrainDataset()
    {
        $data = $this->generateTwoCircleDataset(10000, true);
        DatasetFiles::saveTrain($data);
        $this->visualizeData($data);
    }

    public function generateTwoCircleTestDataset()
    {
        $data = $this->generateTwoCircleDataset(1000, false);
        DatasetFiles::saveTest($data);
        $this->visualizeData($data);
    }

    private function generateTwoCircleDataset(int $nbData, bool $randomize): array
    {
        $data = [];
        for ($i = 0; $i < $nbData; ++$i) {
            $x = \rand(-1000, 1000) / 1000 * 6;
            $y = \rand(-1000, 1000) / 1000 * 6;

            $data[] = [
                'x' => $x,
                'y' => $y,
                'v' => $this->isInCircle($x, $y, 0.1, 0.09, -1.3, 4.3, -1.3, 4.3, -1.5, 4, 1, 1, $randomize) ? 'EDIBLE' : ($this->isInCircle($x, $y, 0.13, 0.11, 3, 3, 3, 3, 2, 2, 2, 2, $randomize) ? 'NON-TOXIC' : 'TOXIC')
            ];
        }

        return $data;
    }

    public function visualizeTrainDataset()
    {
        $this->visualizeData(DatasetFiles::getTrain());
    }

    public function visualizeTestDataset()
    {
        $this->visualizeData(DatasetFiles::getTest());
    }

    public function visualizeData(array $dataset, array $predictions = []): MyFirstNeuralNetwork
    {
        \header("Content-Type: image/jpeg");

        (new DatasetVisualizer(1280, 1280))
            ->setDataset($dataset)
            ->setLabelsValuesColors([
                'TOXIC' => '9d00ee',
                'NON-TOXIC' => 'e4c600',
                'EDIBLE' => '005fee'
            ])
            ->draw($predictions)
            ->displayJPG(90);
        return $this;
    }


    public function train(): string
    {
        $logger = new LoggerHtml();

        $dataTrain = DatasetFiles::getTrain();
        $dataTest = DatasetFiles::getTest();

        $labeledDataTrain = new Labeled(\array_map(fn($v) => [$v['x'], $v['y']], $dataTrain), \array_map(fn($v) => $v['v'], $dataTrain));
        $labeledDataTest = new Labeled(\array_map(fn($v) => [$v['x'], $v['y']], $dataTest), \array_map(fn($v) => $v['v'], $dataTest));

        $estimator = new MultilayerPerceptron(
            [
                new Dense(
                    16,
                    0,
                    true,
                    new Uniform(),
                    new Uniform()
                ),
                new Activation(new Sigmoid()),
            ],
            512,
            new Adam(0.0005, 0.05, 0.0005),
            0,
            1000,
            1e-8,
            500,
            0.3,
            new CrossEntropy(),
            new FBeta()
        );
        $estimator->setLogger($logger);
        $estimator->train($labeledDataTrain);
        $stackTrace = $logger->getStackTrace();

        $predictions = $estimator->predict($labeledDataTest);
        $metric = new Accuracy();
        $score = $metric->score($predictions, $labeledDataTest->labels());

        $persistentModel = new PersistentModel($estimator, new Filesystem(\storage_path('app\private\model.rbx')), new RBX());
        $persistentModel->save();

        return \view('train', ['score' => $score, 'stackTrace' => $stackTrace]);
    }

    private function getPersistentMultilayerPerceptron(): ?MultilayerPerceptron
    {
        $persister = new Filesystem(\storage_path('app\private\model.rbx'));
        $estimator = $persister->load()->deserializeWith(new RBX());

        if ($estimator instanceof MultilayerPerceptron) {
            return $estimator;
        }
        return null;
    }

    public function test()
    {
        $estimator = $this->getPersistentMultilayerPerceptron();

        if ($estimator == null) {
            return;
        }

        $dataTest = DatasetFiles::getTest();
        $labeledDataTest = new Labeled(\array_map(fn($v) => [$v['x'], $v['y']], $dataTest), \array_map(fn($v) => $v['v'], $dataTest));
        $predictions = $estimator->predict($labeledDataTest);
        $this->visualizeData($dataTest, $predictions);
    }

    public function visualizeNeuralNetworkPredictions()
    {
        $estimator = $this->getPersistentMultilayerPerceptron();

        if ($estimator == null) {
            return;
        }

        $dataVisualizer = new DatasetVisualizer(1280, 1280);

        foreach ($estimator->network()->layers() as $layer) {
            if ($layer instanceof Dense) {
                $params = $layer->parameters();
                $fn = [];
                foreach ($params as $k => $param) {
                    switch ($k) {
                        case 'weights':
                            $weights = $param->param()->asArray();
                            foreach ($weights as $weight) {
                                $fn[] = ['wx' => $weight[0] ?? 0, 'wy' => $weight[1] ?? 0, 'bias' => 0];
                            }
                            break;
                        case 'biases':
                            $biases = $param->param()->asArray();
                            foreach ($biases as $i => $bias) {
                                $fn[$i]['bias'] = $bias;
                            }
                            break;
                    }
                }
                foreach ($fn as $item) {
                    $dataVisualizer->addLinearFunction($item['wx'], $item['wy'], $item['bias']);
                }
                break;
            }
        }

        $dataset = DatasetFiles::getTest();
        $dataVisualizer
            ->setDataset($dataset)
            ->setLabelsValuesColors([
                'TOXIC' => '9d00ee',
                'NON-TOXIC' => 'e4c600',
                'EDIBLE' => '005fee'
            ]);

        $dataset = $dataVisualizer->generateGridDataset();
        $labeledDataTest = new Labeled($dataset, \array_fill(0, \count($dataset), 'TOXIC'));
        $predictions = $estimator->predict($labeledDataTest);
        $dataset = \array_map(fn($v, $p) => [...$v, 'v' => $p], $dataset, $predictions);

        \header("Content-Type: image/jpeg");
        $dataVisualizer
            ->setDataset($dataset)
            ->draw()
            ->displayJPG(90);
    }
}
