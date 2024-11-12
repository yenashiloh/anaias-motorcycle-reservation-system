<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\DriverInformation;
use App\Models\Admin;
use App\Models\Motorcycle;
use App\Models\Penalty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Events\PenaltyAdded;
use App\Models\Notification;
use Illuminate\Validation\ValidationException;


class PenaltyController extends Controller
{
    //store penalty
    public function storePenalty(Request $request)
    {
        try {
            $validated = $request->validate([
                'reservation_id' => 'required|integer|exists:reservations,reservation_id',
                'customer_id' => 'required|integer',
                'driver_id' => 'required|integer',
                'penalty_type' => 'required|string|max:255',
                'description' => 'required|string',
                'additional_payment' => 'required|numeric',
            ]);
    
            $penalty = Penalty::create($validated);
    
            Reservation::where('reservation_id', $validated['reservation_id'])
                ->update(['violation_status' => 'Violator']);
    
            $notification = Notification::create([
                'customer_id' => $validated['customer_id'],
                'reservation_id' => $validated['reservation_id'],
                'type' => 'penalty',
                'message' => "New penalty added: {$validated['penalty_type']} - ₱{$validated['additional_payment']}",
                'read' => false
            ]);
    
            return redirect()->back()->with('success', 'Penalty Added Successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Failed to add penalty: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add penalty. Please try again.');
        }
    }
    
    //show penalty page
    public function showPenaltiesPage()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.admin-login');
        }
    
        $admin = Auth::guard('admin')->user();

        $penalties = Penalty::with('driver')->get();
    
        return view('admin.reservation.penalties', compact('admin', 'penalties'));
    }
    


}
