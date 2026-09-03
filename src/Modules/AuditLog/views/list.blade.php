<div class="cb-audit-page space-y-5"
     x-data="cbAuditLogCoachmark(window.cbAuditLogCoachmarkConfig || {})"
     x-on:resize.window="queueUpdatePosition()"
     x-on:scroll.window="queueUpdatePosition()"
     x-on:keydown.escape.window="dismissCoachmark()"
     x-on:cb-download-csv.window="
        const blob = new Blob([$event.detail.content], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', $event.detail.filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
     ">
    <style>
        [x-cloak] { display: none !important; }
        .cb-audit-page .cb-audit-frame {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
        }
        .cb-audit-page .cb-audit-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .cb-audit-page .cb-audit-subtitle {
            margin-top: 6px;
            font-size: 14px;
            color: #64748b;
        }
        .cb-audit-page .cb-audit-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }
        .cb-audit-page .cb-audit-grid .wide { grid-column: span 2; }
        .cb-audit-page .cb-audit-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }
        .cb-audit-page .cb-audit-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            height: 38px;
            padding: 0 10px;
            font-size: 14px;
            color: #334155;
            background: #fff;
        }
        .cb-audit-page .cb-audit-actions {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .cb-audit-page .cb-audit-btn {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            border-radius: 8px;
            padding: 7px 12px;
        }
        .cb-audit-page .cb-audit-table-wrap {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .cb-audit-page table {
            width: 100%;
            border-collapse: collapse;
        }
        .cb-audit-page thead th {
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: #64748b;
            text-transform: uppercase;
            padding: 11px 12px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .cb-audit-page tbody td {
            padding: 12px;
            border-top: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
            vertical-align: top;
        }
        .cb-audit-page .cb-chip {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .cb-audit-page .cb-chip-success { color: #065f46; background: #d1fae5; }
        .cb-audit-page .cb-chip-failed { color: #991b1b; background: #fee2e2; }
        .cb-audit-page .cb-chip-blocked { color: #92400e; background: #fef3c7; }
        .cb-audit-page .cb-audit-footer {
            padding: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }
        .cb-audit-page .cb-muted {
            color: #64748b;
            font-size: 13px;
        }
        .cb-audit-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.62);
            z-index: 10050;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow-y: auto;
            padding: 36px 16px;
        }
        .cb-audit-modal {
            width: min(980px, 96vw);
            background: #fff;
            border: 1px solid #dbe7f7;
            border-radius: 12px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.2);
            padding: 18px;
        }
        .cb-audit-json {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 12px;
            max-height: 280px;
            overflow: auto;
            padding: 12px;
            margin-top: 6px;
        }
        .cb-audit-section {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            background: #fff;
        }
        .cb-audit-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .cb-audit-page .cb-coach-spotlight {
            position: fixed;
            border-radius: 10px;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.74);
            border: 2px solid #60a5fa;
            z-index: 10010;
            pointer-events: none;
        }
        .cb-audit-page .cb-coach-card {
            position: fixed;
            width: 350px;
            max-width: calc(100vw - 24px);
            background: #fff;
            border: 1px solid #dbe7f7;
            border-radius: 12px;
            box-shadow: 0 16px 44px rgba(15, 23, 42, 0.28);
            padding: 14px;
            z-index: 10011;
        }
        .cb-audit-page .cb-coach-step {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }
        .cb-audit-page .cb-coach-title {
            margin-top: 4px;
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
        }
        .cb-audit-page .cb-coach-desc {
            margin-top: 8px;
            font-size: 14px;
            line-height: 1.45;
            color: #475569;
        }
        .cb-audit-page .cb-coach-actions {
            margin-top: 14px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
        }
        .cb-audit-page .cb-coach-btn {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            border-radius: 8px;
            padding: 8px 12px;
            line-height: 1.1;
        }
        .cb-audit-page .cb-coach-btn-primary {
            border-color: #3b82f6;
            background: #3b82f6;
            color: #fff;
        }
        @media (max-width: 1280px) {
            .cb-audit-page .cb-audit-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .cb-audit-page .cb-audit-grid .wide { grid-column: span 3; }
        }
        @media (max-width: 768px) {
            .cb-audit-page .cb-audit-grid { grid-template-columns: 1fr; }
            .cb-audit-page .cb-audit-grid .wide { grid-column: span 1; }
        }
    </style>

    <div class="cb-audit-frame">
        <div class="cb-audit-title" data-coach-target="audit-title">{{ __('cb::audit_log.title') }}</div>
        <div class="cb-audit-subtitle">{{ __('cb::audit_log.subtitle') }}</div>

        <div class="cb-audit-grid">
            <div data-coach-target="audit-filter-user">
                <label class="cb-audit-label">{{ __('cb::audit_log.filters.user') }}</label>
                <input type="text" wire:model.live.debounce.300ms="filterUser" class="cb-audit-input" placeholder="{{ __('cb::audit_log.filters.user_placeholder') }}">
            </div>
            <div data-coach-target="audit-filter-action">
                <label class="cb-audit-label">{{ __('cb::audit_log.filters.action') }}</label>
                <select wire:model.live="filterAction" class="cb-audit-input">
                    <option value="">{{ __('cb::audit_log.filters.all') }}</option>
                    @foreach($actionOptions as $action)
                        <option value="{{ $action }}">{{ strtoupper($action) }}</option>
                    @endforeach
                </select>
            </div>
            <div data-coach-target="audit-filter-module">
                <label class="cb-audit-label">{{ __('cb::audit_log.filters.module') }}</label>
                <select wire:model.live="filterModule" class="cb-audit-input">
                    <option value="">{{ __('cb::audit_log.filters.all') }}</option>
                    @foreach($moduleOptions as $moduleOption)
                        <option value="{{ $moduleOption }}">{{ $moduleOption }}</option>
                    @endforeach
                </select>
            </div>
            <div data-coach-target="audit-filter-outcome">
                <label class="cb-audit-label">{{ __('cb::audit_log.filters.outcome') }}</label>
                <select wire:model.live="filterOutcome" class="cb-audit-input">
                    <option value="">{{ __('cb::audit_log.filters.all') }}</option>
                    <option value="success">{{ __('cb::audit_log.outcome.success') }}</option>
                    <option value="failed">{{ __('cb::audit_log.outcome.failed') }}</option>
                    <option value="blocked">{{ __('cb::audit_log.outcome.blocked') }}</option>
                </select>
            </div>
            <div class="wide" data-coach-target="audit-filter-path">
                <label class="cb-audit-label">{{ __('cb::audit_log.filters.path') }}</label>
                <input type="text" wire:model.live.debounce.300ms="filterPath" class="cb-audit-input" placeholder="{{ __('cb::audit_log.filters.path_placeholder') }}">
            </div>
            <div>
                <label class="cb-audit-label">{{ __('cb::audit_log.filters.date_from') }}</label>
                <input type="date" wire:model.live="filterDateFrom" class="cb-audit-input">
            </div>
            <div>
                <label class="cb-audit-label">{{ __('cb::audit_log.filters.date_to') }}</label>
                <input type="date" wire:model.live="filterDateTo" class="cb-audit-input">
            </div>
        </div>

        <div class="cb-audit-actions" data-coach-target="audit-actions">
            <button type="button" wire:click="$refresh" class="cb-audit-btn">{{ __('cb::audit_log.actions.refresh') }}</button>
            <button type="button" wire:click="exportCsv" class="cb-audit-btn">{{ __('cb::audit_log.actions.export_csv') }}</button>
            <button type="button" wire:click="clearFilters" class="cb-audit-btn">{{ __('cb::audit_log.actions.clear_filters') }}</button>
        </div>
    </div>

    <div class="cb-audit-table-wrap" data-coach-target="audit-table">
        <table>
            <thead>
                <tr>
                    <th>{{ __('cb::audit_log.table.time') }}</th>
                    <th>{{ __('cb::audit_log.table.user') }}</th>
                    <th>{{ __('cb::audit_log.table.action') }}</th>
                    <th>{{ __('cb::audit_log.table.module') }}</th>
                    <th>{{ __('cb::audit_log.table.path') }}</th>
                    <th>{{ __('cb::audit_log.table.outcome') }}</th>
                    <th>{{ __('cb::audit_log.table.ip') }}</th>
                    <th>{{ __('cb::audit_log.table.details') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <div class="font-semibold">{{ $log->user_name ?: '-' }}</div>
                            <div class="cb-muted">{{ $log->user_email ?: '-' }}</div>
                        </td>
                        <td class="font-semibold">{{ strtoupper($log->action) }}</td>
                        <td>{{ $log->module_key ?: '-' }}</td>
                        <td class="font-mono text-xs">{{ $log->path ?: '-' }}</td>
                        <td>
                            <span class="cb-chip cb-chip-{{ $log->outcome }}">
                                {{ __('cb::audit_log.outcome.' . $log->outcome) }}
                            </span>
                        </td>
                        <td>{{ $log->ip_address ?: '-' }}</td>
                        <td>
                            <button type="button"
                                    wire:click="openDetail('{{ $log->id }}')"
                                    class="cb-audit-btn"
                                    @if($loop->first) data-coach-target="audit-row-view" @endif>
                                {{ __('cb::audit_log.actions.view') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center cb-muted py-8">{{ __('cb::audit_log.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="cb-audit-footer">
            <div class="cb-muted">
                {{ __('cb::audit_log.showing', ['from' => $logs->firstItem() ?? 0, 'to' => $logs->lastItem() ?? 0, 'total' => $logs->total()]) }}
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="previousPage" class="cb-audit-btn" @disabled($logs->onFirstPage())>{{ __('cb::audit_log.actions.previous') }}</button>
                <button type="button" wire:click="nextPage" class="cb-audit-btn" @disabled(!$logs->hasMorePages())>{{ __('cb::audit_log.actions.next') }}</button>
            </div>
        </div>
    </div>

    @if($showDetailModal)
        <div class="cb-audit-modal-overlay" wire:click.self="closeDetail">
            <div class="cb-audit-modal" data-coach-target="audit-detail-modal">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xl font-bold text-slate-900">{{ __('cb::audit_log.detail.title') }}</div>
                        <div class="cb-muted mt-1">{{ __('cb::audit_log.detail.labels.log_id') }}: {{ $selectedLog['id'] ?? '-' }}</div>
                    </div>
                    <button type="button" wire:click="closeDetail" class="cb-audit-btn">{{ __('cb::audit_log.actions.close') }}</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-4">
                    <div class="cb-audit-section">
                        <div class="cb-audit-section-title">{{ __('cb::audit_log.detail.actor') }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.table.user') }}:</strong> {{ $selectedLog['user_name'] ?? '-' }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.detail.labels.email') }}:</strong> {{ $selectedLog['user_email'] ?? '-' }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.detail.labels.id') }}:</strong> {{ $selectedLog['user_id'] ?? '-' }}</div>
                    </div>
                    <div class="cb-audit-section">
                        <div class="cb-audit-section-title">{{ __('cb::audit_log.detail.request') }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.detail.labels.method') }}:</strong> {{ $selectedLog['http_method'] ?? '-' }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.table.path') }}:</strong> <span class="font-mono text-xs">{{ $selectedLog['path'] ?? '-' }}</span></div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.detail.labels.ip') }}:</strong> {{ $selectedLog['ip_address'] ?? '-' }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.detail.labels.request_id') }}:</strong> <span class="font-mono text-xs">{{ $selectedLog['request_id'] ?? '-' }}</span></div>
                    </div>
                    <div class="cb-audit-section">
                        <div class="cb-audit-section-title">{{ __('cb::audit_log.detail.context') }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.table.action') }}:</strong> {{ strtoupper($selectedLog['action'] ?? '-') }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.table.module') }}:</strong> {{ $selectedLog['module_key'] ?? '-' }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.detail.labels.entity') }}:</strong> {{ $selectedLog['entity_type'] ?? '-' }}</div>
                        <div class="text-sm"><strong>{{ __('cb::audit_log.detail.labels.entity_id') }}:</strong> {{ $selectedLog['entity_id'] ?? '-' }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3" data-coach-target="audit-detail-changed-fields">
                    <div class="cb-audit-section">
                        <div class="cb-audit-section-title">{{ __('cb::audit_log.detail.changed_fields') }}</div>
                        <div class="cb-audit-json">{{ $this->prettyJson($selectedLog['changed_fields'] ?? []) }}</div>
                    </div>
                    <div class="cb-audit-section">
                        <div class="cb-audit-section-title">{{ __('cb::audit_log.detail.request_payload') }}</div>
                        <div class="cb-audit-json">{{ $this->prettyJson($selectedLog['request_payload'] ?? []) }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3" data-coach-target="audit-detail-before-after">
                    <div class="cb-audit-section">
                        <div class="cb-audit-section-title">{{ __('cb::audit_log.detail.before') }}</div>
                        <div class="cb-audit-json">{{ $this->prettyJson($selectedLog['before_data'] ?? []) }}</div>
                    </div>
                    <div class="cb-audit-section">
                        <div class="cb-audit-section-title">{{ __('cb::audit_log.detail.after') }}</div>
                        <div class="cb-audit-json">{{ $this->prettyJson($selectedLog['after_data'] ?? []) }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <template x-if="showCoachmark && coachReady">
        <div>
            <div class="cb-coach-spotlight"
                 x-cloak
                 :style="`top:${coachTarget.top - 6}px;left:${coachTarget.left - 6}px;width:${coachTarget.width + 12}px;height:${coachTarget.height + 12}px;`"></div>

            <div class="cb-coach-card"
                 x-cloak
                 :style="coachCardStyle()">
                <div class="cb-coach-step" x-text="`${labels.step} ${coachIndex + 1} / ${steps.length}`"></div>
                <div class="cb-coach-title" x-text="currentCoach.title"></div>
                <div class="cb-coach-desc" x-text="currentCoach.desc"></div>
                <div class="cb-coach-actions">
                    <button type="button" class="cb-coach-btn" x-on:click="dismissCoachmark()" x-text="labels.dismiss"></button>
                    <button type="button" class="cb-coach-btn" x-show="coachIndex > 0" x-on:click="prevCoachStep()" x-text="labels.back"></button>
                    <button type="button" class="cb-coach-btn cb-coach-btn-primary" x-on:click="nextCoachStep()" x-text="isLastCoachStep ? labels.finish : labels.next"></button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    window.cbAuditLogCoachmarkConfig = {!! json_encode([
        'firstLogId' => $logs->first()?->id,
        'localStorageKey' => 'cb_audit_log_coachmark_v2',
        'steps' => [
            ['target' => '[data-coach-target="audit-title"]', 'title' => __('cb::audit_log.coachmark.step_1_title'), 'desc' => __('cb::audit_log.coachmark.step_1_desc')],
            ['target' => '[data-coach-target="audit-filter-user"]', 'title' => __('cb::audit_log.coachmark.step_2_title'), 'desc' => __('cb::audit_log.coachmark.step_2_desc')],
            ['target' => '[data-coach-target="audit-filter-action"]', 'title' => __('cb::audit_log.coachmark.step_3_title'), 'desc' => __('cb::audit_log.coachmark.step_3_desc')],
            ['target' => '[data-coach-target="audit-filter-module"]', 'title' => __('cb::audit_log.coachmark.step_4_title'), 'desc' => __('cb::audit_log.coachmark.step_4_desc')],
            ['target' => '[data-coach-target="audit-filter-outcome"]', 'title' => __('cb::audit_log.coachmark.step_5_title'), 'desc' => __('cb::audit_log.coachmark.step_5_desc')],
            ['target' => '[data-coach-target="audit-filter-path"]', 'title' => __('cb::audit_log.coachmark.step_6_title'), 'desc' => __('cb::audit_log.coachmark.step_6_desc')],
            ['target' => '[data-coach-target="audit-actions"]', 'title' => __('cb::audit_log.coachmark.step_7_title'), 'desc' => __('cb::audit_log.coachmark.step_7_desc')],
            ['target' => '[data-coach-target="audit-table"]', 'title' => __('cb::audit_log.coachmark.step_8_title'), 'desc' => __('cb::audit_log.coachmark.step_8_desc')],
            ['target' => '[data-coach-target="audit-row-view"]', 'title' => __('cb::audit_log.coachmark.step_9_title'), 'desc' => __('cb::audit_log.coachmark.step_9_desc'), 'action' => 'open-detail'],
            ['target' => '[data-coach-target="audit-detail-modal"]', 'title' => __('cb::audit_log.coachmark.step_10_title'), 'desc' => __('cb::audit_log.coachmark.step_10_desc')],
            ['target' => '[data-coach-target="audit-detail-changed-fields"]', 'title' => __('cb::audit_log.coachmark.step_11_title'), 'desc' => __('cb::audit_log.coachmark.step_11_desc')],
            ['target' => '[data-coach-target="audit-detail-before-after"]', 'title' => __('cb::audit_log.coachmark.step_12_title'), 'desc' => __('cb::audit_log.coachmark.step_12_desc'), 'action' => 'close-detail'],
        ],
        'labels' => [
            'dismiss' => __('cb::audit_log.coachmark.dismiss'),
            'back' => __('cb::audit_log.coachmark.back'),
            'next' => __('cb::audit_log.coachmark.next'),
            'finish' => __('cb::audit_log.coachmark.finish'),
            'step' => __('cb::audit_log.coachmark.step'),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

    function cbAuditLogCoachmark(config) {
        return {
            firstLogId: config.firstLogId || null,
            localStorageKey: config.localStorageKey,
            steps: config.steps || [],
            labels: config.labels || {},
            coachIndex: 0,
            showCoachmark: false,
            coachReady: false,
            coachTarget: { top: 0, left: 0, width: 0, height: 0 },
            updateTimer: null,
            init() {
                if (!this.steps.length || this.isCoachmarkDismissed()) return;
                this.showCoachmark = true;
                this.$nextTick(() => this.waitAndUpdatePosition());
            },
            get currentCoach() {
                return this.steps[this.coachIndex] || this.steps[0] || {};
            },
            get isLastCoachStep() {
                return this.coachIndex >= (this.steps.length - 1);
            },
            isCoachmarkDismissed() {
                try {
                    return !!window.localStorage.getItem(this.localStorageKey);
                } catch (e) {
                    return true;
                }
            },
            queueUpdatePosition() {
                if (!this.showCoachmark) return;
                if (this.updateTimer) clearTimeout(this.updateTimer);
                this.updateTimer = setTimeout(() => this.waitAndUpdatePosition(), 80);
            },
            waitAndUpdatePosition(retries = 12) {
                this.updatePosition();
                if (this.coachReady) return;
                if (retries <= 0) {
                    this.moveToAvailableStep(1);
                    return;
                }
                setTimeout(() => this.waitAndUpdatePosition(retries - 1), 120);
            },
            updatePosition() {
                if (!this.showCoachmark) return;
                const selector = this.currentCoach.target;
                if (!selector) {
                    this.coachReady = false;
                    return;
                }
                const target = this.resolveTarget(selector);
                if (!target) {
                    this.coachReady = false;
                    return;
                }
                const rect = target.getBoundingClientRect();
                this.coachTarget = { top: rect.top, left: rect.left, width: rect.width, height: rect.height };
                this.coachReady = true;
            },
            coachCardStyle() {
                const cardWidth = Math.min(350, (window.innerWidth || 1280) - 24);
                const margin = 12;
                const viewportHeight = window.innerHeight || 720;
                const viewportWidth = window.innerWidth || 1280;
                let left = this.coachTarget.left + this.coachTarget.width - cardWidth;
                left = Math.max(margin, Math.min(left, viewportWidth - cardWidth - margin));
                let top = this.coachTarget.top + this.coachTarget.height + 14;
                const estimatedHeight = 190;
                if (top + estimatedHeight > viewportHeight - margin) {
                    top = this.coachTarget.top - estimatedHeight - 14;
                }
                top = Math.max(margin, top);
                return `top:${top}px;left:${left}px;`;
            },
            nextCoachStep() {
                const action = this.currentCoach.action || null;
                if (action === 'open-detail' && this.firstLogId) {
                    this.$wire.openDetail(this.firstLogId);
                    this.coachIndex++;
                    this.coachReady = false;
                    this.$nextTick(() => this.waitAndUpdatePosition());
                    return;
                }
                if (action === 'close-detail') {
                    this.$wire.closeDetail();
                }
                if (this.isLastCoachStep) {
                    this.dismissCoachmark();
                    return;
                }
                this.coachIndex++;
                this.coachReady = false;
                this.$nextTick(() => this.waitAndUpdatePosition());
            },
            prevCoachStep() {
                if (this.coachIndex <= 0) return;
                this.coachIndex--;
                this.coachReady = false;
                this.$nextTick(() => this.waitAndUpdatePosition());
            },
            moveToAvailableStep(direction = 1) {
                const total = this.steps.length;
                for (let i = 1; i < total; i++) {
                    const nextIndex = this.coachIndex + (i * direction);
                    if (nextIndex < 0 || nextIndex >= total) break;
                    const step = this.steps[nextIndex];
                    if (!step || !step.target) continue;
                    if (this.resolveTarget(step.target)) {
                        this.coachIndex = nextIndex;
                        this.coachReady = false;
                        this.$nextTick(() => this.waitAndUpdatePosition());
                        return;
                    }
                }
                this.dismissCoachmark();
            },
            resolveTarget(selector) {
                return document.querySelector(selector);
            },
            dismissCoachmark() {
                this.showCoachmark = false;
                this.coachReady = false;
                try {
                    window.localStorage.setItem(this.localStorageKey, '1');
                } catch (e) {}
            },
        };
    }
</script>
