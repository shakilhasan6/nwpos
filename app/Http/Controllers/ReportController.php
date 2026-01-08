<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    // Show all reports
    public function index()
    {
        $data = Report::orderBy('id', 'desc')->get();
        return view('layout.report', compact('data'));
    }

    // Store new report
    public function store(Request $request)
    {
        $request->validate([
            'pubali_id' => 'nullable|exists:pubali_data,id',
            'tid' => 'required',
            'mid' => 'required',
            'engineer_name' => 'required',
            'status' => 'required|in:Pending,Done',
            
          
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('reports', 'public');
        }

        Report::create($data);

        return redirect()->back()->with('success', 'Report created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $report = Report::findOrFail($id);
        return view('layout.report', compact('report'));
    }

  
    // Update report
 public function update(Request $request, $id)
 {
     $request->validate([
         'status' => 'required|in:pending,assigned,completed',
         'remarks' => 'nullable|string',
          'bank' => 'nullable|string',
         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
     ]);

     $report = Report::findOrFail($id);

     if (auth()->user()->isEngineer() && $report->user_id != auth()->id()) {
         abort(403, 'Unauthorized');
     }

     $report->status = $request->status;
     $report->remarks = $request->remarks ?? $report->remarks;

     $report->bank = $request->bank ?? $report->bank;

     if ($request->hasFile('image')) {
         if (!empty($report->image_path) && Storage::disk('public')->exists($report->image_path)) {
             Storage::disk('public')->delete($report->image_path);
         }
         $report->image_path = $request->file('image')->store('reports', 'public');
     }

     $report->save();

     return redirect()->route('report.index')->with('success', 'Report updated successfully!');
 }



    // Delete report
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if (auth()->user()->isEngineer() && $report->user_id != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Delete image if exists
        if (!empty($report->image_path) && Storage::disk('public')->exists($report->image_path)) {
            Storage::disk('public')->delete($report->image_path);
        }

        $report->delete();

        return redirect()->back()->with('success', 'Report deleted successfully!');
    }
}
