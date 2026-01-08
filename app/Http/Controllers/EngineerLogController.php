<?php

namespace App\Http\Controllers;

use App\Models\EngineerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class EngineerLogController extends Controller
{
    public function create()
    {
        return view('layout.convence');
    }

   public function store(Request $request)
    {
        // -----------------------------
        // Validate Basic Fields
        // -----------------------------
        $request->validate([
            'engineer_name' => 'required|string',
            'date' => 'required|array',
        ]);

        $entries = [];

        foreach ($request->date as $index => $singleDate) {
            $rows = [];
            $rowCount = count($request->from[$index]);

            for ($i = 0; $i < $rowCount; $i++) {
                // Hotel image upload (if exists)
                $hotelImagePath = null;

                if ($request->hasFile("hotel_image.$index.$i")) {
                    $hotelImagePath = $request->file("hotel_image.$index.$i")
                        ->store("hotel_images", "public");
                }

                $rows[] = [
                    "from"      => $request->from[$index][$i],
                    "to"        => $request->to[$index][$i],
                    "transport" => $request->transport[$index][$i],
                    "amount"    => (float) $request->amount[$index][$i],
                    "purpose"   => $request->purpose[$index][$i],
                    "food"      => isset($request->food[$index][$i]) ? (float)$request->food[$index][$i] : 0,
                    "hotel"     => isset($request->hotel[$index][$i]) ? (float)$request->hotel[$index][$i] : 0,
                    "remarks"   => $request->remarks[$index][$i],
                    "hotel_image" => $hotelImagePath,
                ];
            }

            $entries[] = [
                "date" => $singleDate,
                "rows" => $rows,
            ];
        }

        // Determine log_month from the first date
        $firstDate = $entries[0]['date'];
        $logMonth = Carbon::parse($firstDate)->format('Y-m');

        // Validate that all dates are in the same month
        foreach ($entries as $entry) {
            $entryMonth = Carbon::parse($entry['date'])->format('Y-m');
            if ($entryMonth !== $logMonth) {
                return back()->withErrors(['date' => 'All dates in a single submission must be in the same month.']);
            }
        }

        // Grand total calculate
        $grandTotal = 0;
        foreach ($entries as $block) {
            foreach ($block['rows'] as $r) {
                $grandTotal += ($r['amount'] + $r['food'] + $r['hotel']);
            }
        }

        // SAVE to DB
        EngineerLog::create([
            "engineer_name" => $request->engineer_name,
            "entries"       => json_encode($entries),
            "grand_total"   => $grandTotal,
            "submitted_at"  => now(),
            "status"        => "pending",
            "verify"        => "pending",
            "completed"        => "pending",
            "user_id"       => auth()->id(),
            "log_month"     => $logMonth
        ]);

        return back()->with("success", "Engineer log saved successfully!");
    }

    public function index()
    {
        $query = EngineerLog::query();
            //route accesse author
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            $query->where('user_id', auth()->id());
        }

        $logs = $query->orderBy('submitted_at', 'desc')->get();

        $grouped = [];

        foreach ($logs as $log) {
            $engineer = $log->engineer_name;
            $month = Carbon::createFromFormat('Y-m', $log->log_month)->format('F Y');

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

    public function monthView(Request $request, $engineer, $month)
    {
        $engineer = urldecode($engineer);
        $month = urldecode($month);

        // Convert month from "F Y" to "Y-m"
        $logMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');

        $query = EngineerLog::where('engineer_name', $engineer)
            ->where('log_month', $logMonth);
//update athur
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            $query->where('user_id', auth()->id());
        }

        $logs = $query->get();

        // Merge all entries
        $mergedEntries = collect();
        foreach ($logs as $log) {
            $entries = json_decode($log->entries, true);
            if (is_array($entries)) {
                foreach ($entries as $entryIndex => $entry) {
                    $date = $entry['date'] ?? null;
                    $rows = $entry['rows'] ?? [];
                    foreach ($rows as $rowIndex => $row) {
                        $mergedEntries->push([
                            'log_id' => $log->id,
                            'entry_index' => $entryIndex,
                            'row_index' => $rowIndex,
                            'date' => $date,
                            'from' => $row['from'] ?? '',
                            'to' => $row['to'] ?? '',
                            'transport' => $row['transport'] ?? '',
                            'amount' => (float)($row['amount'] ?? 0),
                            'food' => (float)($row['food'] ?? 0),
                            'hotel' => (float)($row['hotel'] ?? 0),
                            'purpose' => $row['purpose'] ?? '',
                            'remarks' => $row['remarks'] ?? '',
                            'total' => (float)($row['amount'] ?? 0) + (float)($row['food'] ?? 0) + (float)($row['hotel'] ?? 0),
                            'hotel_image' => $row['hotel_image'] ?? '',
                        ]);
                    }
                }
            }
        }

        $grandTotal = $mergedEntries->sum('total');

        return view('layout.month_view', compact('mergedEntries', 'grandTotal', 'engineer', 'month'));
    }

    public function bulkApprove(Request $request, $engineer, $month)
    {
        //blank author
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|string',
            'grand_total' => 'required|numeric',
        ]);

        $engineer = urldecode($engineer);
        $month = urldecode($month);

        // Convert month from "F Y" to "Y-m"
        $logMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');

        $logs = EngineerLog::where('engineer_name', $engineer)
            ->where('log_month', $logMonth)
            ->get();

        $count = $logs->count();
        if ($count > 0) {
            $newGrandTotal = $request->grand_total / $count;
            foreach ($logs as $log) {
                $log->status = $request->status;
                $log->grand_total = $newGrandTotal;
                $log->save();
            }
        }

        return back()->with('success', 'All logs for the month updated successfully.');
    }

    public function bulkVerify(Request $request, $engineer, $month)
    {
        //blank author
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            abort(403);
        }

        $engineer = urldecode($engineer);
        $month = urldecode($month);

        // Convert month from "F Y" to "Y-m"
        $logMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');

        $logs = EngineerLog::where('engineer_name', $engineer)
            ->where('log_month', $logMonth)
            ->get();

        foreach ($logs as $log) {
            $log->verify = 'approved';
            $log->save();
        }

        return back()->with('success', 'All logs for the month verified successfully.');
    }

    public function bulkComplete(Request $request, $engineer, $month)
    {
        //blank author
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            abort(403);
        }

        $engineer = urldecode($engineer);
        $month = urldecode($month);

        // Convert month from "F Y" to "Y-m"
        $logMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');

        $logs = EngineerLog::where('engineer_name', $engineer)
            ->where('log_month', $logMonth)
            ->get();

        foreach ($logs as $log) {
            $log->completed = 'approved';
            $log->save();
        }

        return back()->with('success', 'All logs for the month completed successfully.');
    }

    public function monthDelete(Request $request, $engineer, $month)
    {
        //munth loge accses author
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            abort(403);
        }

        $engineer = urldecode($engineer);
        $month = urldecode($month);

        // Convert month from "F Y" to "Y-m"
        $logMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');

        EngineerLog::where('engineer_name', $engineer)
            ->where('log_month', $logMonth)
            ->delete();

        return back()->with('success', 'All logs for the month deleted successfully.');
    }

    public function exportMonthExcel($engineer, $month)
    {
        // munth log exprot
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            abort(403);
        }

        $engineer = urldecode($engineer);
        $month = urldecode($month);

        // Convert month from "F Y" to "Y-m"
        $logMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');

        $logs = EngineerLog::where('engineer_name', $engineer)
            ->where('log_month', $logMonth)
            ->get();

        $data = [];
        foreach ($logs as $log) {
            $entries = json_decode($log->entries, true);
            if (is_array($entries)) {
                foreach ($entries as $entry) {
                    $date = $entry['date'] ?? null;
                    $rows = $entry['rows'] ?? [];
                    foreach ($rows as $row) {
                        $data[] = [
                            'Date' => $date,
                            'From' => $row['from'] ?? '',
                            'To' => $row['to'] ?? '',
                            'Transport' => $row['transport'] ?? '',
                            'Purpose' => $row['purpose'] ?? '',
                            'Amount' => (float)($row['amount'] ?? 0),
                            'Food' => (float)($row['food'] ?? 0),
                            'Hotel' => (float)($row['hotel'] ?? 0),
                            'Total' => (float)($row['amount'] ?? 0) + (float)($row['food'] ?? 0) + (float)($row['hotel'] ?? 0),
                            'Remarks' => $row['remarks'] ?? '',
                            'Status' => $log->status,
                            'Verify'=>$log->verify,
                            'Completed'=>$log->completed,
                        ];
                    }
                }
            }
        }

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            public function __construct($data) {
                $this->data = $data;
            }
            public function collection() {
                return collect($this->data);
            }
            public function headings(): array {
                return ['Date', 'From', 'To', 'Transport', 'Purpose', 'Amount', 'Food', 'Hotel', 'Total', 'Remarks', 'Status', 'Verify', 'Completed'];
            }
        }, 'engineer_logs_' . str_replace(' ', '_', $engineer) . '_' . str_replace(' ', '_', $month) . '.xlsx');
    }

    public function exportMonthPdf($engineer, $month)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            abort(403);
        }

        $engineer = urldecode($engineer);
        $month = urldecode($month);

        // Convert month from "F Y" to "Y-m"
        $logMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');

        $logs = EngineerLog::where('engineer_name', $engineer)
            ->where('log_month', $logMonth)
            ->get();

        $data = [];
        $grandTotal = 0;
        foreach ($logs as $log) {
            $entries = json_decode($log->entries, true);
            if (is_array($entries)) {
                foreach ($entries as $entry) {
                    $date = $entry['date'] ?? null;
                    $rows = $entry['rows'] ?? [];
                    foreach ($rows as $row) {
                        $total = (float)($row['amount'] ?? 0) + (float)($row['food'] ?? 0) + (float)($row['hotel'] ?? 0);
                        $grandTotal += $total;
                        $data[] = [
                            'date' => $date,
                            'from' => $row['from'] ?? '',
                            'to' => $row['to'] ?? '',
                            'transport' => $row['transport'] ?? '',
                            'purpose' => $row['purpose'] ?? '',
                            'amount' => (float)($row['amount'] ?? 0),
                            'food' => (float)($row['food'] ?? 0),
                            'hotel' => (float)($row['hotel'] ?? 0),
                            'total' => $total,
                            'remarks' => $row['remarks'] ?? '',
                        ];
                    }
                }
            }
        }

        $pdf = \PDF::loadView('layout.month_pdf', compact('data', 'grandTotal', 'engineer', 'month'));
        return $pdf->download('engineer_logs_' . str_replace(' ', '_', $engineer) . '_' . str_replace(' ', '_', $month) . '.pdf');
    }

    public function updateEntry(Request $request)
    {
        // munth loge update author
        if (!auth()->user()->isAdmin() && !auth()->user()->isChecker() && !auth()->user()->isVerify()) {
            abort(403);
        }

        $request->validate([
            'log_id' => 'required|integer',
            'entry_index' => 'required|integer',
            'row_index' => 'required|integer',
            'field' => 'required|string',
            'value' => 'required|string',
        ]);

        $log = EngineerLog::findOrFail($request->log_id);
        $entries = json_decode($log->entries, true);

        if (!isset($entries[$request->entry_index]['rows'][$request->row_index])) {
            return response()->json(['error' => 'Invalid entry or row index'], 400);
        }

        $entries[$request->entry_index]['rows'][$request->row_index][$request->field] = $request->value;

        // Recalculate total if amount, food, or hotel changed
        if (in_array($request->field, ['amount', 'food', 'hotel'])) {
            $row = $entries[$request->entry_index]['rows'][$request->row_index];
            $entries[$request->entry_index]['rows'][$request->row_index]['total'] = (float)($row['amount'] ?? 0) + (float)($row['food'] ?? 0) + (float)($row['hotel'] ?? 0);
        }

        $log->entries = json_encode($entries);

        // Recalculate grand total
        $grandTotal = 0;
        foreach ($entries as $entry) {
            foreach ($entry['rows'] as $row) {
                $grandTotal += (float)($row['amount'] ?? 0) + (float)($row['food'] ?? 0) + (float)($row['hotel'] ?? 0);
            }
        }
        $log->grand_total = $grandTotal;

        $log->save();

        return response()->json(['success' => true]);
    }
}
