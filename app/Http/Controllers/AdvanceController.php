<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advance;
use App\Models\User;
use Carbon\Carbon;

class AdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('advances.list_by_name');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('advance.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'purpose' => 'required|string',
            'payment_method' => 'required|in:Cash,Bkash,Nagad,Bank',
            'payment_number' => 'nullable|string',
            'amount' => 'required|numeric',
            'status' => 'required|in:pending,approved,paid',
        ]);

        Advance::create($request->all());

        return redirect()->route('advances.list_by_name')->with('success', 'Advance created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        //
    }

    public function indexByName()
    {
        $advances = Advance::with('user')->orderBy('date')->get()->groupBy(function($item) {
            return $item->user ? $item->user->name : 'Unknown User';
        })->map(function($group) {
            return $group->groupBy(function($item) {
                return Carbon::parse($item->date)->format('F Y');
            });
        });

        return view('advance.list_by_name', compact('advances'));
    }

    public function indexByDate()
    {
        $advances = Advance::with('user')->orderBy('date')->get()->groupBy(function($item) {
            return Carbon::parse($item->date)->format('F Y');
        })->map(function($group) {
            return $group->groupBy('date');
        });

        return view('advance.list_by_date', compact('advances'));
    }

    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isChecker() && !$user->isVerify()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,approved,paid',
        ]);

        $advance = Advance::findOrFail($id);
        $advance->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}

