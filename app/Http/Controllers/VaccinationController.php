<?php

namespace App\Http\Controllers;

use App\Http\Requests\VaccinationStoreRequest;
use App\Http\Requests\VaccinationUpdateRequest;
use App\Models\Vaccination;
use App\Services\AuditLogService;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VaccinationController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vaccination::class);

        $statusFilter = $this->normalizeStatusFilter($request->query('status'));
        $today = now()->toDateString();
        $dueSoonEnd = now()->addDays(7)->toDateString();

        $vaccinationsQuery = Vaccination::query()
            ->with('pet:id,user_id,owner_name,contact_number,pet_name,species,breed,sex,age,age_value,age_type')
            ->ownedBy($request->user());

        if ($statusFilter === 'overdue') {
            $vaccinationsQuery
                ->whereDate('next_due_date', '<', $today)
                ->orderBy('next_due_date');
        } elseif ($statusFilter === 'due_soon') {
            $vaccinationsQuery
                ->whereDate('next_due_date', '>=', $today)
                ->whereDate('next_due_date', '<=', $dueSoonEnd)
                ->orderBy('next_due_date');
        } else {
            $vaccinationsQuery->latest('date_given');
        }

        $vaccinations = $vaccinationsQuery
            ->paginate(10)
            ->withQueryString();

        return view('vaccinations.index', compact('vaccinations', 'statusFilter'));
    }

    public function create(): View
    {
        $this->authorize('create', Vaccination::class);

        return view('vaccinations.create');
    }

    public function store(VaccinationStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Vaccination::class);

        $payload = $this->sanitizeVaccinationPayload($request->validated());
        $payload['next_due_date'] = $this->resolveNextDueDate(
            (string) $payload['date_given'],
            (string) $payload['vaccine_name']
        );
        $payload['pet_id'] = null;
        $payload['user_id'] = $request->user()->id;

        $vaccination = Vaccination::create($payload);

        $this->auditLogService->log(
            $request->user(),
            'created',
            'vaccination',
            $vaccination->id,
            sprintf('Created vaccination record for %s.', $vaccination->display_pet_name)
        );

        return redirect()
            ->route('vaccinations.edit', $vaccination)
            ->with('success', 'Vaccination created successfully.');
    }

    public function edit(Vaccination $vaccination): View
    {
        $this->authorize('update', $vaccination);

        return view('vaccinations.edit', compact('vaccination'));
    }

    public function show(Vaccination $vaccination): View
    {
        $this->authorize('view', $vaccination);

        $vaccination->load([
            'pet' => fn ($query) => $query
                ->select('id', 'user_id', 'owner_name', 'contact_number', 'pet_name', 'species', 'breed', 'sex', 'age', 'age_value', 'age_type')
                ->with('owner'),
        ]);

        $isOverdue = $vaccination->next_due_date?->isBefore(today()) ?? false;
        $reminderMessage = $this->buildOwnerReminderMessage($vaccination);
        $ownerContactNumber = $vaccination->display_contact_number;
        if ($ownerContactNumber === 'N/A') {
            $ownerContactNumber = '';
        }
        $dialableOwnerContact = preg_replace('/[^0-9+]/', '', $ownerContactNumber) ?? '';
        $smsLink = $dialableOwnerContact !== ''
            ? 'sms:'.$dialableOwnerContact.'?body='.rawurlencode($reminderMessage)
            : null;
        $callLink = $dialableOwnerContact !== ''
            ? 'tel:'.$dialableOwnerContact
            : null;

        return view('vaccinations.show', compact(
            'vaccination',
            'isOverdue',
            'reminderMessage',
            'smsLink',
            'callLink'
        ));
    }

    public function update(VaccinationUpdateRequest $request, Vaccination $vaccination): RedirectResponse
    {
        $this->authorize('update', $vaccination);

        $payload = $this->sanitizeVaccinationPayload($request->validated());
        $payload['next_due_date'] = $this->resolveNextDueDate(
            (string) $payload['date_given'],
            (string) $payload['vaccine_name']
        );

        $vaccination->update($payload);
        $this->auditLogService->log(
            $request->user(),
            'updated',
            'vaccination',
            $vaccination->id,
            sprintf('Updated vaccination record for %s.', $vaccination->display_pet_name)
        );

        return redirect()
            ->route('vaccinations.index')
            ->with('success', 'Vaccination updated successfully.');
    }

    public function destroy(Vaccination $vaccination): RedirectResponse
    {
        $this->authorize('delete', $vaccination);

        $vaccinationId = $vaccination->id;
        $petName = $vaccination->display_pet_name !== 'N/A'
            ? $vaccination->display_pet_name
            : 'Unknown pet';
        $vaccination->delete();
        $this->auditLogService->log(
            auth()->user(),
            'deleted',
            'vaccination',
            $vaccinationId,
            sprintf('Deleted vaccination record for %s.', $petName)
        );

        return redirect()
            ->route('vaccinations.index')
            ->with('success', 'Vaccination deleted successfully.');
    }

    public function overdue(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Vaccination::class);

        return redirect()->route('vaccinations.index', ['status' => 'overdue']);
    }

    private function resolveNextDueDate(string $dateGiven, string $vaccineName): string
    {
        $normalized = strtolower($vaccineName);
        $baseDate = Carbon::parse($dateGiven);

        if (
            str_contains($normalized, 'rabies')
            || str_contains($normalized, '5-in-1')
            || str_contains($normalized, '5 in 1')
            || str_contains($normalized, '6-in-1')
            || str_contains($normalized, '6 in 1')
        ) {
            return $baseDate->copy()->addYear()->toDateString();
        }

        return $baseDate->copy()->addYear()->toDateString();
    }

    private function normalizeStatusFilter(?string $status): string
    {
        $normalized = strtolower((string) $status);
        $allowedFilters = ['all', 'due_soon', 'overdue'];

        return in_array($normalized, $allowedFilters, true) ? $normalized : 'all';
    }

    private function buildOwnerReminderMessage(Vaccination $vaccination): string
    {
        $petName = $vaccination->display_pet_name !== 'N/A'
            ? $vaccination->display_pet_name
            : 'your pet';
        $vaccineName = $vaccination->vaccine_name;
        $today = today();
        $isOverdue = $vaccination->next_due_date?->isBefore($today) ?? false;
        $isDueSoon = $vaccination->next_due_date
            ? (! $isOverdue && $vaccination->next_due_date->isBetween($today, $today->copy()->addDays(7)))
            : false;
        $duePhrase = $isOverdue
            ? 'is overdue for'
            : ($isDueSoon ? 'is due soon for' : 'is due for');

        return "Hello! This is a reminder from PE+ Infirmary Veterinary Clinic.\n\n"
            ."Your pet ({$petName}) {$duePhrase} its {$vaccineName} vaccination.\n\n"
            ."Please visit our clinic as soon as possible.\n\n"
            ."Clinic Location: Narvacan, Ilocos Sur\n"
            ."Emergency Hotline: 0956 348 1378";
    }

    private function sanitizeVaccinationPayload(array $payload): array
    {
        $payload['pet_name'] = trim((string) ($payload['pet_name'] ?? ''));
        $payload['owner_name'] = $this->normalizeOptionalText($payload['owner_name'] ?? null);
        $payload['contact_number'] = $this->normalizeOptionalText($payload['contact_number'] ?? null);

        return $payload;
    }

    private function normalizeOptionalText(?string $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}
