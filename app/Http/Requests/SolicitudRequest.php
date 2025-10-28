<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fecha_solicitud' => 'required|date',
            'tecnico_id' => 'required|exists:users,id',
            'clinica_id' => 'required|exists:centros_medicos,id',
            'razon' => 'required|string|min:10',
            'repuestos' => 'required|array|min:1',
            'repuestos.*.repuesto_id' => 'required|exists:repuestos,id',
            'repuestos.*.cantidad' => 'required|integer|min:1',
            'repuestos.*.observacion' => 'nullable|string',
            'repuestos.*.orden' => 'nullable|integer|min:1'
        ];
    }

    public function messages()
    {
        return [
            'fecha_solicitud.required' => 'La fecha de solicitud es obligatoria',
            'tecnico_id.required' => 'Debe seleccionar un técnico',
            'tecnico_id.exists' => 'El técnico seleccionado no es válido',
            'clinica_id.required' => 'Debe seleccionar una clínica',
            'clinica_id.exists' => 'La clínica seleccionada no es válida',
            'razon.required' => 'Debe ingresar el motivo de la solicitud',
            'razon.min' => 'El motivo debe tener al menos 10 caracteres',
            'repuestos.required' => 'Debe seleccionar al menos un repuesto',
            'repuestos.min' => 'Debe seleccionar al menos un repuesto',
            'repuestos.*.repuesto_id.required' => 'Debe seleccionar un repuesto',
            'repuestos.*.repuesto_id.exists' => 'El repuesto seleccionado no es válido',
            'repuestos.*.cantidad.required' => 'Debe especificar la cantidad',
            'repuestos.*.cantidad.min' => 'La cantidad debe ser mayor a 0'
        ];
    }
}
