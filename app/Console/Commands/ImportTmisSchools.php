<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Ramsey\Uuid\Uuid;

class ImportTmisSchools extends Command
{
    protected $signature   = 'import:tmis-schools';
    protected $description = 'Import / upsert schools from public/my_uploads/tmis_school_list_2025.xlsx';

    private array $edLevelMap = [
        'A. Pre-Primary'      => 'pre',
        'B. Primary'          => 'pri',
        'C. Junior Secondary' => 'jss',
        'D. Senior Secondary' => 'sss',
    ];

    public function handle(): int
    {
        $file = public_path('my_uploads/tmis_school_list_2025.xlsx');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info('Loading spreadsheet…');
        $rows = IOFactory::load($file)->getActiveSheet()->toArray();
        array_shift($rows); // remove header

        // --- build lookup maps ---

        // TMIS district_id → [geo_id, do_uuid]
        $districtMap = [];
        $districtOffices = DB::table('district_office')
            ->join('geo', function ($j) {
                $j->on('geo.name', '=', 'district_office.name')
                  ->where('geo.type', '=', 2);
            })
            ->select('district_office.district_id', 'district_office.uuid as do_uuid', 'geo.id as geo_id')
            ->get();

        foreach ($districtOffices as $d) {
            $districtMap[(int) $d->district_id] = [
                'do_uuid' => $d->do_uuid,
                'geo_id'  => $d->geo_id,
            ];
        }

        // chiefdom_code → geo.id
        $chiefdomMap = [];
        DB::table('geo')
            ->where('type', 3)
            ->whereNotNull('fabinc_recordid')
            ->select('id', 'fabinc_recordid')
            ->get()
            ->each(function ($row) use (&$chiefdomMap) {
                $chiefdomMap[(int) $row->fabinc_recordid] = $row->id;
            });

        // existing schools keyed by emis_id
        $existing = DB::table('school')
            ->whereNotNull('emis_id')
            ->pluck('uuid', 'emis_id');

        $now     = now()->toDateTimeString();
        $insert  = [];
        $updated = 0;
        $skipped = 0;

        $this->info('Processing ' . count($rows) . ' rows…');
        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            [
                $emisCode, $region, $districtName, $tmisDistrictId, $districtCode,
                $councilName, $chiefdomName, $tmisChiefdomId, $chiefdomCode,
                $ward, $sectionName, $townName,
                $schType, $schoolName, $shiftStatus, $schoolStatus,
                $longitude, $latitude,
            ] = $row;

            $emisCode  = trim((string) $emisCode);
            $schoolName = trim((string) $schoolName);

            if (!$emisCode || !$schoolName) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $tmisDistrictId  = (int) $tmisDistrictId;
            $chiefdomCode    = (int) $chiefdomCode;
            $districtInfo    = $districtMap[$tmisDistrictId] ?? null;
            $chiefdomGeoId   = $chiefdomMap[$chiefdomCode] ?? null;
            $edLevel         = $this->edLevelMap[trim((string) $schType)] ?? null;
            $lat             = is_numeric($latitude)  ? (float) $latitude  : null;
            $lng             = is_numeric($longitude) ? (float) $longitude : null;

            $fields = [
                'name'                       => $schoolName,
                'school_education_level_oid' => $edLevel,
                'district_office_uuid'       => $districtInfo['do_uuid'] ?? null,
                'district_id'                => $districtInfo['geo_id']  ?? null,
                'chiefdom_id'                => $chiefdomGeoId,
                'council_name'               => trim((string) $councilName)  ?: null,
                'section_name'               => trim((string) $sectionName)  ?: null,
                'town_name'                  => trim((string) $townName)      ?: null,
                'lat'                        => $lat,
                'lng'                        => $lng,
                'active'                     => 1,
                'updated_at'                 => $now,
                'updated_by'                 => 0,
                'synced_at'                  => $now,
            ];

            if ($existing->has($emisCode)) {
                DB::table('school')
                    ->where('emis_id', $emisCode)
                    ->update($fields);
                $updated++;
            } else {
                $insert[] = array_merge($fields, [
                    'uuid'       => Uuid::uuid4()->toString(),
                    'emis_id'    => $emisCode,
                    'created_at' => $now,
                    'created_by' => 0,
                ]);
            }

            // flush inserts in batches of 500
            if (count($insert) >= 500) {
                DB::table('school')->insert($insert);
                $insert = [];
            }

            $bar->advance();
        }

        // flush remaining inserts
        if (count($insert)) {
            DB::table('school')->insert($insert);
        }

        $bar->finish();
        $this->newLine();

        $inserted = count($rows) - $updated - $skipped;
        $this->info("Done. Inserted: {$inserted}  |  Updated: {$updated}  |  Skipped: {$skipped}");

        return 0;
    }
}
