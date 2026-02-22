<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProdutoController extends Controller
{
    private function resolveEmpresaId(Request $request): int
    {
        $empresaId = auth()->user()->id_empresa ?? $request->get('id_empresa');
        if (!$empresaId) {
            throw ValidationException::withMessages([
                'id_empresa' => ['id_empresa e obrigatorio quando nao ha usuario autenticado.'],
            ]);
        }
        return (int) $empresaId;
    }

    /**
     * Lista todos os produtos com filtros dinâmicos.
     */
    public function index(Request $request)
    {
        $query = Produto::query();

        // Filtros dinâmicos
        if ($request->has('id_produto')) {
            $query->where('id_produto', $request->id_produto);
        }

        if ($request->has('id_empresa')) {
            $query->where('id_empresa', $request->id_empresa);
        }

        if ($request->has('id_categoria')) {
            $query->where('id_categoria', $request->id_categoria);
        }

        if ($request->has('id_secao')) {
            $query->where('id_secao', $request->id_secao);
        }

        if ($request->has('id_grupo')) {
            $query->where('id_grupo', $request->id_grupo);
        }

        if ($request->has('id_subgrupo')) {
            $query->where('id_subgrupo', $request->id_subgrupo);
        }

        if ($request->has('codigo_barras')) {
            $query->where('codigo_barras', $request->codigo_barras);
        }

        if ($request->has('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        // Paginação (20 por página)
        $produtos = $query->orderBy('id_produto', 'desc')->paginate(20);

        return response()->json($produtos);
    }

    /**
     * Mostra um produto específico.
     */
    public function show($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        return response()->json($produto);
    }

    /**
     * Cadastra um novo produto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_empresa' => 'required|integer',
            'descricao' => 'required|string|max:255',
            'preco_custo' => 'nullable|numeric',
            'preco_venda' => 'nullable|numeric',
        ]);

        $produto = Produto::create($validated + $request->only([
            'id_categoria',
            'id_secao',
            'id_grupo',
            'id_subgrupo',
            'codigo_barras',
            'unidade_medida',
            'ativo'
        ]));

        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'produto' => $produto
        ], 201);
    }

    public function importar(Request $request)
    {
        $data = $request->validate([
            'id_empresa' => 'nullable|integer|exists:tb_empresas,id_empresa',
            'arquivo' => 'required|file|mimes:xls,xlsx,csv,txt',
            'modo' => 'nullable|in:upsert,skip,update',
            'delimiter' => 'nullable|string|max:2',
            'detalhado' => 'nullable|boolean',
            'id_categoria' => 'nullable|integer',
            'id_secao' => 'nullable|integer',
            'id_grupo' => 'nullable|integer',
            'id_subgrupo' => 'nullable|integer',
            'unidade_medida' => 'nullable|string|max:10',
            'ativo' => 'nullable|boolean',
        ]);

        $empresaId = $this->resolveEmpresaId($request);
        $modo = $data['modo'] ?? 'upsert';

        $file = $request->file('arquivo');
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = $this->parseFile($file->getRealPath(), $ext, $data['delimiter'] ?? null);

        if (!$rows) {
            return response()->json([
                'success' => false,
                'message' => 'Arquivo vazio ou formato nao suportado.',
            ], 422);
        }

        $headerRow = $rows[0] ?? [];
        $headerMap = $this->buildHeaderMap($headerRow);
        $hasHeader = $headerMap['has_header'];
        $map = $headerMap['map'];

        if (!$hasHeader) {
            $map = [
                'codigo_barras' => 0,
                'descricao' => 1,
                'unidade_medida' => 2,
                'preco_custo' => 3,
                'preco_venda' => 4,
            ];
        }

        $startIndex = $hasHeader ? 1 : 0;

        $resumo = [
            'total_linhas' => 0,
            'importados' => 0,
            'atualizados' => 0,
            'ignorados' => 0,
            'erros' => 0,
        ];
        $erros = [];
        $detalhes = [];
        $detalhar = $request->boolean('detalhado', true);

        DB::beginTransaction();
        try {
            for ($i = $startIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                $resumo['total_linhas']++;

                try {
                    $descricao = $this->getCell($row, $map['descricao'] ?? null);
                    if (!$descricao) {
                        $resumo['ignorados']++;
                        if ($detalhar) {
                            $detalhes[] = [
                                'linha' => $i + 1,
                                'status' => 'ignorado',
                                'motivo' => 'Descricao vazia',
                            ];
                        }
                        continue;
                    }

                    $codigoBarras = $this->getCell($row, $map['codigo_barras'] ?? null);
                    $unidade = $this->getCell($row, $map['unidade_medida'] ?? null);
                    $precoCusto = $this->parseDecimal($this->getCell($row, $map['preco_custo'] ?? null));
                    $precoVenda = $this->parseDecimal($this->getCell($row, $map['preco_venda'] ?? null));

                    $payload = [
                        'id_empresa' => $empresaId,
                        'id_categoria' => $data['id_categoria'] ?? null,
                        'id_secao' => $data['id_secao'] ?? null,
                        'id_grupo' => $data['id_grupo'] ?? null,
                        'id_subgrupo' => $data['id_subgrupo'] ?? null,
                        'codigo_barras' => $codigoBarras,
                        'descricao' => $descricao,
                        'unidade_medida' => $unidade ?: ($data['unidade_medida'] ?? 'UN'),
                        'preco_custo' => $precoCusto ?? 0,
                        'preco_venda' => $precoVenda ?? 0,
                        'ativo' => array_key_exists('ativo', $data) ? (int) $data['ativo'] : 1,
                    ];

                    if (!$codigoBarras) {
                        $existsDescricao = Produto::where('id_empresa', $empresaId)
                            ->where('descricao', $descricao)
                            ->exists();
                        if ($existsDescricao) {
                            $resumo['ignorados']++;
                            if ($detalhar) {
                                $detalhes[] = [
                                    'linha' => $i + 1,
                                    'status' => 'ignorado',
                                    'motivo' => 'Descricao duplicada sem codigo de barras',
                                    'descricao' => $descricao,
                                ];
                            }
                            continue;
                        }
                    }

                    $query = Produto::where('id_empresa', $empresaId);
                    if ($codigoBarras) {
                        $query->where('codigo_barras', $codigoBarras);
                    } else {
                        $query->where('descricao', $descricao);
                    }

                    $existing = $query->first();

                    if ($existing) {
                        if ($modo === 'skip') {
                            $resumo['ignorados']++;
                            if ($detalhar) {
                                $detalhes[] = [
                                    'linha' => $i + 1,
                                    'status' => 'ignorado',
                                    'motivo' => 'Registro ja existe',
                                    'codigo_barras' => $codigoBarras,
                                    'descricao' => $descricao,
                                ];
                            }
                            continue;
                        }
                        $existing->update($payload);
                        $resumo['atualizados']++;
                        if ($detalhar) {
                            $detalhes[] = [
                                'linha' => $i + 1,
                                'status' => 'atualizado',
                                'codigo_barras' => $codigoBarras,
                                'descricao' => $descricao,
                            ];
                        }
                    } else {
                        if ($modo === 'update') {
                            $resumo['ignorados']++;
                            if ($detalhar) {
                                $detalhes[] = [
                                    'linha' => $i + 1,
                                    'status' => 'ignorado',
                                    'motivo' => 'Registro nao encontrado para atualizar',
                                    'codigo_barras' => $codigoBarras,
                                    'descricao' => $descricao,
                                ];
                            }
                            continue;
                        }
                        Produto::create($payload);
                        $resumo['importados']++;
                        if ($detalhar) {
                            $detalhes[] = [
                                'linha' => $i + 1,
                                'status' => 'importado',
                                'codigo_barras' => $codigoBarras,
                                'descricao' => $descricao,
                            ];
                        }
                    }
                } catch (\Throwable $rowError) {
                    $resumo['erros']++;
                    $erros[] = ['linha' => $i + 1, 'erro' => $rowError->getMessage()];
                    if ($detalhar) {
                        $detalhes[] = [
                            'linha' => $i + 1,
                            'status' => 'erro',
                            'motivo' => $rowError->getMessage(),
                        ];
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $resumo['erros']++;
            $erros[] = ['linha' => null, 'erro' => $e->getMessage()];
        }

        return response()->json([
            'success' => true,
            'resumo' => $resumo,
            'erros' => $erros,
            'detalhes' => $detalhar ? $detalhes : [],
        ]);
    }

    /**
     * Atualiza um produto existente.
     */
    public function update(Request $request, $id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $produto->update($request->all());

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'produto' => $produto
        ]);
    }

    /**
     * Exclui um produto.
     */
    public function destroy($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto excluído com sucesso.']);
    }

    /**
     * Lista produtos por empresa.
     */
    public function listarPorEmpresa($id_empresa)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->orderBy('id_produto', 'desc')
            ->paginate(20);

        return response()->json($produtos);
    }

    /**
     * Lista produtos por categoria.
     */
    public function listarPorCategoria($id_empresa, $id_categoria)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_categoria', $id_categoria)
            ->paginate(20);
        return response()->json($produtos);
    }

    /**
     * Lista produtos por seção.
     */
    public function listarPorSecao($id_empresa, $id_secao)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_secao', $id_secao)
            ->paginate(20);
        return response()->json($produtos);
    }

    /**
     * Lista produtos por grupo.
     */
    public function listarPorGrupo($id_empresa, $id_grupo)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_grupo', $id_grupo)
            ->paginate(20);
        return response()->json($produtos);
    }

    /**
     * Lista produtos por subgrupo.
     */
    public function listarPorSubgrupo($id_empresa, $id_subgrupo)
    {
        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_subgrupo', $id_subgrupo)
            ->paginate(20);
        return response()->json($produtos);
    }

    /**
     * Lista produtos por empresa e categoria via rota /categorias/empresa/{id_empresa}/produtos?id_categoria=
     */
    public function listarPorEmpresaCategoria(Request $request, $id_empresa)
    {
        $idCategoria = $request->get('id_categoria');
        if (!$idCategoria) {
            return response()->json(['message' => 'id_categoria é obrigatório.'], 422);
        }

        $produtos = Produto::where('id_empresa', $id_empresa)
            ->where('id_categoria', $idCategoria)
            ->paginate(20);

        return response()->json($produtos);
    }

    private function parseFile(string $path, string $ext, ?string $delimiter = null): array
    {
        if (in_array($ext, ['csv', 'txt'], true)) {
            return $this->readDelimited($path, $delimiter);
        }

        if (in_array($ext, ['xls', 'xlsx'], true)) {
            $sheet = IOFactory::load($path)->getActiveSheet();
            return $sheet->toArray(null, true, true, false);
        }

        return [];
    }

    private function readDelimited(string $path, ?string $delimiter = null): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        $delimiter = $delimiter ?: $this->detectDelimiter($firstLine);
        rewind($handle);

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($data === [null] || $data === false) {
                continue;
            }
            $rows[] = $data;
        }

        fclose($handle);
        return $rows;
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $best = ',';
        $max = 0;
        foreach ($delimiters as $delim) {
            $count = substr_count($line, $delim);
            if ($count > $max) {
                $max = $count;
                $best = $delim;
            }
        }
        return $best;
    }

    private function buildHeaderMap(array $headerRow): array
    {
        $map = [];
        $hasHeader = false;

        foreach ($headerRow as $index => $header) {
            $norm = $this->normalizeHeader($header);
            if (!$norm) {
                continue;
            }

            if (str_contains($norm, 'CODIGO') && str_contains($norm, 'BARRAS')) {
                $map['codigo_barras'] = $index;
                $hasHeader = true;
            } elseif (str_contains($norm, 'DESCRICAO')) {
                $map['descricao'] = $index;
                $hasHeader = true;
            } elseif (str_contains($norm, 'UNIDADE')) {
                $map['unidade_medida'] = $index;
                $hasHeader = true;
            } elseif (str_contains($norm, 'PRECO') && str_contains($norm, 'CUSTO')) {
                $map['preco_custo'] = $index;
                $hasHeader = true;
            } elseif (str_contains($norm, 'PRECO') && str_contains($norm, 'VENDA')) {
                $map['preco_venda'] = $index;
                $hasHeader = true;
            }
        }

        return [
            'has_header' => $hasHeader,
            'map' => $map,
        ];
    }

    private function normalizeHeader($value): string
    {
        if ($value === null) {
            return '';
        }
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        $value = mb_strtoupper($value, 'UTF-8');
        $trans = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        if ($trans !== false) {
            $value = $trans;
        }
        $value = preg_replace('/[^A-Z0-9]+/', '_', $value);
        return trim($value, '_');
    }

    private function getCell(array $row, ?int $index): ?string
    {
        if ($index === null) {
            return null;
        }
        if (!array_key_exists($index, $row)) {
            return null;
        }
        $value = $row[$index];
        if ($value === null) {
            return null;
        }
        return trim((string) $value);
    }

    private function parseDecimal(?string $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }

        $value = preg_replace('/[^0-9\.-]/', '', $value);
        if ($value === '' || $value === '-' || $value === '.') {
            return null;
        }

        return (float) $value;
    }
}
