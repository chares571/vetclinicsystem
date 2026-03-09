<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentStoreRequest;
use App\Http\Requests\AppointmentUpdateRequest;
use App\Models\Appointment;
use App\Models\Pet;
use App\Services\ActivityLogService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::query()
            ->with('pet:id,pet_name,user_id')
            ->ownedBy($request->user())
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_APPROVED])
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('appointments.index', compact('appointments'));
    }

    public function completed(Request $request): View
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::query()
            ->with('pet:id,pet_name,user_id')
            ->ownedBy($request->user())
            ->where('status', Appointment::STATUS_COMPLETED)
            ->latest('appointment_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('appointments.completed', compact('appointments'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Appointment::class);

        $pets = Pet::query()
            ->ownedBy($request->user())
            ->orderBy('pet_name')
            ->get(['id', 'pet_name']);

        return view('appointments.create', [
            'pets' => $pets,
            'appointmentType' => null,
            'allowTypeSelection' => true,
        ]);
    }

    public function createGrooming(Request $request): View
    {
        $this->authorize('create', Appointment::class);

        abort_unless($request->user()->isClient(), 403);

        $pets = Pet::query()
            ->ownedBy($request->user())
            ->orderBy('pet_name')
            ->get(['id', 'pet_name']);

        return view('appointments.grooming-create', [
            'pets' => $pets,
            'appointmentType' => Appointment::TYPE_GROOMING,
        ]);
    }

    public function store(AppointmentStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Appointment::class);

        $payload = $request->validated();
        $petId = $payload['pet_id'] ?? null;
        $pet = null;

        if ($petId) {
            $pet = Pet::query()->findOrFail($petId);
            $this->authorize('view', $pet);
        } elseif ($request->user()->isStaffOrAdmin()) {
            $pet = Pet::query()->create([
                'user_id' => null,
                'owner_name' => trim((string) ($payload['owner_name'] ?? 'Walk-in Client')),
                'contact_number' => trim((string) ($payload['contact_number'] ?? 'N/A')),
                'pet_name' => trim((string) ($payload['pet_name'] ?? 'Walk-in Pet')),
                'species' => 'Unknown',
                'breed' => null,
                'sex' => null,
                'age' => null,
                'age_value' => null,
                'age_type' => null,
            ]);
        }

        if (! $pet) {
            return back()
                ->withInput()
                ->with('error', 'Please select a pet or provide walk-in pet details.');
        }

        $payload['pet_id'] = $pet->id;
        unset($payload['pet_name'], $payload['owner_name'], $payload['contact_number']);
        $payload['type'] = $payload['type'] ?? Appointment::TYPE_VACCINATION;

        if ($payload['type'] === Appointment::TYPE_GROOMING && ! $request->user()->isClient()) {
            abort(403, 'Only clients can submit grooming appointment requests.');
        }

        if ($request->user()->isClient()) {
            $payload['status'] = Appointment::STATUS_PENDING;
            if ($this->supportsEmergencyFlag()) {
                $payload['is_emergency'] = false;
            }
            if ($this->supportsStaffAssignment()) {
                $payload['staff_id'] = null;
            }
        } else {
            if ($this->supportsEmergencyFlag()) {
                $payload['is_emergency'] = $request->boolean('is_emergency');
            }
            if ($this->supportsStaffAssignment()) {
                $payload['staff_id'] = $request->user()->id;
            }
        }

        $ownerId = $request->user()->isStaffOrAdmin() ? ($pet->user_id ?? null) : $request->user()->id;
        $payload = $this->normalizeAppointmentPayload($request, $payload);

        if ($this->isDuplicateAppointmentSubmission($ownerId, $payload)) {
            return back()
                ->withInput()
                ->with('error', 'Duplicate submission detected. Please wait a few seconds before submitting again.');
        }

        if ($request->user()->isClient() && $this->hasExistingPendingDuplicate($ownerId, $payload)) {
            return back()
                ->withInput()
                ->with('error', 'This appointment request is already pending.');
        }

        $appointment = Appointment::create(array_merge($payload, ['user_id' => $ownerId]));

        return redirect()
            ->route('appointments.edit', $appointment)
            ->with('success', $payload['type'] === Appointment::TYPE_GROOMING
                ? 'Grooming appointment requested successfully.'
                : 'Appointment created successfully.');
    }

    public function edit(Request $request, Appointment $appointment): View
    {
        $this->authorize('update', $appointment);

        $pets = Pet::query()
            ->ownedBy($request->user())
            ->orderBy('pet_name')
            ->get(['id', 'pet_name']);

        return view('appointments.edit', [
            'appointment' => $appointment,
            'pets' => $pets,
            'appointmentType' => $appointment->type ?? Appointment::TYPE_VACCINATION,
        ]);
    }

    public function update(AppointmentUpdateRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $user = $request->user();
        $pet = Pet::query()->findOrFail($request->validated('pet_id'));
        $this->authorize('view', $pet);

        $payload = $request->validated();
        $payload['type'] = $appointment->type ?? Appointment::TYPE_VACCINATION;
        $originalStatus = $appointment->status;

        if ($user->isClient()) {
            if (in_array($appointment->status, [
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_REJECTED,
            ], true)) {
                return back()->with('error', 'This appointment can no longer be modified.');
            }

            $payload['pet_id'] = $appointment->pet_id;
            $payload['user_id'] = $appointment->user_id;
            $payload['status'] = $appointment->status;
            if ($this->supportsEmergencyFlag()) {
                $payload['is_emergency'] = $appointment->is_emergency;
            }
        }

        if ($user->isStaffOrAdmin()) {
            $payload['user_id'] = $pet->user_id;
            if ($this->supportsEmergencyFlag()) {
                $payload['is_emergency'] = $request->boolean('is_emergency');
            }
            if ($this->supportsStaffAssignment()) {
                $payload['staff_id'] = $user->id;
            }
        }

        $payload = $this->normalizeAppointmentPayload($request, $payload);
        $appointment->update($payload);
        $this->logAppointmentStatusChange($request, $appointment, $originalStatus);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $this->authorize('delete', $appointment);

        $appointmentId = $appointment->id;
        $petName = $appointment->pet?->pet_name ?? 'Unknown pet';
        $appointment->delete();
        $this->auditLogService->log(
            auth()->user(),
            'deleted',
            'appointment',
            $appointmentId,
            sprintf('Deleted appointment for %s.', $petName)
        );

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('cancel', $appointment);

        $user = $request->user();

        if ($appointment->status === Appointment::STATUS_CANCELLED) {
            return back()->with('error', 'Appointment is already cancelled.');
        }

        if ($appointment->status === Appointment::STATUS_COMPLETED) {
            return back()->with('error', 'Completed appointments cannot be cancelled.');
        }

        if ($user->isClient() && $appointment->status !== Appointment::STATUS_PENDING) {
            return back()->with('error', 'Only pending appointments can be cancelled.');
        }

        $originalStatus = $appointment->status;
        $cancelPayload = ['status' => Appointment::STATUS_CANCELLED];

        if ($this->supportsStaffAssignment() && $user->isStaffOrAdmin()) {
            $cancelPayload['staff_id'] = $user->id;
        }

        $appointment->update($cancelPayload);

        $this->logAppointmentStatusChange($request, $appointment, $originalStatus);

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    private function logAppointmentStatusChange(Request $request, Appointment $appointment, string $originalStatus): void
    {
        if ($appointment->status === $originalStatus) {
            return;
        }

        $action = match ($appointment->status) {
            Appointment::STATUS_APPROVED => 'Appointment Approved',
            Appointment::STATUS_COMPLETED => 'Appointment Completed',
            default => 'Status Updated',
        };

        $petName = $appointment->pet?->pet_name ?? 'Unknown pet';
        $description = sprintf(
            '%s status changed from %s to %s.',
            $petName,
            ucfirst(str_replace('_', ' ', $originalStatus)),
            ucfirst(str_replace('_', ' ', $appointment->status))
        );

        $this->activityLogService->log($request->user(), $action, $description);
        $this->auditLogService->log(
            $request->user(),
            'status_changed',
            'appointment',
            $appointment->id,
            $description,
            [
                'from' => $originalStatus,
                'to' => $appointment->status,
                'is_emergency' => (bool) $appointment->is_emergency,
            ]
        );
    }

    private function normalizeAppointmentPayload(Request $request, array $payload): array
    {
        $type = $payload['type'] ?? Appointment::TYPE_VACCINATION;

        if ($type === Appointment::TYPE_GROOMING) {
            $serviceType = $payload['grooming_service_type'] ?? null;
            $payload['purpose'] = Appointment::GROOMING_SERVICE_LABELS[$serviceType] ?? 'Grooming Service';

            return $payload;
        }

        if ($type === Appointment::TYPE_VACCINATION) {
            $allowedVaccines = Appointment::VACCINE_PURPOSE_OPTIONS;
            $selectedVaccine = trim((string) $request->input('vaccination_purpose', $payload['purpose'] ?? ''));
            $otherVaccine = trim((string) $request->input('other_vaccine', ''));

            if (in_array($selectedVaccine, $allowedVaccines, true)) {
                $payload['purpose'] = $selectedVaccine;
            } elseif ($selectedVaccine === 'Others' && $otherVaccine !== '') {
                $payload['purpose'] = $otherVaccine;
            } else {
                abort(403, 'Invalid vaccine selection.');
            }

            $payload['grooming_service_type'] = null;
            $payload['preferred_time'] = null;
            $payload['notes'] = null;

            return $payload;
        }

        if ($type === Appointment::TYPE_CHECKUP) {
            $checkupPurpose = trim((string) $request->input('checkup_purpose', $payload['purpose'] ?? ''));

            if ($checkupPurpose === '') {
                abort(403, 'Invalid checkup purpose.');
            }

            $payload['purpose'] = $checkupPurpose;
            $payload['grooming_service_type'] = null;
            $payload['preferred_time'] = null;
            $payload['notes'] = null;

            return $payload;
        }

        abort(403, 'Invalid appointment type.');
    }

    private function supportsEmergencyFlag(): bool
    {
        static $supportsEmergencyFlag;

        if ($supportsEmergencyFlag === null) {
            $supportsEmergencyFlag = Schema::hasColumn('appointments', 'is_emergency');
        }

        return $supportsEmergencyFlag;
    }

    private function supportsStaffAssignment(): bool
    {
        static $supportsStaffAssignment;

        if ($supportsStaffAssignment === null) {
            $supportsStaffAssignment = Schema::hasColumn('appointments', 'staff_id');
        }

        return $supportsStaffAssignment;
    }

    private function isDuplicateAppointmentSubmission(int $ownerId, array $payload): bool
    {
        $fingerprint = [
            'owner_id' => $ownerId,
            'pet_id' => $payload['pet_id'] ?? null,
            'type' => $payload['type'] ?? Appointment::TYPE_VACCINATION,
            'appointment_date' => $payload['appointment_date'] ?? null,
            'preferred_time' => $payload['preferred_time'] ?? null,
            'grooming_service_type' => $payload['grooming_service_type'] ?? null,
            'purpose' => $payload['purpose'] ?? null,
            'status' => $payload['status'] ?? Appointment::STATUS_PENDING,
        ];

        $lockKey = 'appointments:submission:'.sha1(json_encode($fingerprint));

        return ! Cache::add($lockKey, true, now()->addSeconds(15));
    }

    private function hasExistingPendingDuplicate(int $ownerId, array $payload): bool
    {
        $type = $payload['type'] ?? Appointment::TYPE_VACCINATION;

        $query = Appointment::query()
            ->where('user_id', $ownerId)
            ->where('pet_id', $payload['pet_id'] ?? null)
            ->where('type', $type)
            ->whereDate('appointment_date', $payload['appointment_date'] ?? null)
            ->where('status', Appointment::STATUS_PENDING);

        if ($type === Appointment::TYPE_GROOMING) {
            return $query
                ->where('preferred_time', $payload['preferred_time'] ?? null)
                ->where('grooming_service_type', $payload['grooming_service_type'] ?? null)
                ->exists();
        }

        return $query
            ->where('purpose', $payload['purpose'] ?? null)
            ->exists();
    }
}
