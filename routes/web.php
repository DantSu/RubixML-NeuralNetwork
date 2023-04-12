<?php

use Illuminate\Support\Facades\Route;
use App\Domain\DeepLearning\NeuralNetwork\MyNeuralNetwork;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn() => view('welcome'));
Route::get('/linear-train-dataset', [MyNeuralNetwork::class, 'generateLinearTrainDataset']);
Route::get('/linear-test-dataset', [MyNeuralNetwork::class, 'generateLinearTestDataset']);
Route::get('/circle-train-dataset', [MyNeuralNetwork::class, 'generateCircleTrainDataset']);
Route::get('/circle-test-dataset', [MyNeuralNetwork::class, 'generateCircleTestDataset']);
Route::get('/two-circle-train-dataset', [MyNeuralNetwork::class, 'generateTwoCircleTrainDataset']);
Route::get('/two-circle-test-dataset', [MyNeuralNetwork::class, 'generateTwoCircleTestDataset']);
Route::get('/train-dataset-visualize', [MyNeuralNetwork::class, 'visualizeTrainDataset']);
Route::get('/test-dataset-visualize', [MyNeuralNetwork::class, 'visualizeTestDataset']);
Route::get('/neuralnetwork-train', [MyNeuralNetwork::class, 'train']);
Route::get('/neuralnetwork-test', [MyNeuralNetwork::class, 'test']);
Route::get('/neuralnetwork-visualize', [MyNeuralNetwork::class, 'visualizeNeuralNetworkPredictions']);
