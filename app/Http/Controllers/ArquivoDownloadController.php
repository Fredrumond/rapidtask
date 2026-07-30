<?php

namespace App\Http\Controllers;

use App\Models\ProjetoArquivo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArquivoDownloadController extends Controller
{
    public function __invoke(ProjetoArquivo $arquivo): StreamedResponse
    {
        $this->authorize('view', $arquivo);

        abort_unless(Storage::disk('local')->exists($arquivo->src), 404);

        return Storage::disk('local')->download($arquivo->src, $arquivo->nome);
    }
}
