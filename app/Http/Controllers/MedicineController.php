<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        if (! $this->tableExists()) {
            $medicines = new LengthAwarePaginator(
                [],
                0,
                12,
                $request->integer('page', 1),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('medicines.index', [
                'medicines' => $medicines,
                'lowStockCount' => 0,
                'expiredCount' => 0,
                'schemaReady' => false,
            ]);
        }

        $medicines = Medicine::query()
            ->orderBy('name')
            ->paginate(12);

        $lowStockCount = Medicine::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
        $expiredCount = Medicine::query()
            ->whereDate('expiration_date', '<', now()->toDateString())
            ->count();

        return view('medicines.index', [
            'medicines' => $medicines,
            'lowStockCount' => $lowStockCount,
            'expiredCount' => $expiredCount,
            'schemaReady' => true,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (! $this->tableExists()) {
            return redirect()
                ->route('medicines.index')
                ->with('error', 'Medicines module is not ready yet. Run `php artisan migrate` first.');
        }

        return view('medicines.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $this->tableExists()) {
            return redirect()
                ->route('medicines.index')
                ->with('error', 'Medicines module is not ready yet. Run `php artisan migrate` first.');
        }

        $medicine = Medicine::query()->create($this->validatedPayload($request));
        $this->auditLogService->log(
            $request->user(),
            'created',
            'medicine',
            $medicine->id,
            sprintf('Created medicine inventory record for %s.', $medicine->name)
        );

        return redirect()
            ->route('medicines.edit', $medicine)
            ->with('success', 'Medicine added successfully.');
    }

    public function edit(Medicine $medicine): View
    {
        return view('medicines.edit', compact('medicine'));
    }

    public function show(Medicine $medicine): View
    {
        return view('medicines.show', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse
    {
        $medicine->update($this->validatedPayload($request));
        $this->auditLogService->log(
            $request->user(),
            'updated',
            'medicine',
            $medicine->id,
            sprintf('Updated medicine inventory record for %s.', $medicine->name)
        );

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Request $request, Medicine $medicine): RedirectResponse
    {
        $medicineId = $medicine->id;
        $medicineName = $medicine->name;
        $medicine->delete();

        $this->auditLogService->log(
            $request->user(),
            'deleted',
            'medicine',
            $medicineId,
            sprintf('Deleted medicine inventory record for %s.', $medicineName)
        );

        return redirect()
            ->route('medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    private function validatedPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function tableExists(): bool
    {
        return Schema::hasTable('medicines');
    }
}
