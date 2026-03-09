<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalRecordStoreRequest;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;

class MedicalRecordController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function store(MedicalRecordStoreRequest $request): RedirectResponse
    {
        $pet = Pet::query()->findOrFail($request->validated('pet_id'));
        $this->authorize('update', $pet);

        $record = MedicalRecord::create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        $this->auditLogService->log(
            $request->user(),
            'updated',
            'medical_record',
            $record->id,
            sprintf('Saved consultation record for %s.', $pet->pet_name)
        );

        return back()->with('success', 'Medical record saved successfully.');
    }
}
