<?php

namespace App\Services\Evolution;

use App\Models\Configuration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionService
{
    public static function createInstance()
    {
        $validatedData = [
            'instanceName' => config('app.name'),
            'integration' => config('services.evolution.type'),
        ];

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.key'),
        ])->post(config('services.evolution.url') . '/instance/create', $validatedData);

        if (!$response->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar instancia.',
            ], $response->status());
        }

        $config = Configuration::first();
        $config->update([
            'instance_uuid' => $response->json('instance')['instanceId'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Instancia criada com sucesso.',
            'data' => $response->json(),
        ], 201);
    }

    public static function deleteInstance()
    {
        $instanceName = config('app.name');

        $response = Http::withHeaders([
            'apikey' => config('services.evolution.key'),
        ])->delete(config('services.evolution.url') . '/instance/delete/' . $instanceName);

        if (!$response->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao deletar instancia.',
            ], $response->status());
        }

        $config = Configuration::first();
        $config->update([
            'instance_uuid' => null,
            'instance_status' => null
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Instancia deletada com sucesso.',
            'data' => $response->json(),
        ], 201);
    }

    public static function connectInstance()
    {
        try{
            $instance = config('app.name');

            $response = Http::withHeaders([
                'apikey' => config('services.evolution.key'),
            ])->get(config('services.evolution.url') . '/instance/connect/' . $instance);

            return $response->json();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Falha ao conectar instancia',
            ]);
        }
    }

    public static function stateInstance()
    {
       try {
            $instance = config('app.name');

            $response = Http::withHeaders([
                'apikey' => config('services.evolution.key'),
            ])->get(config('services.evolution.url') . '/instance/connectionState/' . $instance);

            if(!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erro ao conectar esta instancia.',
                ], 400);
             }
            $status = $response->json('instance')['state'];

            if($status){
                return response()->json([
                    'status' => 'success',
                    'message' => $status,
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao conectar esta instancia.',
            ], 401);

       } catch (\Exception $e) {
           Log::error($e->getMessage());
           return response()->json([
               'status' => 'error',
               'message' => 'Falha ao conectar instancia',
           ], 400);
       }

    }

    public static function disconnectInstance()
    {
        try {
            $instance = config('app.name');

            $response = Http::withHeaders([
                'apikey' => config('services.evolution.key'),
            ])->delete(config('services.evolution.url') . '/instance/logout/' . $instance);

            return $response->json();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Falha ao conectar instancia',
            ]);
        }
    }

    public function sendText($number, $text)
    {
        try {
            $instance = config('app.name');
            $delay = rand(3000, 7000);

            if(!$number || !$text){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Número e mensagem necessários para envio',
                ]);
            }

            $response = Http::withHeaders([
                'apikey' => config('services.evolution.key'),
            ])->post(config('services.evolution.url') . '/message/sendText/' . $instance, [
                'number' => $number,
                'text' => $text,
                'delay' => $delay,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
