<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ForgotPasswordController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservationsExport;
use App\Exports\OngoingExport;
use App\Exports\AllBookingsExport;
use App\Exports\CancelledBookingsExport;
use App\Exports\PenaltyExport;
use App\Exports\MaintenanceExport;
use App\Exports\UsersExport;
use App\Exports\MotorcyclesExport;


Route::get('/', [HomeController::class, 'home'])->name('home');

//Sign up
Route::get('sign-up', [SignUpController::class, 'showSignUpForm'])->name('sign-up');
Route::post('sign-up.registration', [SignUpController::class, 'storeRegistration'])->name('storeRegistration');
Route::get('motorcycles', [HomeController::class, 'viewMotorcycleCategories'])->name('motorcycles');
Route::get('login', [SignUpController::class, 'showLoginCustomer'])->name('login');
Route::post('login', [SignUpController::class, 'loginCustomer'])->name('customer.login');
// Forgot password form
Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot-password');

// Send reset link
Route::post('/link-reset', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

// Password reset form
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset');

// Handle password reset
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

//OTP
Route::get('email/otp', [SignUpController::class, 'showOtp'])->name('email.otp');
Route::post('/email/verify', [SignUpController::class, 'verifyOtp'])->name('email.verify');
Route::post('/email/resend', [SignUpController::class, 'resendOtp'])->name('email.resend');
Route::get('success-verification', [SignUpController::class, 'successVerification'])->name('success-verification');

Route::get('/admin/admin-login', [AdminLoginController::class, 'showLoginForm'])->name('admin.admin-login');
Route::post('admin/admin-login', [AdminLoginController::class, 'loginDashboard'])->middleware(\App\Http\Middleware\PreventBackHistory::class);

Route::get('/motorcycles/search', [HomeController::class, 'search'])->name('motorcycles.search');
   
Route::get('/reservations/invoice/{reservationId}', [BookingsController::class, 'generateInvoice'])->name('reservations.invoice');

//Motorcycle Customer
Route::get('/motorcycle/details/{id}', [HomeController::class, 'viewDetailsMotorcycle'])->name('motorcycle.details-motorcycle');

Route::middleware([PreventBackHistory::class, 'customer'])->group(function () {
    Route::post('logoutCustomer', [SignUpController::class, 'logoutCustomer'])->name('customer.logout');

    //Reservation Transaction
    Route::get('/motorcycle/reservation/details', [HomeController::class, 'viewReservationDetails'])->name('reservation.details');
    Route::post('/reservation/process', [HomeController::class, 'process'])->name('reservation.process');
    Route::get('/payment/{reservation_id}', [HomeController::class, 'showPayment'])->name('motorcycle.payment');
    Route::post('/payment/process', [HomeController::class, 'processPayment'])->name('payment.process');
    Route::get('/reservation/confirmation/{reservation_id}', [HomeController::class, 'confirmation'])->name('reservation.confirmation');
    Route::get('/reservation/success/{reservation_id}', [HomeController::class, 'showSuccessPage'])->name('motorcycle.success');
    Route::get('/history/view/{reservation_id}', [HomeController::class, 'viewHistory'])->name('view.history');

    Route::get('/check-penalty', [HomeController::class, 'checkPenalty'])->name('check.penalty');

    //Dashboard
    Route::get('/history', [CustomerDashboardController::class, 'viewDashboard'])->name('customer.customer-dashboard');


    Route::post('/reservations/cancel/{reservation_id}', [CustomerDashboardController::class, 'cancelReservation'])
     ->name('reservations.cancel');

    
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);

    Route::post('/submit-payment', [PenaltyController::class, 'submitPayment'])->name('submit.payment');

});

//ADMIN SIDE 
Route::middleware([PreventBackHistory::class, 'admin'])->group(function () {
    Route::get('admin-dashboard', [AdminDashboardController::class, 'showAdminDashboard'])->name('admin.admin-dashboard');
    Route::get('/motorcycles/manage-motorcycles', [MotorcycleController::class, 'showManageMotorcycles'])->name('admin.motorcycles.manage-motorcycles');
    Route::post('/motorcycles/update-status/{motor_id}', [MotorcycleController::class, 'updateStatus'])->name('admin.motorcycles.update-status');
    Route::post('motorcycles', [MotorcycleController::class, 'store'])->name('motorcycles.store');
    Route::get('/motorcycles/manage-motorcycles/add-motorcycles', [MotorcycleController::class, 'showaddMotorcycles'])->name('admin.motorcycles.add-motorcycle');
    Route::delete('/motorcycles/{id}', [MotorcycleController::class, 'destroy'])->name('motorcycles.destroy');
    Route::get('motorcycles/manage-motorcycles/edit-motorcycle/{motor_id}', [MotorcycleController::class, 'edit'])->name('admin.motorcycles.edit-motorcycle');
    Route::put('/motorcycles/{motor_id}', [MotorcycleController::class, 'update'])->name('admin.motorcycles.update');
    Route::get('motorcycles/manage-motorcycles/view-motorcycle/{motor_id}', [MotorcycleController::class, 'viewMotorcycle'])->name('admin.motorcycles.view-motorcycle');
    Route::post('/logout', [AdminLoginController::class, 'logoutAdmin'])->name('logout');

    Route::get('/motorcycles/maintenance', [MotorcycleController::class, 'showMotorcycleMaintenance'])->name('admin.motorcycles.maintenance-motorcycles');
    Route::post('/admin/motorcycles/update-maintenance/{motorcycleId}', [MotorcycleController::class, 'updateMotorcycleMaintenance'])->name('admin.motorcycles.update-maintenance');


    //bookings
    Route::get('/new-bookings', [BookingsController::class, 'showBookings'])->name('admin.reservation.bookings');
    Route::get('reservation/bookings/view/{id}', [BookingsController::class, 'viewBookings'])->name('admin.reservation.view-bookings');

    //all bookings record
    Route::get('/bookings/completed', [BookingsController::class, 'showAllBookings'])->name('admin.reservation.all-bookings-record');
    Route::get('/bookings/completed/view/{id}', [BookingsController::class, 'viewAllBookingsRecord'])->name('admin.reservation.view-all-bookings');

    Route::get('/bookings/ongoing', [BookingsController::class, 'showOngoingBookings'])->name('admin.reservation.ongoing-bookings');
    Route::get('/bookings/ongoing/view/{id}', [BookingsController::class, 'viewOngoingBookings'])->name('admin.reservation.view-ongoing-bookings');

    Route::get('/motorcycles/manage-motorcycles/view-motorcycle/view-booking/{id}', [BookingsController::class, 'viewBookingSpecific'])->name('admin.reservation.view-bookings-specific');

    //cancelled bookings
    Route::get('/bookings/cancelled', [BookingsController::class, 'showCancelledBookings'])->name('admin.reservation.cancelled-bookings');
    Route::get('/bookings/cancelled/view/{id}', [BookingsController::class, 'viewCancelledBookingsRecord'])->name('admin.reservation.view-cancelled-bookings');

    //change status
    Route::post('reservations/approve/{id}', [BookingsController::class, 'approve'])->name('reservations.approve');
    Route::post('reservations/decline/{id}', [BookingsController::class, 'decline'])->name('reservations.decline');
    Route::post('admin/bookings/cancel/{reservation_id}', [BookingsController::class, 'cancel'])->name('reservations.cancel');

    Route::post('reservations/mark-ongoing/{reservation}', [BookingsController::class, 'markOngoing'])->name('reservations.markOngoing');
    Route::post('reservations/completed/{reservation}', [BookingsController::class, 'completed'])->name('reservations.completed');

    Route::post('reservations/update-payment-status/{reservation_id}', [BookingsController::class, 'updatePaymentStatus'])->name('reservations.update-payment-status');

    Route::post('/penalties', [PenaltyController::class, 'storePenalty'])->name('penalties.store');
    Route::get('/bookings/penalties', [PenaltyController::class, 'showPenaltiesPage'])->name('admin.reservation.penalties');
    Route::put('/penalties/{penaltyId}/update-status', [PenaltyController::class, 'updateStatusPenalty'])->name('penalties.updateStatus');



    Route::get('/users', [UsersController::class, 'showUsersPage'])->name('admin.user-management.users');

    //archive
    Route::post('/admin/motorcycles/archive/{id}', [MotorcycleController::class, 'archive'])->name('admin.motorcycles.archive');
    Route::get('/archived/motorcycles', [MotorcycleController::class, 'showArchivedMotorcycles'])->name('admin.motorcycles.archived-motorcycles');
    Route::post('/admin/motorcycles/restore/{id}', [MotorcycleController::class, 'restore'])->name('admin.motorcycles.restore');
    Route::get('archived/motorcycles/view/{motor_id}', [MotorcycleController::class, 'viewArchiveMotorcycle'])->name('admin.motorcycles.view-archive-motorcycle');
    Route::get('/admin/motorcycles/status/{id}', [MotorcycleController::class, 'getStatus']);

    //Notification

    Route::get('/admin/notifications', [NotificationController::class, 'getAdminNotifications'])->name('admin.notifications');
    Route::post('/admin/notifications/markAllRead', [NotificationController::class, 'markAllNotificationsAsRead']);

  
    //exports
    Route::get('/export-reservations', function () {
        return Excel::download(new ReservationsExport, 'bookings.xlsx');})->name('export.reservations');

    Route::get('/export-ongoing-export', function () {
        return Excel::download(new OngoingExport, 'ongoing-bookings.xlsx');})->name('export.ongoing-bookings');

    Route::get('/export-all-bookings-record', function () {
        return Excel::download(new AllBookingsExport, 'completed-bookings.xlsx');})->name('export.all-bookings-record');

    Route::get('/export-ongoing-export', function () {
        return Excel::download(new OngoingExport, 'ongoing-bookings.xlsx');})->name('export.ongoing-bookings');
    
    Route::get('/export-cancelled-export', function () {
        return Excel::download(new CancelledBookingsExport, 'cancelled-bookings.xlsx');})->name('export.cancelled-bookings');

    Route::get('/export.penalty', function () {
        return Excel::download(new PenaltyExport, 'penalty.xlsx');})->name('export.penalty');

    Route::get('/export.users', function () {
        return Excel::download(new UsersExport, 'users.xlsx');})->name('export.users');
        
    Route::get('/export.maintenance', function () {
        return Excel::download(new MaintenanceExport, 'maintenance-motorcycle.xlsx');})->name('export.maintenance');
    
        Route::get('/export.motorcycle', function () {
            return Excel::download(new MotorcyclesExport, 'motorcycles.xlsx');})->name('export.motorcycle');
});
