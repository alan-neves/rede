<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Adicionar Nova Planta Baixa</h5>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" action="/plantas/{{ $predio->id }}">
            @csrf
            <input type="hidden" name="predio_id" value="{{ $predio->id }}">

            <!-- Campo: Nome da Planta -->
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nome da Planta</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       placeholder="Ex: Pavimento Térreo, Subsolo 1..." 
                       required>
            </div>

            <!-- Campo: Upload de Arquivo SVG -->
            <div class="mb-3">
                <label for="planta" class="form-label fw-semibold">Arquivo SVG da Planta Baixa</label>
                <input type="file" 
                       id="planta" 
                       name="planta" 
                       accept=".svg" 
                       class="form-control" 
                       required>
                <div class="form-text">Selecione apenas arquivos no formato .SVG</div>
            </div>

            <!-- Botão de Envio -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-upload me-1"></i> Enviar Planta
                </button>
            </div>
        </form>
    </div>
</div>