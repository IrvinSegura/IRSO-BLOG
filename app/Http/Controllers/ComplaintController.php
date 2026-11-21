<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * File a reader's report about a publication. Each reader reports a publication once.
     */
    public function store(Request $request, Publication $publication): RedirectResponse
    {
        if ($publication->user_id === $request->user()->id) {
            return back()->with('failed', 'No puedes reportar tu propia publicación.');
        }

        $data = $request->validate([
            'reason' => ['required', Rule::in(array_keys(Complaint::REASONS))],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'reason.required' => 'Elige un motivo para el reporte.',
            'reason.in' => 'Elige uno de los motivos de la lista.',
            'description.max' => 'Los detalles no pueden tener más de 255 caracteres.',
        ]);

        $complaint = Complaint::firstOrCreate(
            ['user_id' => $request->user()->id, 'publication_id' => $publication->id],
            [
                'reason' => $data['reason'],
                'description' => trim((string) ($data['description'] ?? '')),
                'status' => Complaint::STATUS_PENDING,
            ],
        );

        return back()->with('success', $complaint->wasRecentlyCreated
            ? 'Gracias, el equipo revisará esta publicación.'
            : 'Ya habías reportado esta publicación.');
    }

    /**
     * Moderation queue for administrators and editors.
     */
    public function index(Request $request): View
    {
        $status = $request->string('estado')->toString();
        $status = in_array($status, [Complaint::STATUS_RESOLVED, Complaint::STATUS_DISMISSED], true)
            ? $status
            : Complaint::STATUS_PENDING;

        $complaints = Complaint::with(['user', 'resolver', 'publication' => fn ($query) => $query->with('user')->withCount('complaints')])
            ->whereNotNull('publication_id')
            ->where('status', $status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Complaint::whereNotNull('publication_id')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.reports.index', compact('complaints', 'status', 'counts'));
    }

    /**
     * Close a report: dismiss it, mark it handled, or take the publication down. Taking it
     * down closes every pending report about the same publication.
     */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $action = $request->validate([
            'action' => ['required', Rule::in(['dismiss', 'resolve', 'remove'])],
        ])['action'];

        $closed = [
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ];

        if ($action === 'remove') {
            DB::transaction(function () use ($complaint, $closed) {
                $complaint->publication?->delete();
                Complaint::where('publication_id', $complaint->publication_id)
                    ->where('status', Complaint::STATUS_PENDING)
                    ->update([...$closed, 'status' => Complaint::STATUS_RESOLVED]);
            });

            return back()->with('success', 'Publicación retirada del blog. Puedes restaurarla desde los reportes resueltos.');
        }

        $complaint->update([
            ...$closed,
            'status' => $action === 'dismiss' ? Complaint::STATUS_DISMISSED : Complaint::STATUS_RESOLVED,
        ]);

        return back()->with('success', $action === 'dismiss' ? 'Reporte descartado.' : 'Reporte marcado como resuelto.');
    }

    /**
     * Put back a publication that was taken down from a report.
     */
    public function restorePublication(Complaint $complaint): RedirectResponse
    {
        $publication = $complaint->publication;

        if ($publication?->trashed()) {
            $publication->restore();
        }

        return back()->with('success', 'Publicación restaurada en el blog.');
    }
}
