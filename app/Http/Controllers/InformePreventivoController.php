<?php

namespace App\Http\Controllers;

use App\Models\CentroMedico;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\InformePreventivo;
use App\Models\Repuesto;
use App\Models\TipoInformePreventivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class InformePreventivoController extends Controller
{
    public function selectTipo(): View
    {
        $tipos = TipoInformePreventivo::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('informes.preventivos.select_tipo', compact('tipos'));
    }

    public function create(TipoInformePreventivo $tipoInformePreventivo): View
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $centrosMedicos = CentroMedico::orderBy('centro_dialisis')->get();
        $equipos = Equipo::orderBy('nombre')
            ->when($tipoInformePreventivo->tipo_equipo_id, function ($query) use ($tipoInformePreventivo) {
                $query->where('tipo_equipo_id', $tipoInformePreventivo->tipo_equipo_id);
            })
            ->get();
        $repuestos = Repuesto::orderBy('nombre')->get();
        $siguienteNumero = $this->generarNumeroPreventivo();
        $configKey = $this->resolveConfigKey($tipoInformePreventivo);

        $view = $this->resolveCreateView($tipoInformePreventivo);

        return view($view, compact(
            'tipoInformePreventivo',
            'clientes',
            'centrosMedicos',
            'equipos',
            'repuestos',
            'siguienteNumero',
            'configKey'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipo_informe_preventivo_id' => ['required', 'exists:tipo_informe_preventivo,id'],
            'fecha' => ['required', 'date'],
            'centro_medico_id' => ['required', 'exists:centros_medicos,id'],
            'equipo_id' => ['required', 'exists:equipos,id'],
            'tipo_trabajo' => ['required', 'in:T1,T2,T3,T4,Anual,Semestral,Trimestral,Ocasional,Otro'],
            'condicion_equipo' => ['required', 'in:Operativo,En observacion,Fuera de servicio,Baja'],
            'horas_operacion' => ['required', 'integer', 'min:0'],
            'comentarios' => ['nullable', 'string', 'max:500'],
            'fecha_proximo_control' => ['nullable', 'date'],
            'firma_tecnico' => ['required', 'string'],
            'firma_cliente' => ['nullable', 'string'],
            'firma_cliente_nombre' => ['nullable', 'string', 'max:150'],
            'inspecciones' => ['nullable', 'array'],
            'inspecciones.*' => ['nullable', 'string', 'max:255'],
            'inspecciones_text' => ['nullable', 'array'],
            'inspecciones_text.*' => ['nullable', 'string', 'max:255'],
            'inspecciones_comment' => ['nullable', 'array'],
            'inspecciones_comment.*' => ['nullable', 'string', 'max:255'],
            'inspecciones_comment_group' => ['nullable', 'array'],
            'inspecciones_comment_group.*' => ['nullable', 'array'],
            'inspecciones_comment_group.*.*' => ['nullable', 'string', 'max:255'],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*.repuesto_id' => ['nullable', 'required_with:repuestos.*.cantidad', 'exists:repuestos,id'],
            'repuestos.*.cantidad' => ['nullable', 'required_with:repuestos.*.repuesto_id', 'integer', 'min:1'],
        ], [
            'repuestos.*.repuesto_id.exists' => 'El repuesto seleccionado es inválido.',
            'repuestos.*.repuesto_id.required_with' => 'Selecciona un repuesto cuando indiques una cantidad.',
            'repuestos.*.cantidad.required_with' => 'Ingresa una cantidad para el repuesto seleccionado.',
            'repuestos.*.cantidad.integer' => 'La cantidad de cada repuesto debe ser un número entero.',
            'repuestos.*.cantidad.min' => 'La cantidad mínima por repuesto es 1.',
        ]);

        $equipo = Equipo::find($data['equipo_id']);
        if ($equipo && $data['horas_operacion'] < (int) $equipo->horas_uso) {
            throw ValidationException::withMessages([
                'horas_operacion' => 'Las horas de operación deben ser mayores o iguales a las horas actuales del equipo (' . $equipo->horas_uso . ').',
            ]);
        }

        $tipoInformePreventivo = TipoInformePreventivo::findOrFail($data['tipo_informe_preventivo_id']);
        if ($tipoInformePreventivo->tipo_equipo_id && $equipo) {
            if ((int) $equipo->tipo_equipo_id !== (int) $tipoInformePreventivo->tipo_equipo_id) {
                throw ValidationException::withMessages([
                    'equipo_id' => 'El equipo seleccionado no corresponde al tipo de informe preventivo.',
                ]);
            }
        }

        $configKey = $this->resolveConfigKey($tipoInformePreventivo);
        $blueprint = $this->getInspeccionesBlueprint($configKey);
        $inspeccionesRadio = $data['inspecciones'] ?? [];
        $inspeccionesTexto = $data['inspecciones_text'] ?? [];
        $inspeccionesComentarios = $data['inspecciones_comment'] ?? [];
        $inspeccionesComentariosGrupo = $data['inspecciones_comment_group'] ?? [];

        $this->validateInspeccionesRespuestas($blueprint, $inspeccionesRadio, $inspeccionesTexto, $inspeccionesComentarios, $inspeccionesComentariosGrupo);
        $numeroReporte = $this->generarNumeroPreventivo();

        $informe = null;

        DB::transaction(function () use (&$informe, $data, $numeroReporte, $equipo, $tipoInformePreventivo, $blueprint, $inspeccionesRadio, $inspeccionesTexto, $inspeccionesComentarios, $inspeccionesComentariosGrupo) {
            $informe = InformePreventivo::create([
                'tipo_informe_preventivo_id' => $tipoInformePreventivo->id,
                'numero_reporte_servicio' => $numeroReporte,
                'fecha' => $data['fecha'],
                'usuario_id' => Auth::id(),
                'centro_medico_id' => $data['centro_medico_id'],
                'equipo_id' => $data['equipo_id'],
                'tipo_trabajo' => $data['tipo_trabajo'],
                'condicion_equipo' => $data['condicion_equipo'],
                'horas_operacion' => $data['horas_operacion'],
                'comentarios' => $data['comentarios'] ?? null,
                'fecha_proximo_control' => $data['fecha_proximo_control'] ?? null,
                'firma_tecnico' => $data['firma_tecnico'],
                'firma_cliente' => $data['firma_cliente'] ?? null,
                'firma_cliente_nombre' => $data['firma_cliente_nombre'] ?? null,
            ]);

            if ($equipo) {
                $equipo->update([
                    'horas_uso' => $data['horas_operacion'],
                    'tipo_mantencion' => $data['tipo_trabajo'],
                    'estado' => $data['condicion_equipo'],
                    'proxima_mantencion' => $data['fecha_proximo_control'] ?? $equipo->proxima_mantencion,
                ]);
            }

            $this->registrarInspecciones($informe, $blueprint, $inspeccionesRadio, $inspeccionesTexto, $inspeccionesComentarios, $inspeccionesComentariosGrupo);
            $this->registrarRepuestos($informe, $data['repuestos'] ?? []);
        });

        if (! $informe) {
            throw new RuntimeException('No se pudo generar el informe preventivo.');
        }

        return redirect()
            ->route('informes.preventivo.show', $informe->id)
            ->with('status', 'Informe preventivo generado exitosamente.');
    }

    protected function generarNumeroPreventivo(): string
    {
        $user = Auth::user();
        $name = trim($user?->name ?? 'ASC');
        $words = preg_split('/\s+/', $name);
        $iniciales = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }

            $iniciales .= mb_strtoupper(mb_substr($word, 0, 1));

            if (mb_strlen($iniciales) >= 2) {
                break;
            }
        }

        if ($iniciales === '') {
            $iniciales = 'ASC';
        }

        $ultimoNumero = InformePreventivo::where('numero_reporte_servicio', 'LIKE', $iniciales . '%')
            ->max('numero_reporte_servicio');

        if ($ultimoNumero) {
            $correlativo = (int) substr($ultimoNumero, mb_strlen($iniciales)) + 1;
        } else {
            $correlativo = 1;
        }

        return $iniciales . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
    }

    protected function resolveCreateView(TipoInformePreventivo $tipoInformePreventivo): string
    {
        return match ($tipoInformePreventivo->nombre) {
            'Fresenius' => 'informes.preventivos.create_prev_fresenius',
            'Autoclave' => 'informes.preventivos.create_prev_autoclave',
            'Aspirador de Secreciones' => 'informes.preventivos.create_prev_asp_secreciones',
            'Aspirador' => 'informes.preventivos.create_prev_asp_secreciones',
            'Balanzas' => 'informes.preventivos.create_prev_balanzas',
            'DEA' => 'informes.preventivos.create_prev_DEA',
            default => 'informes.preventivos.create_prev_otros',
        };
    }

    protected function resolveConfigKey(TipoInformePreventivo $tipoInformePreventivo): string
    {
        return match ($tipoInformePreventivo->nombre) {
            'Fresenius' => 'fresenius',
            'Autoclave' => 'autoclave',
            'Aspirador de Secreciones' => 'aspirador',
            'Aspirador' => 'aspirador',
            'Balanzas' => 'balanzas',
            'DEA' => 'dea',
            default => 'fresenius',
        };
    }

    protected function validateInspeccionesRespuestas(array $blueprint, array $radios, array $textos, array $comentarios, array $grupos): void
    {
        $errors = [];

        foreach ($blueprint as $index => $item) {
            $type = $item['type'] ?? 'options';
            $requiresComment = (bool) ($item['requires_comment'] ?? false);
            $commentFields = $item['comment_fields'] ?? null;
            $label = $item['label'] ?? 'este ítem';

            if ($type === 'text') {
                if ($requiresComment) {
                    $textValue = trim((string) ($textos[$index] ?? ''));

                    if ($textValue === '') {
                        $errors["inspecciones_text.$index"] = 'Ingresa un valor para "' . $label . '".';
                    } elseif (mb_strlen($textValue) > 255) {
                        $errors["inspecciones_text.$index"] = 'El texto de "' . $label . '" no puede superar los 255 caracteres.';
                    }
                }

                continue;
            }

            $value = $radios[$index] ?? null;
            $options = $item['options'] ?? ['SI', 'NO', 'N/A'];

            if (! $value || ! in_array($value, $options, true)) {
                $errors["inspecciones.$index"] = 'Selecciona una opción válida.';
                continue;
            }

            if ($requiresComment && $value === 'SI') {
                if (is_array($commentFields) && ! empty($commentFields)) {
                    $groupValues = $grupos[$index] ?? [];
                    foreach ($commentFields as $field) {
                        $key = $field['key'] ?? null;
                        if (! $key) {
                            continue;
                        }
                        $rawValue = trim((string) ($groupValues[$key] ?? ''));
                        if ($rawValue === '') {
                            $errors["inspecciones_comment_group.$index.$key"] = 'Ingresa el valor para ' . ($field['label'] ?? 'este subcampo') . '.';
                        } elseif (mb_strlen($rawValue) > 255) {
                            $errors["inspecciones_comment_group.$index.$key"] = 'El valor no puede superar los 255 caracteres.';
                        }
                    }
                } else {
                    $comment = trim((string) ($comentarios[$index] ?? ''));

                    if ($comment === '') {
                        $errors["inspecciones_comment.$index"] = 'Ingresa el valor asociado a "' . $label . '".';
                    } elseif (mb_strlen($comment) > 255) {
                        $errors["inspecciones_comment.$index"] = 'El comentario de "' . $label . '" no puede superar los 255 caracteres.';
                    }
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function registrarInspecciones(InformePreventivo $informe, array $blueprint, array $radios, array $textos, array $comentarios, array $comentariosGrupo): void
    {
        foreach ($blueprint as $index => $item) {
            $type = $item['type'] ?? 'options';
            $requiresComment = (bool) ($item['requires_comment'] ?? false);
            $commentFields = $item['comment_fields'] ?? null;

            if ($type === 'text') {
                $respuesta = $textos[$index] ?? null;
                $comentario = null;
            } else {
                $respuesta = $radios[$index] ?? null;
                $comentario = null;

                if ($requiresComment && $respuesta === 'SI') {
                    if (is_array($commentFields) && ! empty($commentFields)) {
                        $groupValues = $comentariosGrupo[$index] ?? [];
                        $normalized = [];
                        foreach ($commentFields as $field) {
                            $key = $field['key'] ?? null;
                            if (! $key) {
                                continue;
                            }
                            $value = trim((string) ($groupValues[$key] ?? ''));
                            if ($value !== '') {
                                $normalized[$key] = $value;
                            }
                        }
                        if (! empty($normalized)) {
                            $comentario = json_encode([
                                '__structured' => true,
                                'values' => $normalized,
                            ], JSON_UNESCAPED_UNICODE);
                        }
                    } else {
                        $comentario = trim((string) ($comentarios[$index] ?? '')) ?: null;
                    }
                }
            }

            $informe->inspecciones()->create([
                'descripcion' => $item['label'] ?? '—',
                'respuesta' => $respuesta,
                'comentario' => $comentario,
            ]);
        }
    }

    protected function registrarRepuestos(InformePreventivo $informe, array $repuestos): void
    {
        if (empty($repuestos)) {
            return;
        }

        foreach ($repuestos as $registro) {
            $repuestoId = $registro['repuesto_id'] ?? null;
            $cantidad = isset($registro['cantidad']) ? (int) $registro['cantidad'] : null;

            if (! $repuestoId || ! $cantidad) {
                continue;
            }

            $repuesto = Repuesto::whereKey($repuestoId)->lockForUpdate()->first();

            if (! $repuesto || $repuesto->stock < $cantidad) {
                throw ValidationException::withMessages([
                    'repuestos' => [
                        "Stock insuficiente para el repuesto seleccionado (ID: {$repuestoId}).",
                    ],
                ]);
            }

            $informe->repuestos()->create([
                'repuesto_id' => $repuestoId,
                'cantidad' => $cantidad,
            ]);

            $repuesto->decrement('stock', $cantidad);
        }
    }

    protected function getInspeccionesBlueprint(string $configKey): array
    {
        $config = config('preventivos.' . $configKey, config('preventivos.fresenius'));
        $items = [];

        foreach ($config['sections'] ?? [] as $section) {
            foreach ($section['items'] ?? [] as $item) {
                $items[] = [
                    'label' => $item['label'] ?? '—',
                    'type' => $item['type'] ?? 'options',
                    'options' => $item['options'] ?? ['SI', 'NO', 'N/A'],
                    'requires_comment' => (bool) ($item['requires_comment'] ?? false),
                    'comment_placeholder' => $item['comment_placeholder'] ?? null,
                    'comment_suffix' => $item['comment_suffix'] ?? null,
                    'comment_fields' => $item['comment_fields'] ?? null,
                ];
            }
        }

        return $items;
    }
}
