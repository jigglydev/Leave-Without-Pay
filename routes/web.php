<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

// Welcome
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return view('welcome');
});

// ── Auth routes (guests only) ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// ── Admin / Protected routes ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard — passes live employee count
    Route::get('/dashboard', function () {
        // Only count/show confirmed employees (is_confirmed = true)
        $employeeCount = User::where('role', 'employee')->where('is_confirmed', true)->count();

        // All entries this month (used by admin card)
        $recordedEntriesThisMonth = \App\Models\LeaveRecord::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // The logged-in user's own entries this month (entries THEY submitted, regardless of subject)
        $myEntriesThisMonth = \App\Models\LeaveRecord::where('created_by', auth()->id())
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $recentEmployees = User::where('role', 'employee')
            ->where('is_confirmed', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'employeeCount',
            'recordedEntriesThisMonth',
            'myEntriesThisMonth',
            'recentEmployees'
        ));
    })->name('admin.dashboard');

    // All Employees — only confirmed employees
    Route::get('/employees', function () {
        $employees = User::where('role', 'employee')->where('is_confirmed', true)->orderBy('created_at', 'desc')->get();
        return view('admin.employees', compact('employees'));
    })->name('admin.employees');

    Route::delete('/employees/{user}', function (\App\Models\User $user) {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.employees')->withErrors(['delete' => 'You cannot delete your own account.']);
        }
        $name = $user->full_name ?: $user->name;
        $user->delete();
        \App\Models\ActivityLog::log(
            'user_deleted',
            'Leave processor account deleted: ' . $name,
            ['entity' => 'user']
        );
        return redirect()->route('admin.employees')->with('status', 'Account deleted successfully.');
    })->name('admin.employees.destroy');

    // Leave Records
    Route::get('/leaves', function () {
        // Confirmed registered employees (users)
        $userList = \App\Models\User::where('role', 'employee')
            ->where('is_confirmed', true)
            ->orderBy('last_name')->orderBy('given_name')->orderBy('name')
            ->get(['id','last_name','given_name','middle_name','suffix','name','position','office'])
            ->map(fn($u) => [
                'id'       => 'user_' . $u->id,
                'name'     => $u->full_name,
                'position' => $u->position ?? '',
                'office'   => $u->office ?? '',
            ]);

        // Standalone employees (no user account)
        $empList = \App\Models\Employee::orderBy('last_name')->orderBy('given_name')
            ->get()
            ->map(fn($e) => [
                'id'       => 'emp_' . $e->id,
                'name'     => $e->full_name,
                'position' => $e->position ?? '',
                'office'   => $e->office ?? '',
            ]);

        $allUsers = $userList->concat($empList)->sortBy('name')->values();

        // Admins see all records; employees only see records they created
        $recordsQuery = \App\Models\LeaveRecord::with('user', 'employee')->orderByDesc('created_at');
        if (auth()->user()->isEmployee()) {
            $recordsQuery->where('created_by', auth()->id());
        }
        $records = $recordsQuery->get();

        return view('admin.leaves', compact('allUsers', 'records'));
    })->name('admin.leaves');

    // Store Leave Record
    Route::post('/leaves', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'person_id'          => ['required', 'string'],
            'as_of_date'         => ['required', 'date'],
            'el_vl'              => ['nullable', 'numeric', 'min:0'],
            'el_sl'              => ['nullable', 'numeric', 'min:0'],
            'no_pay_vl'          => ['nullable', 'numeric', 'min:0'],
            'no_pay_sl'          => ['nullable', 'numeric', 'min:0'],
            'no_pay_dates'       => ['nullable', 'array'],
            'no_pay_dates.*'     => ['date'],
            'undertime_hours'    => ['nullable', 'integer', 'min:0'],
            'undertime_minutes'  => ['nullable', 'integer', 'min:0', 'max:59'],
            'undertime_dates'    => ['nullable', 'array'],
            'undertime_dates.*'  => ['date'],
        ]);

        $vl = floatval($request->input('no_pay_vl') ?? 0);
        $sl = floatval($request->input('no_pay_sl') ?? 0);

        // Decode the person_id prefix to determine if it's a user or standalone employee
        $personId  = $request->input('person_id');
        $userId    = null;
        $employeeId = null;
        $snapshotName = null;
        $snapshotPosition = null;
        $snapshotOffice   = null;

        if (str_starts_with($personId, 'user_')) {
            $uid  = (int) substr($personId, 5);
            $user = \App\Models\User::find($uid);
            if (!$user) return back()->withErrors(['person_id' => 'Selected user not found.']);
            $userId = $user->id;
            $snapshotName     = $user->full_name;
            $snapshotPosition = $user->position;
            $snapshotOffice   = $user->office;
        } elseif (str_starts_with($personId, 'emp_')) {
            $eid      = (int) substr($personId, 4);
            $employee = \App\Models\Employee::find($eid);
            if (!$employee) return back()->withErrors(['person_id' => 'Selected employee not found.']);
            $employeeId = $employee->id;
            $snapshotName     = $employee->full_name;
            $snapshotPosition = $employee->position;
            $snapshotOffice   = $employee->office;
        } else {
            return back()->withErrors(['person_id' => 'Invalid person selection.']);
        }

        \App\Models\LeaveRecord::create([
            'user_id'           => $userId,
            'employee_id'       => $employeeId,
            'snapshot_name'     => $snapshotName,
            'snapshot_position' => $snapshotPosition,
            'snapshot_office'   => $snapshotOffice,
            'created_by'        => auth()->id(),
            'as_of_date'        => $request->input('as_of_date'),
            'el_vl'             => $request->input('el_vl') ?? null,
            'el_sl'             => $request->input('el_sl') ?? null,
            'no_pay_vl'         => $vl ?: null,
            'no_pay_sl'         => $sl ?: null,
            'no_pay_total'      => ($vl + $sl) ?: null,
            'no_pay_dates'      => $request->input('no_pay_dates') ?? null,
            'undertime_hours'   => $request->input('undertime_hours') ?? null,
            'undertime_minutes' => $request->input('undertime_minutes') ?? null,
            'undertime_dates'   => $request->input('undertime_dates') ?? null,
        ]);

        return redirect()->route('admin.leaves')->with('status', 'Leave record saved successfully!');
    })->name('admin.leaves.store');

    // Delete Leave Record
    Route::delete('/leaves/{id}', function ($id) {
        \App\Models\LeaveRecord::findOrFail($id)->delete();
        return redirect()->route('admin.leaves')->with('status', 'Leave record deleted.');
    })->name('admin.leaves.destroy');

    // ── Recorded Entries ────────────────────────────────────────────────────
    Route::get('/recorded-entries', function () {
        // Admins see all records; employees only see records they created
        $recordsQuery = \App\Models\LeaveRecord::with('user')->orderByDesc('created_at');
        if (auth()->user()->isEmployee()) {
            $recordsQuery->where('created_by', auth()->id());
        }
        $records = $recordsQuery->get();

        // Distinct users who created at least one leave record (for "Created By" filter dropdown)
        $creatorIds = $records->pluck('created_by')->filter()->unique();
        $creators = \App\Models\User::whereIn('id', $creatorIds)
            ->orderBy('last_name')->orderBy('given_name')
            ->get(['id', 'last_name', 'given_name', 'middle_name', 'suffix', 'name'])
            ->map(fn($u) => ['id' => $u->id, 'name' => $u->full_name])
            ->values();

        return view('admin.recorded_entries', compact('records', 'creators'));
    })->name('admin.recorded-entries');

    Route::get('/recorded-entries/{id}/edit', function ($id) {
        $record   = \App\Models\LeaveRecord::with('user', 'employee')->findOrFail($id);

        $userList = \App\Models\User::where('role', 'employee')
            ->where('is_confirmed', true)
            ->orderBy('last_name')->orderBy('given_name')->orderBy('name')
            ->get(['id','last_name','given_name','middle_name','suffix','name','position','office'])
            ->map(fn($u) => [
                'id'       => 'user_' . $u->id,
                'name'     => $u->full_name,
                'position' => $u->position ?? '',
                'office'   => $u->office ?? '',
            ]);

        $empList = \App\Models\Employee::orderBy('last_name')->orderBy('given_name')
            ->get()
            ->map(fn($e) => [
                'id'       => 'emp_' . $e->id,
                'name'     => $e->full_name,
                'position' => $e->position ?? '',
                'office'   => $e->office ?? '',
            ]);

        $allUsers = $userList->concat($empList)->sortBy('name')->values();

        // Determine the current person_id value for the edit form
        $currentPersonId = $record->employee_id
            ? 'emp_' . $record->employee_id
            : 'user_' . $record->user_id;

        return view('admin.recorded_entries_edit', compact('record', 'allUsers', 'currentPersonId'));
    })->name('admin.recorded-entries.edit');

    Route::patch('/recorded-entries/{id}', function (\Illuminate\Http\Request $request, $id) {
        $record = \App\Models\LeaveRecord::findOrFail($id);
        $request->validate([
            'person_id'          => ['required', 'string'],
            'as_of_date'         => ['required', 'date'],
            'el_vl'              => ['nullable', 'numeric', 'min:0'],
            'el_sl'              => ['nullable', 'numeric', 'min:0'],
            'no_pay_vl'          => ['nullable', 'numeric', 'min:0'],
            'no_pay_sl'          => ['nullable', 'numeric', 'min:0'],
            'no_pay_dates'       => ['nullable', 'array'],
            'no_pay_dates.*'     => ['date'],
            'undertime_hours'    => ['nullable', 'integer', 'min:0'],
            'undertime_minutes'  => ['nullable', 'integer', 'min:0', 'max:59'],
            'undertime_dates'    => ['nullable', 'array'],
            'undertime_dates.*'  => ['date'],
        ]);

        $vl = floatval($request->input('no_pay_vl') ?? 0);
        $sl = floatval($request->input('no_pay_sl') ?? 0);

        $personId   = $request->input('person_id');
        $userId     = null;
        $employeeId = null;
        $snapshotName = $record->snapshot_name;
        $snapshotPosition = $record->snapshot_position;
        $snapshotOffice   = $record->snapshot_office;

        if (str_starts_with($personId, 'user_')) {
            $uid  = (int) substr($personId, 5);
            $user = \App\Models\User::find($uid);
            if (!$user) return back()->withErrors(['person_id' => 'Selected user not found.']);
            $userId = $user->id;
            // Update snapshot only if person changed
            $oldPersonId = $record->employee_id ? 'emp_' . $record->employee_id : 'user_' . $record->user_id;
            if ($personId !== $oldPersonId) {
                $snapshotName     = $user->full_name;
                $snapshotPosition = $user->position;
                $snapshotOffice   = $user->office;
            }
        } elseif (str_starts_with($personId, 'emp_')) {
            $eid      = (int) substr($personId, 4);
            $employee = \App\Models\Employee::find($eid);
            if (!$employee) return back()->withErrors(['person_id' => 'Selected employee not found.']);
            $employeeId = $employee->id;
            $oldPersonId = $record->employee_id ? 'emp_' . $record->employee_id : 'user_' . $record->user_id;
            if ($personId !== $oldPersonId) {
                $snapshotName     = $employee->full_name;
                $snapshotPosition = $employee->position;
                $snapshotOffice   = $employee->office;
            }
        } else {
            return back()->withErrors(['person_id' => 'Invalid person selection.']);
        }

        $record->update([
            'user_id'           => $userId,
            'employee_id'       => $employeeId,
            'snapshot_name'     => $snapshotName,
            'snapshot_position' => $snapshotPosition,
            'snapshot_office'   => $snapshotOffice,
            'as_of_date'        => $request->input('as_of_date'),
            'el_vl'             => $request->input('el_vl') ?? null,
            'el_sl'             => $request->input('el_sl') ?? null,
            'no_pay_vl'         => $vl ?: null,
            'no_pay_sl'         => $sl ?: null,
            'no_pay_total'      => ($vl + $sl) ?: null,
            'no_pay_dates'      => $request->input('no_pay_dates') ?? null,
            'undertime_hours'   => $request->input('undertime_hours') ?? null,
            'undertime_minutes' => $request->input('undertime_minutes') ?? null,
            'undertime_dates'   => $request->input('undertime_dates') ?? null,
        ]);

        $displayName = $snapshotName ?? 'unknown employee';
        \App\Models\ActivityLog::log(
            'record_edited',
            'Leave record edited for ' . $displayName,
            ['leave_record_id' => $record->id, 'employee' => $displayName]
        );
        return redirect()->route('admin.recorded-entries')->with('status', 'Record updated successfully!');
    })->name('admin.recorded-entries.update');

    Route::get('/recorded-entries/{id}/pdf', function (\Illuminate\Http\Request $request, $id) {
        $record = \App\Models\LeaveRecord::with('user')->findOrFail($id);
        $email = $record->user?->email;
        \App\Models\ActivityLog::log(
            'pdf_generated',
            'PDF generated for ' . ($record->user?->full_name ?? 'unknown employee'),
            ['leave_record_id' => $id, 'employee' => $record->user?->full_name, 'employee_email' => $email]
        );
        $certifierName     = $request->query('certifier_name', 'CARMELO L. CAGAS, MPA, MHRM');
        $certifierPosition = $request->query('certifier_position', 'Supervising Administrative Officer');
        $leaveTypes        = $request->query('leave_types', 'leave of absence/undertime/tardy');
        $preparedBy        = auth()->user();
        return view('admin.recorded_entries_pdf', compact(
            'record', 'certifierName', 'certifierPosition', 'preparedBy', 'leaveTypes'
        ));
    })->name('admin.recorded-entries.pdf');

    Route::delete('/recorded-entries/{id}', function ($id) {
        $record = \App\Models\LeaveRecord::with('user')->findOrFail($id);
        $employeeName = $record->user?->full_name ?? 'unknown employee';

        $record->delete();

        \App\Models\ActivityLog::log(
            'record_deleted',
            "Leave record deleted for {$employeeName}",
            ['employee' => $employeeName]
        );

        return redirect()->route('admin.recorded-entries')->with('status', 'Record deleted successfully!');
    })->name('admin.recorded-entries.destroy');

    // Export PDF
    Route::post('/recorded-entries/export/pdf', function (Request $request) {
        if (!$request->filled('ids')) {
            return back()->withErrors(['export' => 'No records selected for export.']);
        }
        $ids = explode(',', $request->input('ids'));
        $records = \App\Models\LeaveRecord::with('user', 'employee')->whereIn('id', $ids)->orderByDesc('created_at')->get();
        if (auth()->user()->isEmployee()) {
             $records = $records->filter(fn($r) => $r->created_by === auth()->id());
        }
        $preparedBy = auth()->user();
        
        \App\Models\ActivityLog::log(
            'pdf_generated',
            'Exported ' . $records->count() . ' recorded entries to PDF',
            ['count' => $records->count()]
        );

        return view('admin.recorded_entries_export_pdf', compact('records', 'preparedBy'));
    })->name('admin.recorded-entries.export-pdf');

    // Export Excel (CSV)
    Route::post('/recorded-entries/export/excel', function (Request $request) {
        if (!$request->filled('ids')) {
            return back()->withErrors(['export' => 'No records selected for export.']);
        }
        $ids = explode(',', $request->input('ids'));
        $recordsQuery = \App\Models\LeaveRecord::with('user', 'employee')
            ->whereIn('id', $ids)
            ->orderByDesc('created_at');
            
        $records = $recordsQuery->get();
        if (auth()->user()->isEmployee()) {
             $records = $records->filter(fn($r) => $r->created_by === auth()->id());
        }

        $filename = "Leave_Records_" . now()->format('Ymd_His') . ".csv";

        $callback = function() use($records) {
            $file = fopen('php://output', 'w');
            
            // Output UTF-8 BOM for proper Excel display
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, [
                'No.', 'Name', 'Position', 'Office', 
                'No Pay VL', 'No Pay SL', 'No Pay Total', 
                'Undertime Hrs', 'Undertime Mins'
            ]);
            
            $counter = 1;
            foreach ($records as $rec) {
                $u = $rec->user;
                fputcsv($file, [
                    $counter++,
                    $rec->snapshot_name ?? ($u ? $u->full_name : ''),
                    $rec->snapshot_position ?? ($u ? $u->position : ''),
                    $rec->snapshot_office ?? ($u ? $u->office : ''),
                    $rec->no_pay_vl,
                    $rec->no_pay_sl,
                    $rec->no_pay_total,
                    $rec->undertime_hours,
                    $rec->undertime_minutes
                ]);
            }
            fclose($file);
        };

        \App\Models\ActivityLog::log(
            'pdf_generated', // reusing existing action type since export_generated might not exist
            'Exported ' . $records->count() . ' recorded entries to XLSX (CSV)',
            ['count' => $records->count()]
        );

        return response()->streamDownload($callback, $filename, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    })->name('admin.recorded-entries.export-excel');

    // Activity Log (admin only)
    Route::get('/activity', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\ActivityLog::with('user')->orderByDesc('created_at');

        // Filter by actor name (searches last_name, given_name, name columns on related user)
        if ($request->filled('name')) {
            $search = $request->input('name');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('last_name',  'like', "%{$search}%")
                          ->orWhere('given_name', 'like', "%{$search}%")
                          ->orWhere('name',        'like', "%{$search}%");
                });
            });
        }

        // Filter by month (1–12)
        if ($request->filled('month')) {
            $query->whereMonth('created_at', (int) $request->input('month'));
        }

        // Filter by one or more action types
        $selectedTypes = array_filter((array) $request->input('types', []));
        if (!empty($selectedTypes)) {
            $query->whereIn('action', $selectedTypes);
        }

        $logs = $query->limit(500)->get();

        // Distinct actor names for the datalist suggestion
        $actorNames = \App\Models\User::whereIn('id', $logs->pluck('user_id')->filter()->unique())
            ->get()
            ->map(fn ($u) => $u->full_name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.activity', compact('logs', 'actorNames'));
    })->name('admin.activity');

    // Confirm a pending employee registration
    Route::post('/activity/{log}/confirm', function (\App\Models\ActivityLog $log) {
        $userId = $log->meta['new_user_id'] ?? null;
        if (!$userId) {
            return back()->withErrors(['confirm' => 'Cannot identify the user for this log entry.']);
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return back()->withErrors(['confirm' => 'User not found.']);
        }

        $user->update(['is_confirmed' => true]);

        \App\Models\ActivityLog::log(
            'registration_confirmed',
            'Registration confirmed for ' . ($user->full_name ?: $user->name) . ' (' . $user->email . ')',
            ['confirmed_user_id' => $user->id]
        );

        return back()->with('status', 'Account confirmed. The employee can now log in.');
    })->name('admin.activity.confirm');

    // Reject a pending employee registration — permanently deletes the user record
    Route::post('/activity/{log}/reject', function (\App\Models\ActivityLog $log) {
        $userId = $log->meta['new_user_id'] ?? null;
        if (!$userId) {
            return back()->withErrors(['confirm' => 'Cannot identify the user for this log entry.']);
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return back()->withErrors(['confirm' => 'User not found.']);
        }

        $userName  = $user->full_name ?: $user->name;
        $userEmail = $user->email;

        // Hard-delete the user so their credentials are fully removed
        $user->delete();

        \App\Models\ActivityLog::log(
            'registration_rejected',
            'Registration rejected and account deleted for ' . $userName . ' (' . $userEmail . ')',
            ['rejected_user_email' => $userEmail]
        );

        return back()->with('status', 'Account rejected and login credentials deleted.');
    })->name('admin.activity.reject');

    // Profile
    Route::get('/profile', function () {
        $positions = \App\Models\Position::orderBy('name')->get();
        $offices   = \App\Models\Office::orderBy('name')->get();
        return view('admin.profile', compact('positions', 'offices'));
    })->name('admin.profile');

    Route::patch('/profile', function (Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'last_name'       => ['required', 'string', 'max:255'],
            'given_name'      => ['required', 'string', 'max:255'],
            'middle_name'     => ['nullable', 'string', 'max:255'],
            'suffix'          => ['nullable', 'string', 'max:50'],
            'employee_number' => ['nullable', 'string', 'max:100', 'unique:users,employee_number,' . $user->id],
            'office'          => ['nullable', 'string', 'max:255'],
            'position'        => ['nullable', 'string', 'max:255'],
        ]);

        $user->last_name       = $validated['last_name'];
        $user->given_name      = $validated['given_name'];
        $user->middle_name     = $validated['middle_name'] ?? null;
        $user->suffix          = $validated['suffix'] ?? null;
        $user->employee_number = $validated['employee_number'] ?? null;
        $user->office          = $validated['office'] ?? null;
        $user->position        = $validated['position'] ?? null;

        // Keep the legacy `name` column in sync (Given Middle Last)
        $nameParts = array_filter([
            $validated['given_name'],
            $validated['middle_name'] ?? null,
            $validated['last_name'],
        ]);
        $user->name = implode(' ', $nameParts);

        $user->save();

        return redirect()->route('admin.profile')->with('status', 'Profile updated successfully!');
    })->name('admin.profile.update');

    // Settings
    Route::get('/settings', function () {
        $positions = \App\Models\Position::orderBy('name')->get();
        $offices   = \App\Models\Office::orderBy('name')->get();
        $employees = \App\Models\Employee::orderBy('last_name')->orderBy('given_name')->get();
        // All confirmed users (both admins and employees) excluding the currently logged-in user
        $allUsers = \App\Models\User::where('id', '!=', auth()->id())
            ->where('is_confirmed', true)
            ->orderBy('last_name')->orderBy('given_name')->orderBy('name')
            ->get(['id','last_name','given_name','middle_name','suffix','name','role'])
            ->map(fn($u) => [
                'id'           => $u->id,
                'display_name' => $u->full_name,
                'role'         => $u->role,
                'initials'     => strtoupper(substr($u->given_name ?: $u->name, 0, 1) . substr($u->last_name ?: '', 0, 1)),
                'search_str'   => strtolower($u->full_name . ' ' . ($u->name ?? '')),
            ]);
        return view('admin.settings', compact('positions', 'offices', 'allUsers', 'employees'));
    })->name('admin.settings');

    // Change Password (via Settings)
    Route::patch('/settings/password', function (Request $request) {
        $request->validate([
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.settings')->with('status', 'Password updated successfully!');
    })->name('admin.settings.password');

    // ── Employees CRUD ───────────────────────────────────────────────────────
    Route::post('/settings/employees', function (Request $request) {
        $request->validate([
            'last_name'       => ['required', 'string', 'max:255'],
            'given_name'      => ['required', 'string', 'max:255'],
            'middle_name'     => ['nullable', 'string', 'max:255'],
            'suffix'          => ['nullable', 'string', 'max:50'],
            'employee_number' => ['required', 'string', 'max:100', 'unique:employees,employee_number'],
            'office'          => ['required', 'string', 'max:255'],
            'position'        => ['required', 'string', 'max:255'],
        ]);

        // ── Duplicate name check ─────────────────────────────────────────
        // Reject if Last Name + Given Name + Middle Name already exists,
        // UNLESS the suffix is different (a different suffix is allowed).
        $lastName   = trim($request->input('last_name'));
        $givenName  = trim($request->input('given_name'));
        $middleName = $request->input('middle_name') ? trim($request->input('middle_name')) : null;
        $suffix     = $request->input('suffix') ? trim($request->input('suffix')) : null;

        // Find any employee matching Last + Given + Middle
        $sameNameRecords = \App\Models\Employee::whereRaw('LOWER(last_name)  = ?', [strtolower($lastName)])
            ->whereRaw('LOWER(given_name) = ?', [strtolower($givenName)])
            ->where(function ($q) use ($middleName) {
                if ($middleName === null) {
                    $q->whereNull('middle_name')->orWhere('middle_name', '');
                } else {
                    $q->whereRaw('LOWER(middle_name) = ?', [strtolower($middleName)]);
                }
            })
            ->get(['suffix']);

        if ($sameNameRecords->isNotEmpty()) {
            // Allow only if the new suffix is non-empty and differs from ALL existing suffixes
            $newSuffixLower = $suffix ? strtolower($suffix) : '';
            $existingSuffixes = $sameNameRecords->map(fn($e) => strtolower(trim($e->suffix ?? '')))->all();

            $isDuplicate = empty($newSuffixLower)
                // No suffix provided — duplicate if any existing record also has no suffix
                ? in_array('', $existingSuffixes)
                // Suffix provided — duplicate if the same suffix already exists
                : in_array($newSuffixLower, $existingSuffixes);

            if ($isDuplicate) {
                return redirect()->route('admin.settings')
                    ->withErrors(['last_name' => 'This employee already exists.'])
                    ->withInput();
            }
        }
        // ────────────────────────────────────────────────────────────────

        $employee = \App\Models\Employee::create([
            'last_name'       => trim($request->input('last_name')),
            'given_name'      => trim($request->input('given_name')),
            'middle_name'     => $request->input('middle_name') ? trim($request->input('middle_name')) : null,
            'suffix'          => $request->input('suffix') ? trim($request->input('suffix')) : null,
            'employee_number' => trim($request->input('employee_number')),
            'office'          => trim($request->input('office')),
            'position'        => trim($request->input('position')),
        ]);
        \App\Models\ActivityLog::log(
            'org_added',
            'Employee added: ' . $employee->full_name,
            ['entity' => 'employee', 'employee_id' => $employee->id]
        );
        return redirect()->route('admin.settings')->with('status', 'Employee added successfully.');
    })->name('admin.settings.employees.store');

    Route::put('/settings/employees/{employee}', function (Request $request, \App\Models\Employee $employee) {
        $request->validate([
            'last_name'       => ['required', 'string', 'max:255'],
            'given_name'      => ['required', 'string', 'max:255'],
            'middle_name'     => ['nullable', 'string', 'max:255'],
            'suffix'          => ['nullable', 'string', 'max:50'],
            'employee_number' => ['nullable', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('employees', 'employee_number')->ignore($employee->id)],
            'office'          => ['nullable', 'string', 'max:255'],
            'position'        => ['nullable', 'string', 'max:255'],
        ]);

        // ── Duplicate name check (exclude self) ──────────────────────────
        $lastName   = trim($request->input('last_name'));
        $givenName  = trim($request->input('given_name'));
        $middleName = $request->input('middle_name') ? trim($request->input('middle_name')) : null;
        $suffix     = $request->input('suffix') ? trim($request->input('suffix')) : null;

        $duplicate = \App\Models\Employee::where('id', '!=', $employee->id)
            ->whereRaw('LOWER(last_name)  = ?', [strtolower($lastName)])
            ->whereRaw('LOWER(given_name) = ?', [strtolower($givenName)])
            ->where(function ($q) use ($middleName) {
                if ($middleName === null) {
                    $q->whereNull('middle_name')->orWhere('middle_name', '');
                } else {
                    $q->whereRaw('LOWER(middle_name) = ?', [strtolower($middleName)]);
                }
            })
            ->where(function ($q) use ($suffix) {
                if ($suffix === null) {
                    $q->whereNull('suffix')->orWhere('suffix', '');
                } else {
                    $q->whereRaw('LOWER(suffix) = ?', [strtolower($suffix)]);
                }
            })
            ->exists();

        if ($duplicate) {
            return redirect()->route('admin.settings')
                ->withErrors(['last_name' => 'An employee with the same Last Name, Given Name, Middle Name, and Suffix already exists.'])
                ->withInput();
        }
        // ────────────────────────────────────────────────────────────────

        $employee->update([
            'last_name'       => trim($request->input('last_name')),
            'given_name'      => trim($request->input('given_name')),
            'middle_name'     => $request->input('middle_name') ? trim($request->input('middle_name')) : null,
            'suffix'          => $request->input('suffix') ? trim($request->input('suffix')) : null,
            'employee_number' => $request->input('employee_number') ? trim($request->input('employee_number')) : null,
            'office'          => $request->input('office') ? trim($request->input('office')) : null,
            'position'        => $request->input('position') ? trim($request->input('position')) : null,
        ]);
        \App\Models\ActivityLog::log(
            'org_edited',
            'Employee updated: ' . $employee->full_name,
            ['entity' => 'employee', 'employee_id' => $employee->id]
        );
        return redirect()->route('admin.settings')->with('status', 'Employee updated.');
    })->name('admin.settings.employees.update');

    Route::delete('/settings/employees/{employee}', function (\App\Models\Employee $employee) {
        $name = $employee->full_name;
        $employee->delete();
        \App\Models\ActivityLog::log(
            'org_deleted',
            'Employee deleted: ' . $name,
            ['entity' => 'employee']
        );
        return redirect()->route('admin.settings')->with('status', 'Employee deleted.');
    })->name('admin.settings.employees.destroy');

    // ── Positions CRUD ───────────────────────────────────────────────────────
    // Batch-store: accepts codes[] + names[] array, rejects entire batch on any duplicate
    Route::post('/settings/positions', function (Request $request) {
        $request->validate([
            'names'   => ['required', 'array', 'min:1'],
            'names.*' => ['required', 'string', 'max:255'],
            'codes'   => ['nullable', 'array'],
            'codes.*' => ['nullable', 'string', 'max:100'],
        ]);

        $names = array_map('trim', $request->input('names', []));
        $codes = array_map('trim', $request->input('codes', []));

        $errors = [];

        // ── Check for duplicates within the batch itself ──────────────────
        $seenNames = [];
        $seenCodes = [];
        foreach ($names as $i => $name) {
            $code = $codes[$i] ?? '';

            if (in_array(strtolower($name), $seenNames)) {
                $errors[] = "Duplicate in your list — position name \"$name\" appears more than once.";
            } else {
                $seenNames[] = strtolower($name);
            }

            if ($code !== '' && in_array(strtolower($code), $seenCodes)) {
                $errors[] = "Duplicate in your list — position code \"$code\" appears more than once.";
            } elseif ($code !== '') {
                $seenCodes[] = strtolower($code);
            }
        }

        // ── Check against the database ────────────────────────────────────
        foreach ($names as $i => $name) {
            $code = $codes[$i] ?? '';

            if (\App\Models\Position::whereRaw('LOWER(name) = ?', [strtolower($name)])->exists()) {
                $errors[] = "Position name \"$name\" already exists in the database.";
            }

            if ($code !== '' && \App\Models\Position::whereRaw('LOWER(code) = ?', [strtolower($code)])->exists()) {
                $errors[] = "Position code \"$code\" is already used by another position.";
            }
        }

        if (!empty($errors)) {
            return redirect()->route('admin.settings')
                ->withErrors($errors)
                ->withInput();
        }

        // ── All clear – save ──────────────────────────────────────────────
        $saved = 0;
        $names_saved = [];
        foreach ($names as $i => $name) {
            $code = $codes[$i] ?? '';
            \App\Models\Position::create(['code' => $code ?: null, 'name' => $name]);
            $saved++;
            $names_saved[] = $name;
        }

        \App\Models\ActivityLog::log(
            'org_added',
            $saved === 1
                ? 'Position added: ' . $names_saved[0]
                : "$saved positions added: " . implode(', ', $names_saved),
            ['entity' => 'position', 'count' => $saved]
        );

        return redirect()->route('admin.settings')->with('status',
            "$saved position(s) added successfully.");
    })->name('admin.settings.positions.store');

    // Rename a single position
    Route::put('/settings/positions/{position}', function (Request $request, \App\Models\Position $position) {
        $request->validate([
            'name' => ['required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('positions', 'name')->ignore($position->id)],
            'code' => ['nullable', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('positions', 'code')->ignore($position->id)->whereNotNull('code')],
        ], [
            'name.unique' => 'A position with that name already exists.',
            'code.unique' => 'That position code is already used by another position.',
        ]);
        $position->update([
            'name' => trim($request->input('name')),
            'code' => trim($request->input('code')) ?: null,
        ]);
        \App\Models\ActivityLog::log(
            'org_edited',
            'Position updated: ' . $position->name,
            ['entity' => 'position', 'position_id' => $position->id]
        );
        return redirect()->route('admin.settings')->with('status', 'Position updated.');
    })->name('admin.settings.positions.update');

    // Delete a single position
    Route::delete('/settings/positions/{position}', function (\App\Models\Position $position) {
        $name = $position->name;
        $position->delete();
        \App\Models\ActivityLog::log(
            'org_deleted',
            'Position deleted: ' . $name,
            ['entity' => 'position']
        );
        return redirect()->route('admin.settings')->with('status', 'Position deleted.');
    })->name('admin.settings.positions.destroy');

    // ── Offices CRUD ─────────────────────────────────────────────────────────
    // Batch-store: accepts names[] array, skips duplicates
    Route::post('/settings/offices', function (Request $request) {
        $request->validate(['names'   => ['required', 'array', 'min:1'],
                            'names.*' => ['required', 'string', 'max:255']]);
        $saved = 0;
        $names_saved = [];
        foreach ($request->input('names') as $raw) {
            $name = trim($raw);
            if ($name && !\App\Models\Office::where('name', $name)->exists()) {
                \App\Models\Office::create(['name' => $name]);
                $saved++;
                $names_saved[] = $name;
            }
        }
        if ($saved > 0) {
            \App\Models\ActivityLog::log(
                'org_added',
                $saved === 1
                    ? 'Office added: ' . $names_saved[0]
                    : "$saved offices added: " . implode(', ', $names_saved),
                ['entity' => 'office', 'count' => $saved]
            );
        }
        return redirect()->route('admin.settings')->with('status',
            $saved ? "$saved office(s) added successfully." : 'No new offices were added (duplicates skipped).');
    })->name('admin.settings.offices.store');

    // Rename a single office
    Route::put('/settings/offices/{office}', function (Request $request, \App\Models\Office $office) {
        $request->validate(['name' => ['required', 'string', 'max:255',
            \Illuminate\Validation\Rule::unique('offices', 'name')->ignore($office->id)]]);
        $office->update(['name' => trim($request->input('name'))]);
        \App\Models\ActivityLog::log(
            'org_edited',
            'Office updated: ' . $office->name,
            ['entity' => 'office', 'office_id' => $office->id]
        );
        return redirect()->route('admin.settings')->with('status', 'Office updated.');
    })->name('admin.settings.offices.update');

    // Delete a single office
    Route::delete('/settings/offices/{office}', function (\App\Models\Office $office) {
        $name = $office->name;
        $office->delete();
        \App\Models\ActivityLog::log(
            'org_deleted',
            'Office deleted: ' . $name,
            ['entity' => 'office']
        );
        return redirect()->route('admin.settings')->with('status', 'Office deleted.');
    })->name('admin.settings.offices.destroy');

    // ── User Access Level ────────────────────────────────────────────────────
    Route::patch('/settings/users/{user}/role', function (Request $request, \App\Models\User $user) {
        // Prevent self-demotion
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.settings')->withErrors(['role' => 'You cannot change your own access level.']);
        }
        $request->validate([
            'role' => ['required', 'in:admin,employee'],
        ]);
        $user->update(['role' => $request->input('role')]);
        $label = $request->input('role') === 'admin' ? 'Super Admin (Full Access)' : 'Employee (Limited Access)';
        \App\Models\ActivityLog::log(
            'role_updated',
            'Access level updated for ' . ($user->full_name ?: $user->name) . ' → ' . $label,
            ['target_user_id' => $user->id, 'new_role' => $request->input('role')]
        );
        return redirect()->route('admin.settings')->with('status', ($user->full_name ?: $user->name) . "'s access level updated to {$label}.");
    })->name('admin.settings.user-role.update');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
