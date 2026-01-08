<?php

namespace App\Http\Controllers;

use App\Models\ServiceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceReportController extends Controller
{
    public function index()
    {
        $serviceReports = ServiceReport::latest()->get();
        return view('service-reports.index', compact('serviceReports'));
    }

    public function create()
    {
        return view('service-reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_name' => 'required|string',
            'bank_name' => 'required|string',
            'tid' => 'required|string',
            'pos_serial' => 'required|string',
            'merchant_address' => 'required|string',
            'service_type' => 'required|in:Merchant Deploy,Branch Deploy,Support,Replace,Roll Out,Roll Out Not Done',
            'service_report_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('service_report_image')->store('service-reports', 'public');

        ServiceReport::create([
            'zone_name' => $request->zone_name,
            'bank_name' => $request->bank_name,
            'engineer_name' => Auth::user()->name, // Auth user name used
            'tid' => $request->tid,
            'pos_serial' => $request->pos_serial,
            'merchant_address' => $request->merchant_address,
            'service_type' => $request->service_type,
            'remarks' => $request->remarks,
            'service_report_image_path' => $path,
        ]);

        return redirect()->route('service-reports.index')->with('success', 'Report saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $report = ServiceReport::findOrFail($id);

        $request->validate([
            'zone_name' => 'required|string',
            'bank_name' => 'required|string',
            'engineer_name' => 'required|string',
            'tid' => 'required|string',
            'pos_serial' => 'required|string',
            'merchant_address' => 'required|string',
            'service_type' => 'required|string',
        ]);

        $data = $request->only(['zone_name', 'bank_name', 'engineer_name', 'tid', 'pos_serial', 'merchant_address', 'service_type', 'remarks']);

        if ($request->hasFile('service_report_image')) {
            if ($report->service_report_image_path) {
                Storage::disk('public')->delete($report->service_report_image_path);
            }
            $data['service_report_image_path'] = $request->file('service_report_image')->store('service-reports', 'public');
        }

        $report->update($data);

        return redirect()->route('service-reports.index')->with('success', 'Report updated successfully.');
    }

    public function destroy($id)
    {
        $report = ServiceReport::findOrFail($id);
        if ($report->service_report_image_path) {
            Storage::disk('public')->delete($report->service_report_image_path);
        }
        $report->delete();
        return redirect()->back()->with('success', 'Report deleted.');
    }
}