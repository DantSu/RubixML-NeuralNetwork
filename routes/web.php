<?php

use Illuminate\Support\Facades\Route;
use App\Domain\DeepLearning\NeuralNetwork\MyFirstNeuralNetwork;

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
Route::get('/linear-train-dataset', [MyFirstNeuralNetwork::class, 'generateLinearTrainDataset']);
Route::get('/linear-test-dataset', [MyFirstNeuralNetwork::class, 'generateLinearTestDataset']);
Route::get('/circle-train-dataset', [MyFirstNeuralNetwork::class, 'generateCircleTrainDataset']);
Route::get('/circle-test-dataset', [MyFirstNeuralNetwork::class, 'generateCircleTestDataset']);
Route::get('/two-circle-train-dataset', [MyFirstNeuralNetwork::class, 'generateTwoCircleTrainDataset']);
Route::get('/two-circle-test-dataset', [MyFirstNeuralNetwork::class, 'generateTwoCircleTestDataset']);
Route::get('/train-dataset-visualize', [MyFirstNeuralNetwork::class, 'visualizeTrainDataset']);
Route::get('/test-dataset-visualize', [MyFirstNeuralNetwork::class, 'visualizeTestDataset']);
Route::get('/neuralnetwork-train', [MyFirstNeuralNetwork::class, 'train']);
Route::get('/neuralnetwork-test', [MyFirstNeuralNetwork::class, 'test']);
Route::get('/neuralnetwork-visualize', [MyFirstNeuralNetwork::class, 'visualizeNeuralNetworkPredictions']);
