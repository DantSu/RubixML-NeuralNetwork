<?php

namespace App\Domain\DeepLearning\NeuralNetwork\Visualizers;

use DantSu\PHPImageEditor\Image;

class DatasetVisualizer
{
    private int $width;
    private int $height;
    private array $data;
    private array $linearFunctions;
    private int $xMin;
    private int $yMin;
    private int $xMax;
    private int $yMax;
    private float $xPxValue;
    private float $yPxValue;
    private float $originX;
    private float $originY;
    private array $colors;

    public function __construct(int $width = 1280, int $height = 1280)
    {
        $this->width = $width;
        $this->height = $height;
        $this->linearFunctions = [];
    }

    private function convertXValueToPx(float $x): float
    {
        return $this->originX + $x / $this->xPxValue;
    }

    private function convertYValueToPx(float $y): float
    {
        return $this->originY - $y / $this->yPxValue;
    }

    private function convertXPxToValue(float $x): float
    {
        return ($x - $this->originX) * $this->xPxValue;
    }

    private function convertYPxToValue(float $y): float
    {
        return -1 * ($y - $this->originY) * $this->yPxValue;
    }

    public function setLabelsValuesColors(array $colors): DatasetVisualizer
    {
        $this->colors = $colors;
        return $this;
    }

    public function getColor(string $value): string
    {
        return $this->colors[$value] ?? 'FFFFFF';
    }

    public function setDataset(array $data): DatasetVisualizer
    {
        $this->data = $data;
        $xs = \array_map(fn($v) => $v['x'], $this->data);
        $ys = \array_map(fn($v) => $v['y'], $this->data);
        $this->xMin = \floor(\min([...$xs, 0])) - 1;
        $this->yMin = \floor(\min([...$ys, 0])) - 1;
        $this->xMax = \ceil(\max($xs)) + 1;
        $this->yMax = \ceil(\max($ys)) + 1;
        $this->xPxValue = ($this->xMax - $this->xMin) / $this->width;
        $this->yPxValue = ($this->yMax - $this->yMin) / $this->height;
        $this->originX = -1 * $this->xMin / $this->xPxValue;
        $this->originY = $this->height + $this->yMin / $this->yPxValue;
        return $this;
    }

    public function generateGridDataset(): array
    {
        $dataset = [];
        $iStart = \ceil(1 / $this->xPxValue);
        $iEnd = $this->width - $iStart;
        $jStart = \ceil(1 / $this->yPxValue);
        $jEnd = $this->height - $jStart;

        for ($i = $iStart; $i < $iEnd; $i += 8) {
            $x = $this->convertXPxToValue($i);
            for ($j = $jStart; $j < $jEnd; $j += 8) {
                $y = $this->convertYPxToValue($j);
                $dataset[] = [
                    'x' => $x,
                    'y' => $y
                ];
            }
        }
        return $dataset;
    }

    public function addLinearFunction(float $wx, float $wy, float $bias)
    {
        $this->linearFunctions[] = ['wx' => $wx, 'wy' => $wy, 'b' => $bias];
    }


    public function draw(array $predictions = []): Image
    {
        $fontFile = \resource_path('fonts/verdana.ttf');

        $image = Image::newCanvas($this->width, $this->height);
        $image->drawRectangle(0, 0, $this->width, $this->height, '000000');

        $accuracy = 0;
        foreach ($this->data as $k => $v) {
            $x = $this->convertXValueToPx($v['x']);
            $y = $this->convertYValueToPx($v['y']);
            if ($predictions) {
                $image->drawCircle($x, $y, 12, $predictions[$k] === $v['v'] ? '89d800' : 'f40000');
                $predictions[$k] === $v['v'] && ++$accuracy;
            }
            $image->drawCircle($x, $y, 8, $this->getColor($v['v']));
        }

        $image->drawLine($this->originX, 0, $this->originX, $this->height, 2, 'FFFFFF');
        $image->drawLine(0, $this->originY, $this->width, $this->originY, 2, 'FFFFFF');
        for ($i = $this->xMin; $i <= $this->xMax; ++$i) {
            $x = $this->convertXValueToPx($i);
            $image->drawLine($x, $this->originY - 5, $x, $this->originY + 5, 2, 'FFFFFF');
            $image->writeText($i, $fontFile, 14, 'FFFFFF', $x + 5, $this->originY + 5, Image::ALIGN_LEFT, Image::ALIGN_TOP);
        }
        for ($i = $this->yMin; $i <= $this->yMax; ++$i) {
            $y = $this->convertYValueToPx($i);
            $image->drawLine($this->originX - 5, $y, $this->originX + 5, $y, 2, 'FFFFFF');
            $image->writeText($i, $fontFile, 14, 'FFFFFF', $this->originX + 5, $y + 5, Image::ALIGN_LEFT, Image::ALIGN_TOP);
        }

        foreach ($this->linearFunctions as $lf) {
            $endPoints = $this->getEndPoints($lf['wx'], $lf['wy'], $lf['b']);
            if ($endPoints) {
                $image->drawLine(
                    $this->convertXValueToPx($endPoints[0]['x']),
                    $this->convertYValueToPx($endPoints[0]['y']),
                    $this->convertXValueToPx($endPoints[1]['x']),
                    $this->convertYValueToPx($endPoints[1]['y']),
                    1,
                    'FFFFFF'
                );
            }
        }

        if ($predictions) {
            $image->writeText('Fiabilité : ' . \round($accuracy / \count($this->data) * 100, 2) . '%', $fontFile, 14, 'FFFFFF', 5, 5, Image::ALIGN_LEFT, Image::ALIGN_TOP);
        }

        return $image;
    }

    private function getEndPoints(float $wx, float $wy, float $b): array
    {
        $minX = $this->convertXPxToValue(0);
        $minY = $this->convertYPxToValue($this->height);
        $maxX = $this->convertXPxToValue($this->width);
        $maxY = $this->convertYPxToValue(0);

        $fromX = ($maxX + $minX) / 2;
        $stepX = $fromX - $minX;
        $fromY = ($maxY + $minY) / 2;
        $stepY = $fromY - $minY;

        $endPointsTests = [
            ['x', $fromX, $stepX, $minY],
            ['x', $fromX, $stepX, $maxY],
            ['y', $fromY, $stepY, $minX],
            ['y', $fromY, $stepY, $maxX],
        ];

        $endPoints = [];
        foreach ($endPointsTests as $test) {
            $endPoint = $this->getEndPoint($test[0], $test[1], $test[2], $test[3], $wx, $wy, $b);
            if (\is_array($endPoint)) {
                $endPoints[] = $endPoint;
            }
        }
        return $endPoints;
    }

    private function getEndPoint(string $axe, float $current, float $step, float $constant, float $wx, float $wy, float $b): ?array
    {
        $lf = fn($x, $y) => $wx * $x + $wy * $y + $b;

        $delta = 0.01;

        if ($axe === 'x') {
            $score = $lf($current, $constant);
            $score1 = $lf($current - $step, $constant);
            $score2 = $lf($current + $step, $constant);
        } else {
            $score = $lf($constant, $current);
            $score1 = $lf($constant, $current - $step);
            $score2 = $lf($constant, $current + $step);
        }

        while (\abs($score) > $delta) {
            $step = $step / 2;

            if ($score1 * $score < 0) {
                $current -= $step;
                $score2 = $score;
            } elseif ($score2 * $score < 0) {
                $current += $step;
                $score1 = $score;
            } else {
                return null;
            }

            if ($axe === 'x') {
                $score = $lf($current, $constant);
            } else {
                $score = $lf($constant, $current);
            }
        }

        if ($axe === 'x') {
            return ['x' => $current, 'y' => $constant];
        } else {
            return ['x' => $constant, 'y' => $current];
        }
    }
}
