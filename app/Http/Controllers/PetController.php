<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetStoreRequest;
use App\Http\Requests\PetUpdateRequest;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuditLogService;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PetController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Pet::class);

        $pets = Pet::query()
            ->withCount(['appointments', 'vaccinations'])
            ->ownedBy($request->user())
            ->latest()
            ->paginate(10);

        return view('pets.index', compact('pets'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Pet::class);

        $clients = collect();

        if ($request->user()?->isStaffOrAdmin()) {
            $clients = User::query()
                ->where('role', User::ROLE_CLIENT)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return view('pets.create', compact('clients'));
    }

    public function store(PetStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Pet::class);

        $payload = array_merge($request->validated(), $this->validateAgeInput($request));
        $ownerUserId = (int) ($payload['owner_user_id'] ?? 0);
        unset($payload['owner_user_id']);

        if ($request->user()->isStaffOrAdmin() && $ownerUserId > 0) {
            $owner = User::query()
                ->where('role', User::ROLE_CLIENT)
                ->where('is_active', true)
                ->findOrFail($ownerUserId);

            $payload['user_id'] = $owner->id;
            $payload['owner_name'] = $owner->name;
        } else {
            $payload['user_id'] = $request->user()->id;
            $payload['owner_name'] = $request->user()->name;
        }

        $pet = Pet::create($payload);
        $this->activityLogService->log(
            $request->user(),
            'Pet Added',
            sprintf('Added pet profile for %s.', $pet->pet_name)
        );
        $this->auditLogService->log(
            $request->user(),
            'created',
            'pet',
            $pet->id,
            sprintf('Created pet profile for %s.', $pet->pet_name)
        );

        return redirect()
            ->route('pets.edit', $pet)
            ->with('success', 'Pet created successfully.');
    }

    public function edit(Request $request, Pet $pet): View
    {
        $this->authorize('update', $pet);

        $clients = collect();

        if ($request->user()?->isStaffOrAdmin()) {
            $clients = User::query()
                ->where('role', User::ROLE_CLIENT)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return view('pets.edit', compact('pet', 'clients'));
    }

    public function show(Pet $pet): View
    {
        $this->authorize('view', $pet);

        $relations = [
            'medicalRecords' => fn ($query) => $query->latest('visit_date'),
            'appointments' => fn ($query) => $query->latest('appointment_date'),
            'vaccinations' => fn ($query) => $query->latest('date_given'),
        ];

        if (Schema::hasTable('hospitalizations')) {
            $relations['hospitalizations'] = fn ($query) => $query->latest('admitted_date');
        }

        $pet->load($relations);

        $timelineEntries = $this->buildTimelineEntries($pet);

        return view('pets.show', compact('pet', 'timelineEntries'));
    }

    public function update(PetUpdateRequest $request, Pet $pet): RedirectResponse
    {
        $this->authorize('update', $pet);

        $payload = array_merge($request->validated(), $this->validateAgeInput($request));
        $ownerUserId = (int) ($payload['owner_user_id'] ?? 0);
        unset($payload['owner_user_id']);

        if ($request->user()->isStaffOrAdmin() && $ownerUserId > 0) {
            $owner = User::query()
                ->where('role', User::ROLE_CLIENT)
                ->where('is_active', true)
                ->findOrFail($ownerUserId);

            $payload['user_id'] = $owner->id;
            $payload['owner_name'] = $owner->name;
        }

        if (! $request->user()->isStaffOrAdmin()) {
            $payload['user_id'] = $request->user()->id;
            $payload['owner_name'] = $request->user()->name;
        }

        $pet->update($payload);
        $this->auditLogService->log(
            $request->user(),
            'updated',
            'pet',
            $pet->id,
            sprintf('Updated pet profile for %s.', $pet->pet_name)
        );

        return redirect()
            ->route('pets.index')
            ->with('success', 'Pet updated successfully.');
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        $this->authorize('delete', $pet);

        $petId = $pet->id;
        $petName = $pet->pet_name;
        $pet->delete();
        $this->auditLogService->log(
            auth()->user(),
            'deleted',
            'pet',
            $petId,
            sprintf('Deleted pet profile for %s.', $petName)
        );

        return redirect()
            ->route('pets.index')
            ->with('success', 'Pet deleted successfully.');
    }

    private function validateAgeInput(Request $request): array
    {
        return $request->validate([
            'age_value' => ['required', 'integer', 'min:0', 'max:240'],
            'age_type' => ['required', Rule::in(['month', 'year'])],
        ]);
    }

    private function buildTimelineEntries(Pet $pet): Collection
    {
        $timeline = collect();

        foreach ($pet->vaccinations as $vaccination) {
            $timeline->push([
                'date' => $vaccination->date_given,
                'type' => 'Vaccination',
                'type_badge_class' => 'bg-blue-100 text-blue-700',
                'description' => sprintf(
                    '%s%s',
                    $vaccination->vaccine_name,
                    $vaccination->next_due_date ? ' (Next due: '.$vaccination->next_due_date->format('M d, Y').')' : ''
                ),
                'status' => 'Recorded',
                'status_badge_class' => 'bg-slate-100 text-slate-600',
            ]);
        }

        foreach ($pet->appointments as $appointment) {
            $type = $appointment->is_emergency
                ? 'Emergency'
                : match ($appointment->type) {
                    Appointment::TYPE_CHECKUP => 'Checkup',
                    Appointment::TYPE_GROOMING => 'Grooming',
                    default => 'Vaccination',
                };

            $typeBadgeClass = match ($type) {
                'Checkup' => 'bg-pink-100 text-pink-700',
                'Grooming' => 'bg-green-100 text-green-700',
                'Emergency' => 'bg-rose-100 text-rose-700',
                default => 'bg-blue-100 text-blue-700',
            };

            $timeline->push([
                'date' => $appointment->appointment_date,
                'type' => $type,
                'type_badge_class' => $typeBadgeClass,
                'description' => $appointment->display_purpose ?: 'Appointment request',
                'status' => $appointment->status_label,
                'status_badge_class' => 'bg-slate-100 text-slate-600',
            ]);
        }

        foreach ($pet->medicalRecords as $record) {
            $timeline->push([
                'date' => $record->visit_date,
                'type' => 'Checkup',
                'type_badge_class' => 'bg-pink-100 text-pink-700',
                'description' => $record->diagnosis
                    ? 'Consultation: '.$record->diagnosis
                    : 'Consultation record updated',
                'status' => 'Documented',
                'status_badge_class' => 'bg-slate-100 text-slate-600',
            ]);
        }

        if (Schema::hasTable('hospitalizations') && $pet->relationLoaded('hospitalizations')) {
            foreach ($pet->hospitalizations as $hospitalization) {
                $timeline->push([
                    'date' => $hospitalization->admitted_date,
                    'type' => 'Hospitalization',
                    'type_badge_class' => 'bg-rose-100 text-rose-700',
                    'description' => $hospitalization->notes ?: 'Hospitalization record created',
                    'status' => ucfirst($hospitalization->status),
                    'status_badge_class' => $hospitalization->status === 'active'
                        ? 'bg-rose-100 text-rose-700'
                        : 'bg-emerald-100 text-emerald-700',
                ]);
            }
        }

        return $timeline
            ->sortByDesc(fn (array $item) => optional($item['date'])?->timestamp ?? 0)
            ->values();
    }
}
