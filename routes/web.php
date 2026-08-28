<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\PredioController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\PatchPanelController;
use App\Http\Controllers\TipoPortaController; 
use App\Http\Controllers\PlantaController; 

Route::get('/',[IndexController::class,'index']);

// Prédios
Route::get('/predios', [PredioController::class, 'index']);
Route::get('/predios/create', [PredioController::class, 'create']);
Route::post('/predios', [PredioController::class, 'store']);
Route::get('/predios/{predio}', [PredioController::class, 'show']);
Route::get('/predios/{predio}/edit', [PredioController::class, 'edit']);
Route::put('/predios/{predio}', [PredioController::class, 'update']);
Route::delete('/predios/{predio}', [PredioController::class, 'destroy']);

// Salas
Route::get('/salas/create', [SalaController::class, 'create']);
Route::post('/salas', [SalaController::class, 'store']);

Route::get('/salas/{planta}/markers', [SalaController::class, 'markers']);
Route::delete('/salas/{sala}/unmark', [SalaController::class, 'unmark']);
Route::put('/salas/{sala}/{planta}/mark', [SalaController::class, 'mark']);

Route::get('/salas/{sala}', [SalaController::class, 'show']);
Route::get('/salas/{sala}/edit', [SalaController::class, 'edit']);
Route::put('/salas/{sala}', [SalaController::class, 'update']);
Route::delete('/salas/{sala}', [SalaController::class, 'destroy']);
Route::get('/salas/{planta}/mark', [PlantaController::class, 'mark']);


// Racks
Route::get('/racks/create', [RackController::class, 'create']);
Route::post('/racks', [RackController::class, 'store']);
Route::get('/racks/{rack}', [RackController::class, 'show']);
Route::get('/racks/{rack}/edit', [RackController::class, 'edit']);
Route::put('/racks/{rack}', [RackController::class, 'update']);
Route::delete('/racks/{rack}', [RackController::class, 'destroy']);

// Patch Panels
Route::get('/patch-panels/create', [PatchPanelController::class, 'create']);
Route::post('/patch-panels', [PatchPanelController::class, 'store']);
Route::get('/patch-panels/{patchPanel}', [PatchPanelController::class, 'show']);
Route::get('/patch-panels/{patchPanel}/edit', [PatchPanelController::class, 'edit']);
Route::put('/patch-panels/{patchPanel}', [PatchPanelController::class, 'update']);
Route::delete('/patch-panels/{patchPanel}', [PatchPanelController::class, 'destroy']);

// Equipamentos
Route::get('/equipamentos/create', [EquipamentoController::class, 'create']);
Route::post('/equipamentos', [EquipamentoController::class, 'store']);
Route::get('/equipamentos/{equipamento}', [EquipamentoController::class, 'show']);
Route::get('/equipamentos/{equipamento}/edit', [EquipamentoController::class, 'edit']);
Route::put('/equipamentos/{equipamento}', [EquipamentoController::class, 'update']);
Route::delete('/equipamentos/{equipamento}', [EquipamentoController::class, 'destroy']);

// Tipo Portas
Route::get('/tipo-portas', [TipoPortaController::class, 'index']);
Route::get('/tipo-portas/create', [TipoPortaController::class, 'create']);
Route::post('/tipo-portas', [TipoPortaController::class, 'store']);
Route::get('/tipo-portas/{tipoPorta}', [TipoPortaController::class, 'show']);
Route::get('/tipo-portas/{tipoPorta}/edit', [TipoPortaController::class, 'edit']);
Route::put('/tipo-portas/{tipoPorta}', [TipoPortaController::class, 'update']);
Route::delete('/tipo-portas/{tipoPorta}', [TipoPortaController::class, 'destroy']);

// Plantas
Route::get('/plantas/public/{planta}', [PlantaController::class, 'showPublic']);
Route::get('/plantas/pdf/{planta}', [PlantaController::class, 'pdfPublic']);
Route::post('/plantas/{predio}', [PlantaController::class, 'store']);
Route::get('/plantas/{planta}/edit', [PlantaController::class, 'edit']);
Route::get('/plantas/{planta}/mark', [PlantaController::class, 'editMark']);
Route::delete('/plantas/{patch_panel_sala_id}/unmark', [PlantaController::class, 'unmark']);
Route::put('/plantas/{planta}/mark', [PlantaController::class, 'mark']);
Route::put('/plantas/{planta}', [PlantaController::class, 'update']);
Route::get('/plantas/{predio}/{planta}', [PlantaController::class, 'show']);
Route::get('/plantas/{predio}', [PlantaController::class, 'index']);
Route::delete('/plantas/{predio}/{planta}', [PlantaController::class, 'destroy']);

// Vincular portas de patch panels a salas
Route::get('/patch-panels/{patchPanel}/selecionar-sala', [PatchPanelController::class, 'selecionarSala']);
Route::get('/patch-panels/{patchPanel}/selecionar-tipo-porta/{sala}', [PatchPanelController::class, 'selecionarTipoPorta']);
Route::post('/patch-panels/{patchPanel}/vincular-sala', [PatchPanelController::class, 'vincularSala']); 
Route::delete('/patch-panels/{patchPanel}/desvincular-sala/{sala}', [PatchPanelController::class, 'desvincularSala']);

// Editar tipo de porta patch panels
Route::get('/patch-panels/{patchPanel}/editar-tipo-porta/{sala}', [PatchPanelController::class, 'editarTipoPorta']);
Route::put('/patch-panels/{patchPanel}/atualizar-tipo-porta/{sala}', [PatchPanelController::class, 'atualizarTipoPorta']);

// Vincular salas a patch panels
Route::get('/salas/{sala}/selecionar-rack', [SalaController::class, 'selecionarRack']);
Route::get('/salas/{sala}/selecionar-patchpanel/{rack}', [SalaController::class, 'selecionarPatchPanel']);
Route::post('/salas/{sala}/vincular-patchpanel', [SalaController::class, 'vincularPatchPanel']); 
Route::delete('/salas/{sala}/desvincular-patchpanel/{patchPanel}', [SalaController::class, 'desvincularPatchPanel']);

// Editar tipo de porta sakas 
Route::get('/salas/{sala}/editar-tipo-porta/{patchPanel}', [SalaController::class, 'editarTipoPorta']);
Route::put('/salas/{sala}/atualizar-tipo-porta/{patchPanel}', [SalaController::class, 'atualizarTipoPorta']);