<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (! Schema::hasTable('audit_logs')) {
            return view('audit-logs.index', [
                'auditLogs' => collect(),
                'eventTypes' => collect(),
                'entityTypes' => collect(),
            ]);
        }

        $query = AuditLog::query()
            ->with('user:id,name')
            ->latest('created_at');

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->string('event_type'));
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->string('entity_type'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search): void {
                $builder->where('description', 'like', '%'.$search.'%')
                    ->orWhere('entity_type', 'like', '%'.$search.'%')
                    ->orWhere('event_type', 'like', '%'.$search.'%');
            });
        }

        $auditLogs = $query->paginate(20)->withQueryString();

        return view('audit-logs.index', [
            'auditLogs' => $auditLogs,
            'eventTypes' => AuditLog::query()->select('event_type')->distinct()->orderBy('event_type')->pluck('event_type'),
            'entityTypes' => AuditLog::query()->select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type'),
        ]);
    }
}
