<?php

namespace App\Http\Controllers;

use App\Models\PubaliData;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;



class PubaliController extends Controller
{
    // Show all data (paginated)
    public function index()
    {
        // change per-page number as needed
        $query = PubaliData::orderBy('id', 'desc');
        if (!auth()->user()->isAdmin() && !auth()->user()->isPblManager()) {
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereNull('user_id');
            });
        }
        $data = $query->paginate(100);
        return view('layout.pubali', compact('data'));
    }

    // Store new record
    public function store(Request $request)
    {
        $request->validate([
            'tid' => 'required',
            'mid' => 'required',
            'merchent' => 'required',
            'address' => 'required',
            'officer' => 'required',
            'number' => 'required',
            'pos_s' => 'required',
        ]);

        PubaliData::create($request->only(['tid','mid','merchent','address','officer','number','pos_s']) + ['user_id' => auth()->id()]);

        return redirect()->back()->with('success', 'New Data Added Successfully!');
    }

    // Edit view
    public function edit($id)
    {
        $data = PubaliData::findOrFail($id);
        return view('layout.pubali', compact('data'));
    }

    // Update record
    public function update(Request $request, $id)
{
    $request->validate([
        'tid' => 'required',
        'mid' => 'required',
        'merchent' => 'required',
        'address' => 'required',
        'officer' => 'required',
        'number' => 'required',
        'pos_s' => 'required',
        // 'status' => 'required|in:Pending,Done',
    ]);

    $data = PubaliData::findOrFail($id);

    if ((!auth()->user()->isAdmin() && !auth()->user()->isPblManager()) && $data->user_id && $data->user_id != auth()->id()) {
        abort(403, 'Unauthorized');
    }

    $data->update($request->only([
        'tid',
        'mid',
        'merchent',
        'address',
        'officer',
        'number',
        'pos_s',
        // 'status'
    ]));

    return redirect()->route('pubali.index')->with('success', 'Data Updated Successfully!');
}

    // Delete record
    public function destroy($id)
    {
        $data = PubaliData::findOrFail($id);
        if ((!auth()->user()->isAdmin() && !auth()->user()->isPblManager()) && $data->user_id && $data->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }
        $data->delete();
        return redirect()->back()->with('success', 'Data Deleted Successfully!');
    }

    // Assign engineer -> create report
    public function assign(Request $request, $id)
    {
        $request->validate([
            'engineer_name' => 'required',
            'engineer_contact' => 'required',
            'assignment_date' => 'required|date',
            'status' => 'required',
            'bank'=>'required',
        ]);

        $data = PubaliData::findOrFail($id);

        Report::create([
            'pubali_id' => $data->id,
            'tid' => $data->tid,
            'mid' => $data->mid,
            'merchent' => $data->merchent,
            'address' => $data->address,
            'officer' => $data->officer,
            'number' => $data->number,
            'pos_s' => $data->pos_s,
            'engineer_name' => $request->engineer_name,
            'engineer_contact' => $request->engineer_contact,
            'assignment_date' => $request->assignment_date,
            'bank' => $request->bank,
            'status' => $request->status,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Engineer Assigned Successfully!');
    }

    // Import CSV/XLSX and replace existing data
    public function import(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $imported = 0;

        try {

            // Reset table
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            PubaliData::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // CSV Handling
            if (in_array($extension, ['csv', 'txt'])) {

                $handle = fopen($file->getRealPath(), 'r');

                // Skip header
                $first = fgetcsv($handle, 3000, ",");

                while (($row = fgetcsv($handle, 3000, ",")) !== false) {

                    if (empty($row[0]) && empty($row[1]) && empty($row[6])) continue;

                    PubaliData::create([
                        'tid' => trim($row[0] ?? ''),
                        'mid' => trim($row[1] ?? ''),
                        'merchent' => trim($row[2] ?? ''),
                        'address' => trim($row[3] ?? ''),
                        'officer' => trim($row[4] ?? ''),
                        'number' => trim($row[5] ?? ''),
                        'pos_s' => trim($row[6] ?? ''),
                    ]);

                    $imported++;
                }

                fclose($handle);
            }

            // Excel Handling
            else if (in_array($extension, ['xlsx', 'xls'])) {

                $sheets = Excel::toArray([], $file);
                $rows = $sheets[0] ?? [];

                array_shift($rows); // remove header

                foreach ($rows as $row) {

                    if (empty($row[0]) && empty($row[1]) && empty($row[6])) continue;

                    PubaliData::create([
                        'tid' => trim($row[0] ?? ''),
                        'mid' => trim($row[1] ?? ''),
                        'merchent' => trim($row[2] ?? ''),
                        'address' => trim($row[3] ?? ''),
                        'officer' => trim($row[4] ?? ''),
                        'number' => trim($row[5] ?? ''),
                        'pos_s' => trim($row[6] ?? ''),
                    ]);

                    $imported++;
                }
            }

            return redirect()->route('pubali.index')
                ->with('success', "$imported records imported successfully!");

        } catch (\Throwable $e) {

            Log::error('Pubali import error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Import failed! Check file format.');
        }
    }
}

