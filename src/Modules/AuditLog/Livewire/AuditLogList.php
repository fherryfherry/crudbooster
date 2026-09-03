<?php

namespace CrudBooster\Modules\AuditLog\Livewire;

use CrudBooster\Modules\AuditLog\Models\CbAuditLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogList extends Component
{
    use WithPagination;

    public string $pageTitle = 'Audit Log';
    public int $perPage = 20;
    public ?string $filterUser = null;
    public ?string $filterAction = null;
    public ?string $filterModule = null;
    public ?string $filterOutcome = null;
    public ?string $filterPath = null;
    public ?string $filterDateFrom = null;
    public ?string $filterDateTo = null;
    public ?string $selectedLogId = null;
    public bool $showDetailModal = false;
    public array $selectedLog = [];

    public function mount(): void
    {
        if (! auth()->check() || ! Gate::allows('read', 'audit-log')) {
            abort(403);
        }
    }

    public function updating(
        string $name,
        mixed $value
    ): void {
        if (str_starts_with($name, 'filter')) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->filterUser = null;
        $this->filterAction = null;
        $this->filterModule = null;
        $this->filterOutcome = null;
        $this->filterPath = null;
        $this->filterDateFrom = null;
        $this->filterDateTo = null;
        $this->resetPage();
    }

    public function exportCsv(): void
    {
        $logs = $this->buildQuery()
            ->limit(5000)
            ->get([
                'created_at',
                'user_id',
                'user_name',
                'user_email',
                'module_key',
                'action',
                'outcome',
                'http_method',
                'path',
                'ip_address',
                'request_id',
                'entity_type',
                'entity_id',
            ]);

        $header = [
            'created_at',
            'user_id',
            'user_name',
            'user_email',
            'module_key',
            'action',
            'outcome',
            'http_method',
            'path',
            'ip_address',
            'request_id',
            'entity_type',
            'entity_id',
        ];

        $rows = [];
        $rows[] = $this->csvLine($header);
        foreach ($logs as $log) {
            $rows[] = $this->csvLine([
                $log->created_at?->format('Y-m-d H:i:s'),
                $log->user_id,
                $log->user_name,
                $log->user_email,
                $log->module_key,
                $log->action,
                $log->outcome,
                $log->http_method,
                $log->path,
                $log->ip_address,
                $log->request_id,
                $log->entity_type,
                $log->entity_id,
            ]);
        }

        $this->dispatch(
            'cb-download-csv',
            content: implode("\n", $rows) . "\n",
            filename: 'audit-log-' . now()->format('Ymd-His') . '.csv'
        );
    }

    public function openDetail(string $id): void
    {
        $log = CbAuditLog::query()->find($id);
        if (! $log) {
            return;
        }

        $this->selectedLogId = $id;
        $this->selectedLog = $log->toArray();
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedLogId = null;
        $this->selectedLog = [];
    }

    public function prettyJson(array|null $value): string
    {
        if (empty($value)) {
            return '{}';
        }

        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function render()
    {
        $logs = $this->buildQuery()->paginate($this->perPage);

        $moduleOptions = CbAuditLog::query()
            ->whereNotNull('module_key')
            ->distinct()
            ->orderBy('module_key')
            ->pluck('module_key');

        $actionOptions = CbAuditLog::query()
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('cb.audit-log::list', [
            'logs' => $logs,
            'moduleOptions' => $moduleOptions,
            'actionOptions' => $actionOptions,
        ])->layout('cb.themes::layout-app')->title(__('audit_log::audit_log.title'));
    }

    private function buildQuery()
    {
        $query = CbAuditLog::query()->latest('created_at');

        if ($this->filterUser) {
            $filterUser = trim($this->filterUser);
            $query->where(function ($builder) use ($filterUser) {
                $builder->where('user_email', 'like', '%' . $filterUser . '%')
                    ->orWhere('user_name', 'like', '%' . $filterUser . '%')
                    ->orWhere('user_id', $filterUser);
            });
        }

        if ($this->filterAction) {
            $query->where('action', $this->filterAction);
        }

        if ($this->filterModule) {
            $query->where('module_key', $this->filterModule);
        }

        if ($this->filterOutcome) {
            $query->where('outcome', $this->filterOutcome);
        }

        if ($this->filterPath) {
            $query->where('path', 'like', '%' . trim($this->filterPath) . '%');
        }

        if ($this->filterDateFrom) {
            $dateFrom = $this->safeParseDate($this->filterDateFrom)?->startOfDay();
            if ($dateFrom) {
                $query->where('created_at', '>=', $dateFrom);
            }
        }

        if ($this->filterDateTo) {
            $dateTo = $this->safeParseDate($this->filterDateTo)?->endOfDay();
            if ($dateTo) {
                $query->where('created_at', '<=', $dateTo);
            }
        }

        return $query;
    }

    private function safeParseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function csvLine(array $columns): string
    {
        $escaped = array_map(static function ($column): string {
            $value = (string) ($column ?? '');
            return '"' . str_replace('"', '""', $value) . '"';
        }, $columns);

        return implode(',', $escaped);
    }
}
