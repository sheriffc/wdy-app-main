<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LearnerAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['downloadTemplate', 'bulkUpload']);
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->user_type_id < 40) {
                abort(403, 'You are not authorised to view this page.');
            }
            return $next($request);
        })->only(['index', 'listJson']);
    }

    public function index()
    {
        return view('admin.learners');
    }

    public function listJson(Request $request)
    {
        $draw   = intval($request->input('draw', 1));
        $start  = intval($request->input('start', 0));
        $length = intval($request->input('length', 25));
        $search = trim($request->input('search.value', ''));

        $base = DB::table('learner as l')
            ->join('person as p', 'p.uuid', '=', 'l.person_uuid')
            ->whereNull('l.deleted_at')
            ->select(
                'l.uuid',
                'l.learner_id',
                'p.first_name',
                'p.middle_name',
                'p.last_name',
                'p.sex_oid',
                DB::raw("DATE_FORMAT(p.date_of_birth, '%Y-%m-%d') as date_of_birth"),
                'p.nin',
                'l.created_at'
            );

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('p.first_name', 'like', "%{$search}%")
                  ->orWhere('p.last_name',  'like', "%{$search}%")
                  ->orWhere('p.nin',        'like', "%{$search}%")
                  ->orWhere('l.learner_id', 'like', "%{$search}%");
            });
        }

        $total    = DB::table('learner')->whereNull('deleted_at')->count();
        $filtered = (clone $base)->count();
        $rows     = $base->orderBy('p.last_name')->orderBy('p.first_name')
                         ->offset($start)->limit($length)->get();

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $rows,
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Learners');

        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];
        $headers = ['First Name', 'Middle Name', 'Last Name', 'Gender (male/female)', 'Date of Birth (YYYY-MM-DD)', 'NIN'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($columns[$i] . '1', $h);
            $sheet->getColumnDimension($columns[$i])->setAutoSize(true);
        }

        $style = $spreadsheet->getActiveSheet()->getStyle('A1:F1');
        $style->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'learner_upload_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function bulkUpload(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls']);

        $file        = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        $userId    = Auth::id();
        $inserted  = 0;
        $skipped   = 0;
        $errors    = [];

        foreach ($rows as $rowNum => $row) {
            if ($rowNum === 1) continue; // skip header

            $firstName  = trim($row['A'] ?? '');
            $middleName = trim($row['B'] ?? '');
            $lastName   = trim($row['C'] ?? '');
            $gender     = strtolower(trim($row['D'] ?? ''));
            $dob        = trim($row['E'] ?? '');
            $nin        = trim($row['F'] ?? '');

            if ($firstName === '' && $lastName === '') continue;

            if (!in_array($gender, ['male', 'female'])) {
                $errors[] = "Row {$rowNum}: Gender must be 'male' or 'female' (got: '{$gender}')";
                $skipped++;
                continue;
            }

            if ($dob !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
                $errors[] = "Row {$rowNum}: Date of Birth must be YYYY-MM-DD format (got: '{$dob}')";
                $skipped++;
                continue;
            }

            // Deduplicate by NIN if provided
            if ($nin !== '') {
                $exists = DB::table('person')
                    ->where('nin', $nin)
                    ->whereNull('deleted_at')
                    ->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }
            }

            DB::transaction(function () use ($firstName, $middleName, $lastName, $gender, $dob, $nin, $userId, &$inserted) {
                $personUuid  = (string) Str::uuid();
                $learnerUuid = (string) Str::uuid();
                $now         = now();

                DB::table('person')->insert([
                    'uuid'         => $personUuid,
                    'first_name'   => $firstName,
                    'middle_name'  => $middleName,
                    'last_name'    => $lastName,
                    'sex_oid'      => $gender,
                    'date_of_birth'=> $dob ?: null,
                    'nin'          => $nin ?: null,
                    'created_at'   => $now,
                    'created_by'   => $userId,
                    'updated_at'   => $now,
                    'updated_by'   => $userId,
                ]);

                DB::table('learner')->insert([
                    'uuid'        => $learnerUuid,
                    'person_uuid' => $personUuid,
                    'created_at'  => $now,
                    'created_by'  => $userId,
                    'updated_at'  => $now,
                    'updated_by'  => $userId,
                ]);

                $inserted++;
            });
        }

        $message = "Imported {$inserted} learner(s).";
        if ($skipped) $message .= " {$skipped} row(s) skipped (duplicates or invalid data).";

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
