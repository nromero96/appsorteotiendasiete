<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DrawController;
use App\Http\Controllers\PrizeController;
use App\Http\Controllers\PublicDrawController;
use App\Http\Controllers\PublicParticipationController;
use App\Http\Controllers\PublicTicketVerificationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketShareController;
use App\Http\Controllers\TicketPrintController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicDrawController::class)->name('public.next-draw');
Route::view('/legales/terminos-y-condiciones', 'legal.terms')->name('legal.terms');
Route::view('/legales/privacidad', 'legal.privacy')->name('legal.privacy');
Route::view('/legales/cookies', 'legal.cookies')->name('legal.cookies');
Route::get('/verificar-tickets', [PublicTicketVerificationController::class, 'index'])->name('public.ticket-verification');
Route::get('/sorteos/{draw}/participar', [PublicParticipationController::class, 'create'])->name('public.participation.create');
Route::post('/sorteos/{draw}/participar', [PublicParticipationController::class, 'store'])->middleware('throttle:10,1')->name('public.participation.store');
Route::get('/participacion/{sale}/confirmacion', [PublicParticipationController::class, 'confirmation'])->name('public.participation.confirmation');

Route::middleware('guest')->group(function () {
    Route::get('/admin', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/admin', [LoginController::class, 'login']);
});
Route::post('/salir', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->middleware(['auth', 'permission:ver panel administrativo'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/usuarios', [UserController::class, 'index'])->middleware('permission:gestionar usuarios')->name('users.index');
    Route::get('/usuarios/crear', [UserController::class, 'create'])->middleware('permission:gestionar usuarios')->name('users.create');
    Route::post('/usuarios', [UserController::class, 'store'])->middleware('permission:gestionar usuarios')->name('users.store');
    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->middleware('permission:gestionar usuarios')->name('users.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->middleware('permission:gestionar usuarios')->name('users.update');
    Route::patch('/usuarios/{user}/estado', [UserController::class, 'toggleStatus'])->middleware('permission:gestionar usuarios')->name('users.status.toggle');
    Route::get('/ventas', [SaleController::class, 'index'])->middleware('permission:ver ventas')->name('sales.index');
    Route::put('/ventas/{sale}/estado', [SaleController::class, 'updateStatus'])->middleware('permission:actualizar estados de venta')->name('sales.status.update');
    Route::get('/tickets/{ticket}/imagen', [TicketShareController::class, 'image'])->middleware('permission:ver ventas')->name('tickets.share-image');
    Route::get('/sorteos/{draw}/talonarios', [TicketPrintController::class, 'index'])->middleware('permission:imprimir talonarios')->name('tickets.print');
    Route::get('/sorteos/{draw}/talonarios/{ticket}', [TicketPrintController::class, 'single'])->middleware('permission:imprimir talonarios')->name('tickets.print.single');
    Route::get('/sorteos', [DrawController::class, 'index'])->name('draws.index');
    Route::get('/sorteos/crear', [DrawController::class, 'create'])->middleware('permission:gestionar sorteos')->name('draws.create');
    Route::post('/sorteos', [DrawController::class, 'store'])->middleware('permission:gestionar sorteos')->name('draws.store');
    Route::get('/sorteos/{draw}', [DrawController::class, 'show'])->name('draws.show');
    Route::get('/sorteos/{draw}/reporte', [DrawController::class, 'report'])->middleware('permission:ver ventas')->name('draws.report');
    Route::get('/sorteos/{draw}/editar', [DrawController::class, 'edit'])->middleware('permission:gestionar sorteos')->name('draws.edit');
    Route::put('/sorteos/{draw}', [DrawController::class, 'update'])->middleware('permission:gestionar sorteos')->name('draws.update');
    Route::get('/sorteos/{draw}/premios/crear', [PrizeController::class, 'create'])->middleware('permission:gestionar premios')->name('prizes.create');
    Route::post('/sorteos/{draw}/premios', [PrizeController::class, 'store'])->middleware('permission:gestionar premios')->name('prizes.store');
    Route::get('/sorteos/{draw}/premios/{prize}/editar', [PrizeController::class, 'edit'])->middleware('permission:gestionar premios')->name('prizes.edit');
    Route::put('/sorteos/{draw}/premios/{prize}', [PrizeController::class, 'update'])->middleware('permission:gestionar premios')->name('prizes.update');
    Route::delete('/sorteos/{draw}/premios/{prize}', [PrizeController::class, 'destroy'])->middleware('permission:gestionar premios')->name('prizes.destroy');
    Route::get('/sorteos/{draw}/tickets/crear', [TicketController::class, 'create'])->middleware('permission:registrar tickets')->name('tickets.create');
    Route::post('/sorteos/{draw}/tickets', [TicketController::class, 'store'])->middleware('permission:registrar tickets')->name('tickets.store');
});
