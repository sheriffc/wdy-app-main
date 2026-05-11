<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TsctmisService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.tsctmis.url'), '/');
        $this->apiKey  = config('services.tsctmis.api_key');
    }

    public function syncNonPayrollTeachers(): array
    {
        $response = Http::withHeaders(['X-Wideya-API-Key' => $this->apiKey])
            ->timeout(30)
            ->get("{$this->baseUrl}/wideya/teachers");  // tsctmis has no /api prefix

        if (!$response->successful()) {
            Log::error('TsctmisService: failed to fetch non-payroll teachers', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException("TSCTMIS API returned HTTP {$response->status()}");
        }

        $teachers = $response->json('data', []);
        $now      = now();
        $inserted = 0;
        $updated  = 0;

        foreach ($teachers as $t) {
            $nin = $t['national_id'] ?? null;

            // Deduplicate by NIN (national ID) if present, otherwise by reference_code stored in nassit_number
            $existing = $nin
                ? DB::table('teacher_payroll')->where('nin', $nin)->first()
                : DB::table('teacher_payroll')->where('nassit_number', 'TMIS-' . $t['reference_code'])->first();

            $row = [
                'school_sid'    => null,
                'first_name'    => $t['first_name'] ?? null,
                'middle_name'   => $t['middle_name'] ?? null,
                'last_name'     => $t['last_name'] ?? null,
                'sex'           => $t['sex'] ?? null,
                'date_of_birth' => $t['date_of_birth'] ?? null,
                'pin'           => null,              // null = non-payroll; Android sets employment_status_oid = 'nonpayroll'
                'nin'           => $nin,
                'nassit_number' => 'TMIS-' . $t['reference_code'],  // store reference_code for traceability
                'yearmonth'     => 0,
                'updated_at'    => $now,
                'synced_at'     => $now,
            ];

            if ($existing) {
                DB::table('teacher_payroll')->where('uuid', $existing->uuid)->update($row);
                $updated++;
            } else {
                DB::table('teacher_payroll')->insert(array_merge($row, [
                    'uuid'       => (string) Str::uuid(),
                    'created_at' => $now,
                    'deleted_at' => null,
                ]));
                $inserted++;
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated, 'total' => count($teachers)];
    }
}
