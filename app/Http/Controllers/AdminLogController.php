<?php

namespace App\Http\Controllers;

use App\Models\EngineerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EngineerLogExport;
use App\Exports\ConveyanceBillExport;

class AdminLogController extends Controller
{

    //log sow page
     // Show all data (grouped)
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $logs = EngineerLog::orderBy('submitted_at', 'desc')->get();

        $grouped = [];

        foreach ($logs as $log) {
            $engineer = $log->engineer_name;
            $month = \Carbon\Carbon::createFromFormat('Y-m', $log->log_month)->format('F Y');

            if (!isset($grouped[$engineer][$month])) {
                $grouped[$engineer][$month] = [
                    'logs' => [],
                    'grand_total' => 0,
                    'status' => 'approved', // default
                    'verify'  => 'approved',
                    'completed' => 'approved',
                    'month' => $month,
                    'engineer' => $engineer
                ];
            }

            $grouped[$engineer][$month]['logs'][] = $log;
            $grouped[$engineer][$month]['grand_total'] += $log->grand_total;

            // If any log is pending, status is pending
            if ($log->status === 'pending') {
                $grouped[$engineer][$month]['status'] = 'pending';
            }
            // If any log is pending, verify is pending
            if ($log->verify === 'pending') {
                $grouped[$engineer][$month]['verify'] = 'pending';
            }
            // If any log is pending, completed is pending
            if ($log->completed === 'pending') {
                $grouped[$engineer][$month]['completed'] = 'pending';
            }
        }

        return view('layout.convencecheck', compact('grouped'));
    }

          
    /**
     * ---------------------------------------------------
     *  LIST PAGE (Search + Sort + Pagination)
     * ---------------------------------------------------
     */
    public function viwe (Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $log = EngineerLog::findOrFail($id);
        $engineerName = $log->engineer_name;

        $q = $request->query('q');
        $sort = $request->query('sort', 'submitted_at');
        $dir  = $request->query('dir', 'desc');
        $perPage = 25;

        // Get all logs for the engineer
        $engineerLogs = EngineerLog::where('engineer_name', $engineerName)->get();

        // Flatten the entries into individual log rows
        $flattenedLogs = collect();
        foreach ($engineerLogs as $engineerLog) {
            $entries = json_decode($engineerLog->entries, true);
            if (!is_array($entries)) {
                $entries = [];
            }
            foreach ($entries as $entryIndex => $entry) {
                $date = is_array($entry) ? ($entry['date'] ?? null) : null;
                $rows = is_array($entry) && isset($entry['rows']) && is_array($entry['rows']) ? $entry['rows'] : [];
                foreach ($rows as $rowIndex => $row) {
                    $flattenedLogs->push([
                        'row_id' => $engineerLog->id . '-' . $entryIndex . '-' . $rowIndex,
                        'id' => $engineerLog->id,
                        'submitted_at' => $engineerLog->submitted_at,
                        'engineer_name' => $engineerLog->engineer_name,
                        'date' => $date,
                        'from_location' => $row['from'] ?? null,
                        'to_location' => $row['to'] ?? null,
                        'transport' => $row['transport'] ?? null,
                        'amount' => $row['amount'] ?? 0,
                        'food' => $row['food'] ?? 0,
                        'hotel' => $row['hotel'] ?? 0,
                        'purpose' => $row['purpose'] ?? null,
                        'remarks' => $row['remarks'] ?? null,
                        'status' => $engineerLog->status,
                    ]);
                }
            }
        }

        // Apply search
        if ($q) {
            $flattenedLogs = $flattenedLogs->filter(function($log) use ($q) {
                return stripos($log['engineer_name'], $q) !== false ||
                       stripos($log['from_location'] ?? '', $q) !== false ||
                       stripos($log['to_location'] ?? '', $q) !== false ||
                       stripos($log['transport'] ?? '', $q) !== false ||
                       stripos($log['purpose'] ?? '', $q) !== false;
            });
        }

        // Apply sorting
        $allowedSort = [
            'submitted_at','engineer_name','from_location',
            'to_location','amount','food','hotel','status'
        ];
        if (!in_array($sort, $allowedSort)) $sort = 'submitted_at';

        $flattenedLogs = $flattenedLogs->sortBy($sort, SORT_REGULAR, $dir === 'desc');

        // Paginate the flattened logs
        $currentPage = $request->query('page', 1);
        $logsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $flattenedLogs->forPage($currentPage, $perPage),
            $flattenedLogs->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );
        $logsPaginated->appends($request->query());

        // Convert to array for Blade
        $logsArray = $logsPaginated->items();

        // Grand total calculation
        $grandTotal = collect($logsArray)->sum(function($log){
            return ($log['amount'] ?? 0) + ($log['food'] ?? 0) + ($log['hotel'] ?? 0);
        });

        return view('layout.convenceviwe', [
            'logs' => $logsArray,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
            'grandTotal' => $grandTotal,
            'pagination' => $logsPaginated,
            'engineerName' => $engineerName
        ]);
    }



        /**
     * ---------------------------------------------------
     *  CHANGE STATUS (Pending / Approved)
     * ---------------------------------------------------
     */
        public function changeStatus(Request $request, $id)
        {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $log = EngineerLog::findOrFail($id);
        $log->status = $request->status;
        $log->save();

        return back()->with('success', 'Status updated successfully');
        }



    /**
     * ---------------------------------------------------
     *  DELETE LOG
     * ---------------------------------------------------
     */
        public function delete($id)
        {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        EngineerLog::findOrFail($id)->delete();
        return back()->with('success', 'Log deleted successfully');
        }



    /**
     * ---------------------------------------------------
     *  INLINE CELL UPDATE (Excel-like edit)
     * ---------------------------------------------------
     */
    public function updateCell(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $validator = Validator::make($request->all(), [
            'id'    => 'required|string',
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'error',
                'errors'=>$validator->errors()
            ], 422);
        }

        $rowId = $request->id;
        $field = $request->field;
        $value = $request->value;

        // Parse row_id: log_id-entry_index-row_index
        $parts = explode('-', $rowId);
        if (count($parts) !== 3) {
            return response()->json([
                'status'=>'error',
                'message'=>'Invalid ID'
            ], 400);
        }
        $logId = $parts[0];
        $entryIndex = (int)$parts[1];
        $rowIndex = (int)$parts[2];

        // Allowed editable fields
        $allowed = [
            'date','from_location','to_location','transport','amount','purpose','food','hotel','remarks','status'
        ];

        if (!in_array($field, $allowed)) {
            return response()->json([
                'status'=>'error',
                'message'=>'Field not allowed'
            ], 403);
        }

        $log = EngineerLog::findOrFail($logId);
        $entries = json_decode($log->entries, true);
        if (!is_array($entries) || !isset($entries[$entryIndex])) {
            return response()->json([
                'status'=>'error',
                'message'=>'Entry not found'
            ], 404);
        }

        // Field mapping to JSON keys
        $fieldMap = [
            'date' => 'date',
            'from_location' => 'from',
            'to_location' => 'to',
            'transport' => 'transport',
            'amount' => 'amount',
            'purpose' => 'purpose',
            'food' => 'food',
            'hotel' => 'hotel',
            'remarks' => 'remarks',
            'status' => 'status'
        ];

        // Numeric casting
        if (in_array($field, ['amount','food','hotel'])) {
            $value = ($value === null || $value === '') ? 0 : floatval(str_replace(',', '', $value));
        }

        if ($field === 'status') {
            $log->status = $value;
            $log->save();
        } elseif ($field === 'date') {
            $entries[$entryIndex]['date'] = $value;
            $log->entries = json_encode($entries);
            $log->save();
        } else {
            $jsonField = $fieldMap[$field];
            if (!isset($entries[$entryIndex]['rows'][$rowIndex])) {
                return response()->json([
                    'status'=>'error',
                    'message'=>'Row not found'
                ], 404);
            }
            $entries[$entryIndex]['rows'][$rowIndex][$jsonField] = $value;
            $log->entries = json_encode($entries);
            $log->save();
        }

        // Recalculate total for this row if numeric field
        $totals = null;
        if (in_array($field, ['amount','food','hotel'])) {
            $row = $entries[$entryIndex]['rows'][$rowIndex];
            $totals = [
                'row_total' => floatval($row['amount'] ?? 0) + floatval($row['food'] ?? 0) + floatval($row['hotel'] ?? 0)
            ];
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'Saved',
            'totals'  => $totals,
            'log'     => $log
        ]);
    }



    /**
      * ---------------------------------------------------
      *  DOWNLOAD EXCEL (Single Engineer Log)
      * ---------------------------------------------------
      */
    public function downloadExcel($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $log = EngineerLog::findOrFail($id);
        $engineerName = $log->engineer_name;
        $month = \Carbon\Carbon::createFromFormat('Y-m', $log->log_month)->format('M-y');

        $flattenedLogs = collect();
        $entries = json_decode($log->entries, true);
        if (!is_array($entries)) $entries = [];
        foreach ($entries as $entry) {
            $date = is_array($entry) ? ($entry['date'] ?? null) : null;
            $rows = is_array($entry) && isset($entry['rows']) && is_array($entry['rows']) ? $entry['rows'] : [];
            foreach ($rows as $row) {
                $flattenedLogs->push([
                    'Date' => $date,
                    'From' => $row['from'] ?? null,
                    'To' => $row['to'] ?? null,
                    'Transport' => $row['transport'] ?? null,
                    'Purpose' => $row['purpose'] ?? null,
                    'Amount' => $row['amount'] ?? 0,
                    'Food' => $row['food'] ?? 0,
                    'Hotel' => $row['hotel'] ?? 0,
                    'Total' => floatval($row['amount'] ?? 0) + floatval($row['food'] ?? 0) + floatval($row['hotel'] ?? 0),
                    'Remarks' => $row['remarks'] ?? null,
                    'Status' => $log->status,
                ]);
            }
        }

        return Excel::download(new ConveyanceBillExport($flattenedLogs, $engineerName, $month), 'conveyance_bill.xlsx');
    }

    /**
     * ---------------------------------------------------
     *  EXPORT VIEW DATA TO EXCEL
     * ---------------------------------------------------
     */
    public function exportView($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $log = EngineerLog::findOrFail($id);
        $engineerName = $log->engineer_name;

        $engineerLogs = EngineerLog::where('engineer_name', $engineerName)->get();

        $flattenedLogs = collect();
        foreach ($engineerLogs as $engineerLog) {
            $entries = json_decode($engineerLog->entries, true);
            if (!is_array($entries)) {
                $entries = [];
            }
            foreach ($entries as $entry) {
                $date = is_array($entry) ? ($entry['date'] ?? null) : null;
                $rows = is_array($entry) && isset($entry['rows']) && is_array($entry['rows']) ? $entry['rows'] : [];
                foreach ($rows as $row) {
                    $flattenedLogs->push([
                       
                        'Date' => $date,
                        'From' => $row['from'] ?? null,
                        'To' => $row['to'] ?? null,
                        'Transport' => $row['transport'] ?? null,
                        'Purpose' => $row['purpose'] ?? null,
                        'Amount' => $row['amount'] ?? 0, 
                        'Food' => $row['food'] ?? 0,
                        'Hotel' => $row['hotel'] ?? 0,
                        'Total' => floatval($row['amount'] ?? 0) + floatval($row['food'] ?? 0) + floatval($row['hotel'] ?? 0),
                        'Remarks' => $row['remarks'] ?? null,
                        'Status' => $engineerLog->status,
                    ]);
                }
            }
        }

        $month = \Carbon\Carbon::createFromFormat('Y-m', $log->log_month)->format('M-y');

        return Excel::download(new ConveyanceBillExport($flattenedLogs, $engineerName, $month), 'conveyance_bill_' . str_replace(' ', '_', $engineerName) . '.xlsx');
    }
}
