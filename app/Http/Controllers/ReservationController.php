<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Lambda\LambdaClient;

class ReservationController extends Controller
{
    private $lambdaClient;

    public function __construct()
    {
        $this->lambdaClient = new LambdaClient([
            'region' => env('AWS_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
    }

    public function showReservations()
    {
        try {
            $result = $this->lambdaClient->invoke([
                'FunctionName' => 'disponiblidad_group5',
                'Payload' => json_encode([]),
            ]);

            $payload = json_decode($result->get('Payload')->getContents(), true);

            if (!isset($payload['body'])) {
                return view('reservation', ['error' => 'Error: Respuesta inválida de Lambda']);
            }

            $body = json_decode($payload['body'], true);

            if (!isset($body['disponibilidad']) || empty($body['disponibilidad'])) {
                return view('reservation', ['error' => 'Error: No se encontraron actividades disponibles']);
            }

            $disponibilidad = $body['disponibilidad'];

            return view('reservation', ['disponibilidad' => $disponibilidad]);

        } catch (\Exception $e) {
            return view('reservation', ['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
        }
    }

    public function makeReservation(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email',
            'actividad' => 'required|string',
            'fecha' => 'required|date',
            'asientos' => 'required|integer|min:1',
        ]);

        try {
            $result = $this->lambdaClient->invoke([
                'FunctionName' => 'reservar_group5',
                'Payload' => json_encode($validated),
            ]);

            $payload = json_decode($result->get('Payload')->getContents(), true);

            if ($payload['statusCode'] !== 200) {
                return back()->withErrors(['error' => $payload['body']]);
            }

            $body = json_decode($payload['body'], true);

            return back()->with('success', $body['mensaje'])->with('reserva', $body['reserva']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al procesar la reserva: ' . $e->getMessage()]);
        }
    }
}
