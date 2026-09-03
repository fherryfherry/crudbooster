<div class="cb-api-list-page space-y-6"
     x-data="cbApiBuilderCoachmark()"
     x-on:cb-new-api-modal-opened.window="
        if (!showQuickCoachmark || !awaitingStep2) return;
        setTimeout(() => {
            coachStep = 1;
            awaitingStep2 = false;
            coachReady = false;
            $nextTick(() => updateQuickCoachmarkPosition());
            setTimeout(() => waitAndUpdateCoachmarkPosition(), 140);
        }, 320);
     "
     x-on:cb-quick-api-generated.window="handleQuickGenerated()"
     x-on:cb-credential-tab-opened.window="queueCredentialJourney(320)"
     x-on:cb-logs-tab-opened.window="queueLogsJourney(320)"
     x-on:cb-copy-api-key.window="navigator.clipboard && navigator.clipboard.writeText($event.detail.value)"
     x-on:cb-download-csv.window="
        const blob = new Blob([$event.detail.content], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', $event.detail.filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
     "
    >
    @if(isset($confirmTitle))
        {!! confirmMessageTag($confirmTitle, $confirmMessage, $confirmAction, $confirmButtonText, $confirmButtonColor) !!}
    @endif
    <style>
        [x-cloak] { display: none !important; }

        .cb-api-list-page {
            --api-card: #ffffff;
            --api-line: #e2e8f0;
            --api-text: #1e293b;
            --api-muted: #64748b;
            --api-blue: #3b82f6;
            --api-green: #10b981;
            --api-orange: #f59e0b;
            --api-gray: #94a3b8;
            --api-red: #ef4444;
            color: var(--api-text);
            font-family: inherit;
        }

        .cb-api-frame {
            background: #f8fafc;
            border: 1px solid var(--api-line);
            border-radius: 16px;
            padding: 24px;
        }

        .cb-api-title {
            font-size: 1.875rem;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .cb-api-subtitle {
            margin-top: 6px;
            font-size: 1.05rem;
            line-height: 1.4;
            color: #475569;
        }

        .cb-tab-wrap {
            border-bottom: 1px solid var(--api-line);
            display: flex;
            gap: 32px;
            margin-bottom: 32px;
        }

        .cb-tab-btn {
            border: 0;
            background: transparent;
            color: #64748b;
            padding: 12px 2px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s;
            position: relative;
        }

        .cb-tab-btn:hover {
            color: #1e293b;
        }

        .cb-tab-btn.active {
            color: #2563eb;
        }

        .cb-tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: #2563eb;
            border-radius: 3px 3px 0 0;
        }

        .cb-table-frame {
            background: var(--api-card);
            border: 1px solid var(--api-line);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .cb-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .cb-table thead th {
            text-align: left;
            padding: 14px 16px;
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border-bottom: 1px solid var(--api-line);
        }

        .cb-table tbody td {
            padding: 16px;
            border-top: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .cb-api-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }

        .cb-api-meta {
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
        }

        .cb-endpoint-pill {
            display: inline-block;
            background: #eff6ff;
            color: #1e40af;
            border-radius: 8px;
            border: 1px solid #dbeafe;
            padding: 6px 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 13px;
            font-weight: 600;
        }

        .cb-method-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 64px;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .cb-method-get { background: #ecfdf5; color: #065f46; border: 1px solid #d1fae5; }
        .cb-method-post { background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; }
        .cb-method-put { background: #f5f3ff; color: #5b21b6; border: 1px solid #ede9fe; }
        .cb-method-delete { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }
        .cb-method-any { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        .cb-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .cb-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .cb-status-active .cb-status-dot { background: #10b981; box-shadow: 0 0 0 4px #d1fae5; }
        .cb-status-testing .cb-status-dot { background: #f59e0b; box-shadow: 0 0 0 4px #fef3c7; }
        .cb-status-disabled .cb-status-dot { background: #94a3b8; box-shadow: 0 0 0 4px #f1f5f9; }

        .cb-token-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .cb-token-status .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .cb-token-status-active .dot { background: #10b981; }
        .cb-token-status-expired .dot { background: #64748b; }
        .cb-token-status-disabled .dot { background: #ef4444; }

        .cb-row-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .cb-row-action-btn {
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #475569;
            font-weight: 600;
            font-size: 12px;
            border-radius: 8px;
            padding: 7px 10px;
            transition: all 0.2s;
            line-height: 1.2;
        }

        .cb-row-action-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .cb-row-action-btn.primary {
            color: #2563eb;
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .cb-row-action-btn.primary:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .cb-row-action-btn.danger {
            color: #dc2626;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .cb-row-action-btn.danger:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .cb-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px;
            border-top: 1px solid var(--api-line);
            background: #ffffff;
        }

        .cb-result-count {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .cb-sort-btn {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 16px;
            background: #fff;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            transition: all 0.2s;
        }

        .cb-sort-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .cb-sort-btn.active {
            border-color: #3b82f6;
            color: #ffffff;
            background: #3b82f6;
        }

        .cb-api-list-page .btn-primary {
            background: #3b82f6;
            color: #ffffff;
            border: 1px solid #3b82f6;
            padding: 10px 20px;
            font-weight: 700;
            border-radius: 10px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
            cursor: pointer;
        }

        .cb-api-list-page .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .cb-api-list-page .btn-outline {
            background: transparent;
            color: #3b82f6;
            border: 1px solid #3b82f6;
            padding: 10px 20px;
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cb-api-list-page .btn-outline:hover {
            background: #eff6ff;
            color: #2563eb;
            border-color: #2563eb;
        }

        .cb-coachmark-card {
            position: fixed;
            width: 320px;
            background: #ffffff;
            border: 1px solid #dbe7f7;
            border-radius: 10px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
            padding: 14px;
            z-index: 10002;
        }

        .cb-coachmark-card::before {
            content: "";
            position: absolute;
            top: -8px;
            right: 32px;
            width: 14px;
            height: 14px;
            background: #fff;
            border-top: 1px solid #dbe7f7;
            border-left: 1px solid #dbe7f7;
            transform: rotate(45deg);
        }

        .cb-coachmark-title {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
        }

        .cb-coachmark-desc {
            margin-top: 6px;
            font-size: 13px;
            line-height: 1.4;
            color: #475569;
        }

        .cb-coachmark-actions {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .cb-coachmark-dismiss {
            border: 1px solid #d6dfec;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            background: #fff;
            color: #415167;
        }

        .cb-coachmark-spotlight {
            position: fixed;
            border-radius: 10px;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.72);
            border: 2px solid #60a5fa;
            z-index: 10001;
            pointer-events: none;
        }

        .cb-list-stat-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .cb-list-stat-card {
            background: #ffffff;
            border: 1px solid var(--api-line);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .cb-list-stat-label {
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        .cb-list-stat-value {
            margin-top: 10px;
            font-size: 2.25rem;
            line-height: 1;
            font-weight: 800;
            color: #0f172a;
        }

        .cb-list-stat-note {
            margin-top: 10px;
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        .cb-security-top {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
            gap: 12px;
        }

        .cb-token-panel {
            background: #fff;
            border: 1px solid var(--api-line);
            border-radius: 10px;
            padding: 12px;
        }

        .cb-token-kicker {
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.1em;
            color: #0f56a6;
            background: #edf3fe;
            border-radius: 6px;
            padding: 4px 8px;
            text-transform: uppercase;
        }

        .cb-token-title {
            margin-top: 14px;
            font-size: 1.55rem;
            font-weight: 800;
            color: #16202f;
        }

        .cb-token-desc {
            margin-top: 8px;
            color: #4f5e73;
            font-size: 13px;
            line-height: 1.35;
            max-width: 620px;
        }

        .cb-token-insight-card {
            background: #fff;
            border: 1px solid var(--api-line);
            border-radius: 10px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .cb-token-insight-title {
            font-size: 15px;
            font-weight: 700;
            color: #1f2a39;
        }

        .cb-token-insight-box {
            border: 1px solid #e6ecf5;
            border-radius: 8px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fbfdff;
        }

        .cb-token-name {
            font-size: 15px;
            font-weight: 700;
            color: #1f2939;
        }

        .cb-token-token {
            margin-top: 8px;
            border: 1px dashed #bfdbfe;
            border-radius: 8px;
            background: #eff6ff;
            color: #1e40af;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 13px;
            padding: 8px 12px;
            display: inline-block;
            word-break: break-all;
        }

        .cb-token-footer-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 10px;
        }

        .cb-page-btn {
            border: 1px solid #d6dfec;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            background: #fff;
            color: #415167;
        }

        .cb-page-btn[disabled] {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .cb-api-key-cell {
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            color: #1e4f99;
            background: #f6f9ff;
            border: 1px dashed #bfd0ea;
            border-radius: 8px;
            padding: 6px 8px;
            display: inline-block;
            vertical-align: middle;
        }

        .cb-copy-btn {
            border: 1px solid #d6dfec;
            border-radius: 8px;
            height: 32px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            background: #fff;
            margin-left: 8px;
        }

        .cb-token-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cb-token-action-btn {
            border: 1px solid #d6dfec;
            border-radius: 8px;
            height: 32px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 600;
            background: #fff;
            color: #415167;
        }

        .cb-token-action-btn.danger {
            border-color: #f2c4c4;
            color: #b53131;
            background: #fff7f7;
        }

        .cb-logs-placeholder {
            background: #fff;
            border: 1px solid var(--api-line);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .cb-log-metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .cb-log-metric-card {
            background: #fff;
            border: 1px solid #e4eaf3;
            border-radius: 10px;
            padding: 12px;
        }

        .cb-log-metric-label {
            color: #475467;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .cb-log-metric-value {
            margin-top: 8px;
            font-size: 1.65rem;
            line-height: 1.1;
            font-weight: 800;
            color: #101828;
        }

        .cb-log-metric-note {
            margin-top: 4px;
            font-size: 12px;
            color: #16a34a;
            font-weight: 600;
        }

        .cb-log-metric-highlight {
            background: linear-gradient(180deg, #0f5fb5 0%, #0b4f99 100%);
            border-color: #0d56a8;
            color: #fff;
        }

        .cb-log-metric-highlight .cb-log-metric-label,
        .cb-log-metric-highlight .cb-log-metric-value,
        .cb-log-metric-highlight .cb-log-metric-note {
            color: #fff;
        }

        .cb-log-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(290px, 1fr);
            gap: 10px;
        }

        .cb-log-panel {
            background: #fff;
            border: 1px solid #e4eaf3;
            border-radius: 10px;
            padding: 10px;
        }

        .cb-log-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }

        .cb-log-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
        }

        .cb-log-header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cb-log-btn-ghost {
            border: 1px solid #d9e2ef;
            border-radius: 8px;
            background: #fff;
            color: #475467;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 10px;
        }

        .cb-log-method {
            border-radius: 6px;
            padding: 3px 7px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cb-log-method.GET { background: #ecfdf3; color: #1f7a3f; }
        .cb-log-method.POST { background: #e7f0ff; color: #1d5fbe; }
        .cb-log-method.PUT { background: #efe6ff; color: #6c3abf; }
        .cb-log-method.PATCH { background: #fff6e8; color: #b36a15; }
        .cb-log-method.DELETE { background: #ffe8e8; color: #b53333; }

        .cb-log-status {
            font-size: 13px;
            font-weight: 700;
        }

        .cb-log-status-ok { color: #16a34a; }
        .cb-log-status-warn { color: #c2410c; }
        .cb-log-status-error { color: #b91c1c; }

        .cb-load-more {
            border: 0;
            background: transparent;
            color: #0f5fb5;
            font-size: 13px;
            font-weight: 700;
            margin-top: 6px;
        }

        .cb-error-item {
            margin-bottom: 10px;
        }

        .cb-error-bar-wrap {
            width: 100%;
            background: #eef2f7;
            border-radius: 999px;
            overflow: hidden;
            height: 10px;
            margin-top: 6px;
        }

        .cb-error-bar {
            height: 10px;
            border-radius: 999px;
            background: #c21f1f;
        }

        .cb-error-note {
            margin-top: 10px;
            border-top: 1px solid #e5e9f0;
            padding-top: 10px;
            font-size: 13px;
            color: #4b5565;
        }

        .cb-token-modal-overlay {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            margin: 0 !important;
            background: rgba(15, 23, 42, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            z-index: 9999;
        }

        .cb-token-modal {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border: 1px solid var(--api-line);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.18);
        }
        .cb-test-api-modal {
            max-width: 1200px;
            width: 95vw;
            position: relative;
        }

        .cb-api-mode-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .cb-api-mode-card {
            border: 2px solid #f1f5f9;
            transition: all 0.2s ease;
            cursor: pointer;
            background: #fbfcfe;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .cb-api-mode-card:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .cb-api-mode-card.selected {
            border-color: #3b82f6;
            background: #eff6ff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .cb-api-mode-card.selected .font-bold {
            color: #1e40af;
        }

        .cb-table-select select {
            border: 1px solid #d1dbe8;
            border-radius: 10px;
            height: 46px;
            padding: 0 12px;
            font-weight: 500;
            background-color: #fff;
            width: 100%;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        .cb-table-select select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .cb-snippet-modal {
            max-width: 800px;
        }

        .cb-token-modal-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }

        .cb-token-modal-cancel {
            border: 1px solid #d6dfec;
            border-radius: 10px;
            height: 44px;
            min-width: 96px;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 600;
            color: #415167;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cb-token-modal-submit {
            height: 44px;
            min-width: 172px;
            padding: 0 20px;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cb-token-modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1f2937;
        }

        .cb-token-modal-subtitle {
            margin-top: 4px;
            font-size: 13px;
            color: #64748b;
        }

        .cb-form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .cb-form-input,
        .cb-form-select {
            width: 100%;
            border: 1px solid #d7e1ef;
            border-radius: 8px;
            padding: 9px 10px;
            font-size: 14px;
            color: #1f2937;
            background: #fff;
        }

        .cb-form-input:focus,
        .cb-form-select:focus {
            outline: none;
            border-color: #9db7df;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        @media (max-width: 1300px) {
            .cb-list-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .cb-security-top { grid-template-columns: 1fr; }
            .cb-log-metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .cb-log-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 860px) {
            .cb-list-stat-grid { grid-template-columns: 1fr; }
            .cb-api-title { font-size: 1.6rem; }
            .cb-api-subtitle { font-size: 1rem; }
            .cb-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .cb-table-frame { overflow-x: auto; }
            .cb-table { min-width: 860px; }
            .cb-log-metric-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="cb-api-frame space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <h1 class="cb-api-title">{{ __('api_builder::api_builder.list.title') }}</h1>
                <p class="cb-api-subtitle">{{ __('api_builder::api_builder.list.subtitle') }}</p>
            </div>
            <div class="relative flex items-center gap-3">
                @if($activeTab === 'credential')
                    <button type="button" class="btn btn-primary inline-flex items-center gap-2" wire:click="generateToken">
                        <span>+</span>
                        <span>{{ __('api_builder::api_builder.actions.generate_new_token') }}</span>
                    </button>
                @else
                    <a href="{{ route('cb.api.swagger') }}" target="_blank" class="btn btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>{{ __('api_builder::api_builder.actions.swagger_apidocs') }}</span>
                    </a>
                    <button type="button" class="btn btn-primary inline-flex items-center gap-2" wire:click="openNewApiModal" x-ref="generateApiButton" data-coach-target="generate-api-button">
                        <span>+</span>
                        <span>{{ __('api_builder::api_builder.actions.generate_new_api') }}</span>
                    </button>
                @endif
            </div>
        </div>

        <template x-if="showQuickCoachmark && coachReady">
            <div>
                <div class="cb-coachmark-spotlight"
                     :style="`top:${coachTarget.top - 4}px;left:${coachTarget.left - 4}px;width:${coachTarget.width + 8}px;height:${coachTarget.height + 8}px;`"></div>
                <div class="cb-coachmark-card"
                     :style="coachCardStyle()">
                    <div class="cb-coachmark-title" x-text="currentCoach.title"></div>
                    <div class="cb-coachmark-desc" x-text="currentCoach.desc"></div>
                    <div class="cb-coachmark-actions">
                        <button type="button" class="cb-coachmark-dismiss" @click="dismissQuickCoachmark()">{{ __('api_builder::api_builder.coachmark.dismiss') }}</button>
                        <button type="button" class="cb-coachmark-dismiss" x-show="coachStep > 0" @click="prevCoachStep()">{{ __('api_builder::api_builder.coachmark.back') }}</button>
                        <button type="button" class="btn btn-primary inline-flex items-center gap-2" @click="nextCoachStep()">
                            <span x-text="isLastCoachStep ? @js(__('api_builder::api_builder.coachmark.finish')) : @js(__('api_builder::api_builder.coachmark.next'))"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="cb-tab-wrap">
            <button type="button" class="cb-tab-btn {{ $activeTab === 'list' ? 'active' : '' }}" wire:click="setTab('list')">{{ __('api_builder::api_builder.tabs.list') }}</button>
            <button type="button" class="cb-tab-btn {{ $activeTab === 'credential' ? 'active' : '' }}" wire:click="setTab('credential')" data-coach-target="tab-credential">{{ __('api_builder::api_builder.tabs.credential') }}</button>
            <button type="button" class="cb-tab-btn {{ $activeTab === 'logs' ? 'active' : '' }}" wire:click="setTab('logs')" data-coach-target="tab-logs">{{ __('api_builder::api_builder.tabs.logs') }}</button>
        </div>

        @if($activeTab === 'list')
            <div class="cb-list-stat-grid">
                <div class="cb-list-stat-card" data-coach-target="stats-total-apis">
                    <div class="cb-list-stat-label">{{ __('api_builder::api_builder.list.total_apis') }}</div>
                    <div class="cb-list-stat-value">{{ $stats['totalApis'] }}</div>
                    <div class="cb-list-stat-note {{ $stats['totalApis'] > 0 ? 'text-green-600' : '' }}">
                        {{ $stats['totalApis'] > 0 ? '+' . $stats['totalApis'] . __('api_builder::api_builder.list.registered_suffix') : __('api_builder::api_builder.list.no_apis_yet') }}
                    </div>
                </div>

                <div class="cb-list-stat-card" data-coach-target="stats-active-endpoints">
                    <div class="cb-list-stat-label">{{ __('api_builder::api_builder.list.active_endpoints') }}</div>
                    <div class="cb-list-stat-value">{{ $stats['activeEndpoints'] }}</div>
                    <div class="cb-list-stat-note">
                        {{ $stats['totalApis'] > 0 ? number_format(($stats['activeEndpoints'] / max(1, $stats['totalApis'])) * 100, 1) . __('api_builder::api_builder.list.availability_suffix') : __('api_builder::api_builder.list.zero_availability') }}
                    </div>
                </div>

                <div class="cb-list-stat-card" data-coach-target="stats-avg-response">
                    <div class="cb-list-stat-label">{{ __('api_builder::api_builder.list.avg_response') }}</div>
                    <div class="cb-list-stat-value">{{ $stats['avgResponse'] }}ms</div>
                    <div class="cb-list-stat-note text-blue-600">
                        {{ $stats['avgResponse'] > 0 && $stats['avgResponse'] <= 200 ? __('api_builder::api_builder.list.optimal_range') : __('api_builder::api_builder.list.need_runtime_data') }}
                    </div>
                </div>

                <div class="cb-list-stat-card" data-coach-target="stats-error-rate">
                    <div class="cb-list-stat-label">{{ __('api_builder::api_builder.list.error_rate') }}</div>
                    <div class="cb-list-stat-value">{{ number_format($stats['errorRate'], 2) }}%</div>
                    <div class="cb-list-stat-note {{ $stats['errorRate'] <= 1 ? 'text-green-600' : '' }}">
                        {{ $stats['errorRate'] <= 1 ? __('api_builder::api_builder.list.healthy') : __('api_builder::api_builder.list.needs_attention') }}
                    </div>
                </div>
            </div>

            <div class="cb-table-frame">
                <table class="cb-table">
                    <thead>
                    <tr>
                        <th>{{ __('api_builder::api_builder.list.headers.api_name') }}</th>
                        <th>{{ __('api_builder::api_builder.list.headers.endpoint_path') }}</th>
                        <th>{{ __('api_builder::api_builder.list.headers.method') }}</th>
                        <th>{{ __('api_builder::api_builder.list.headers.status') }}</th>
                        <th>{{ __('api_builder::api_builder.list.headers.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($apis as $api)
                        <tr>
                            <td>
                                <div class="cb-api-name">{{ $api->name }}</div>
                                <div class="cb-api-meta">{{ __('api_builder::api_builder.list.created', ['time' => $api->created_at?->diffForHumans() ?? __('api_builder::api_builder.misc.dash')]) }}</div>
                            </td>
                            <td>
                                <span class="cb-endpoint-pill">{{ $api->endpoint_path }}</span>
                            </td>
                            <td>
                                <span class="cb-method-pill {{ $this->methodBadgeClass($api->method) }}">{{ strtoupper($api->method) }}</span>
                            </td>
                            <td>
                                <span class="cb-status {{ $this->statusDotClass($api->status) }}">
                                    <span class="cb-status-dot"></span>
                                    <span>{{ __('api_builder::api_builder.list.status.' . $api->status) }}</span>
                                </span>
                            </td>
                            <td>
                                <div class="cb-row-actions" @if($loop->first) data-coach-target="list-row-actions" @endif>
                                    <button type="button" class="cb-row-action-btn primary" wire:click="openTestModal('{{ $api->id }}')">{{ __('api_builder::api_builder.actions.test_api') }}</button>
                                    <button type="button" class="cb-row-action-btn" wire:click="openSnippetModal('{{ $api->id }}')">{{ __('api_builder::api_builder.actions.copy') }}</button>
                                    <button type="button" class="cb-row-action-btn" wire:click="editApi('{{ $api->id }}')">{{ __('api_builder::api_builder.actions.edit') }}</button>
                                    <button type="button" class="cb-row-action-btn danger" wire:click="deleteApi('{{ $api->id }}')">{{ __('api_builder::api_builder.actions.delete') }}</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="text-xl font-bold text-slate-700">{{ __('api_builder::api_builder.list.no_apis_title') }}</div>
                                <div class="mt-1 text-slate-500">{{ __('api_builder::api_builder.list.no_apis_desc') }}</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="cb-footer">
                    <div class="cb-result-count">
                        {{ __('api_builder::api_builder.list.showing_results', ['from' => $apis->firstItem() ?? 0, 'to' => $apis->lastItem() ?? 0, 'total' => $apis->total()]) }}
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs tracking-[0.15em] text-slate-500">{{ __('api_builder::api_builder.list.sort_by') }}</span>
                        <button type="button" wire:click="setSortBy('newest')" class="cb-sort-btn {{ $sortBy === 'newest' ? 'active' : '' }}">{{ __('api_builder::api_builder.list.newest_first') }}</button>
                        <button type="button" wire:click="setSortBy('oldest')" class="cb-sort-btn {{ $sortBy === 'oldest' ? 'active' : '' }}">{{ __('api_builder::api_builder.list.oldest_first') }}</button>
                    </div>
                </div>
            </div>
        @elseif($activeTab === 'credential')
            <div class="space-y-4">
                @if($latestGeneratedToken)
                    <div class="cb-token-panel" style="border-color:#c6d8f4;background:#f5f9ff;">
                        <div class="text-sm font-semibold text-slate-700">{{ __('api_builder::api_builder.credential.latest_token') }}</div>
                        <code class="cb-token-token">{{ $latestGeneratedToken }}</code>
                    </div>
                @endif

                <div class="cb-security-top">
                    <div class="cb-token-panel" data-coach-target="credential-security-module">
                        <span class="cb-token-kicker">{{ __('api_builder::api_builder.credential.module_badge') }}</span>
                        <h2 class="cb-token-title">{{ __('api_builder::api_builder.credential.title') }}</h2>
                        <p class="cb-token-desc">
                            {{ __('api_builder::api_builder.credential.desc') }}
                        </p>
                        <button type="button" class="btn btn-primary mt-3" wire:click="generateToken" data-coach-target="credential-generate-token-btn">+ {{ __('api_builder::api_builder.actions.generate_new_token') }}</button>
                    </div>

                    <div class="cb-token-insight-card" data-coach-target="credential-security-insights">
                        <div class="cb-token-insight-title">{{ __('api_builder::api_builder.credential.insights') }}</div>
                        <div class="cb-token-insight-box">
                            <div>
                                <div class="text-sm text-slate-500">{{ __('api_builder::api_builder.credential.active_tokens') }}</div>
                                <div class="text-xl font-bold text-slate-800">{{ $securityInsights['activeTokens'] }}</div>
                            </div>
                            <div class="text-blue-600 font-semibold">{{ __('api_builder::api_builder.credential.up') }}</div>
                        </div>
                        <div class="cb-token-insight-box">
                            <div>
                                <div class="text-sm text-slate-500">{{ __('api_builder::api_builder.credential.failed_24h') }}</div>
                                <div class="text-xl font-bold text-slate-800">{{ $securityInsights['failed24h'] }}</div>
                            </div>
                            <div class="text-orange-600 font-semibold">{{ __('api_builder::api_builder.credential.alert') }}</div>
                        </div>
                    </div>
                </div>

                    <div class="cb-table-frame">
                        <table class="cb-table">
                            <thead>
                            <tr>
                                <th>{{ __('api_builder::api_builder.credential.headers.token_name') }}</th>
                                <th>{{ __('api_builder::api_builder.credential.headers.api_key') }}</th>
                                <th>{{ __('api_builder::api_builder.credential.headers.scope') }}</th>
                                <th>{{ __('api_builder::api_builder.credential.headers.status') }}</th>
                                <th>{{ __('api_builder::api_builder.credential.headers.created_date') }}</th>
                                <th>{{ __('api_builder::api_builder.credential.headers.last_used') }}</th>
                                <th>{{ __('api_builder::api_builder.credential.headers.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tokens as $token)
                                <tr>
                                    <td @if($loop->first) data-coach-target="credential-token-name" @endif>
                                        <div class="cb-token-name">{{ $token->name }}</div>
                                        <div class="text-slate-500 text-sm mt-1">{{ $this->authMethodLabel($token->auth_method) }}</div>
                                    </td>
                                    <td @if($loop->first) data-coach-target="credential-api-key" @endif>
                                        <span class="cb-api-key-cell" title="{{ $this->apiKeyMasked($token) }}">{{ $this->apiKeyMasked($token) }}</span>
                                        <button type="button" class="cb-copy-btn" wire:click="copyApiKey('{{ $token->id }}')">{{ __('api_builder::api_builder.actions.copy') }}</button>
                                    </td>
                                    <td @if($loop->first) data-coach-target="credential-scope" @endif><span class="cb-endpoint-pill">{{ $token->scope_endpoint }}</span></td>
                                    <td @if($loop->first) data-coach-target="credential-status" @endif>
                                        <span class="cb-token-status {{ $this->tokenStatusClass($token->status) }}">
                                            <span class="dot"></span>
                                            <span>{{ __('api_builder::api_builder.credential.status.' . $token->status) }}</span>
                                        </span>
                                    </td>
                                    <td class="text-slate-600 font-medium" @if($loop->first) data-coach-target="credential-created-date" @endif>{{ $token->created_at?->format('M d, Y') ?? '-' }}</td>
                                    <td class="text-slate-600 font-medium" @if($loop->first) data-coach-target="credential-last-used" @endif>{{ $this->formatLastUsed($token->last_used_at) }}</td>
                                    <td @if($loop->first) data-coach-target="credential-token-actions" @endif>
                                        <div class="cb-token-actions">
                                            <button type="button" class="cb-token-action-btn" wire:click="deactivateToken('{{ $token->id }}')">{{ __('api_builder::api_builder.actions.inactive') }}</button>
                                            <button type="button" class="cb-token-action-btn danger" wire:click="deleteToken('{{ $token->id }}')">{{ __('api_builder::api_builder.actions.delete') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10">
                                        <div class="text-lg font-bold text-slate-700">{{ __('api_builder::api_builder.credential.no_tokens_title') }}</div>
                                        <div class="mt-1 text-slate-500">{{ __('api_builder::api_builder.credential.no_tokens_desc') }}</div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                        <div class="cb-footer">
                            <div class="cb-result-count">
                                {{ __('api_builder::api_builder.credential.showing_tokens', ['from' => $tokens->firstItem() ?? 0, 'to' => $tokens->lastItem() ?? 0, 'total' => $tokens->total()]) }}
                            </div>
                            <div class="cb-token-footer-nav">
                                <button type="button" wire:click="previousPage('tokenPage')" class="cb-page-btn" @disabled($tokens->onFirstPage())>{{ __('api_builder::api_builder.actions.previous') }}</button>
                                <button type="button" wire:click="nextPage('tokenPage')" class="cb-page-btn" @disabled(!$tokens->hasMorePages())>{{ __('api_builder::api_builder.actions.next') }}</button>
                            </div>
                        </div>
                </div>
            </div>
        @else
            <div class="space-y-4">
                <div class="cb-log-metric-grid">
                    <div class="cb-log-metric-card" data-coach-target="logs-metric-total">
                        <div class="cb-log-metric-label">{{ __('api_builder::api_builder.logs.total_calls') }}</div>
                        <div class="cb-log-metric-value">{{ number_format($logsOverview['totalCalls']) }}</div>
                        <div class="cb-log-metric-note">{{ __('api_builder::api_builder.logs.metric_note_total') }}</div>
                    </div>
                    <div class="cb-log-metric-card" data-coach-target="logs-metric-error">
                        <div class="cb-log-metric-label">{{ __('api_builder::api_builder.logs.error_rate') }}</div>
                        <div class="cb-log-metric-value">{{ number_format($logsOverview['errorRate'], 2) }}%</div>
                        <div class="cb-log-metric-note">{{ $logsOverview['errorRate'] < 1 ? __('api_builder::api_builder.logs.metric_note_error_low') : __('api_builder::api_builder.logs.metric_note_error_attention') }}</div>
                    </div>
                    <div class="cb-log-metric-card" data-coach-target="logs-metric-latency">
                        <div class="cb-log-metric-label">{{ __('api_builder::api_builder.logs.avg_latency') }}</div>
                        <div class="cb-log-metric-value">{{ $logsOverview['avgLatency'] }}ms</div>
                        <div class="cb-log-metric-note">{{ __('api_builder::api_builder.logs.metric_note_latency') }}</div>
                    </div>
                    <div class="cb-log-metric-card cb-log-metric-highlight" data-coach-target="logs-metric-success">
                        <div class="cb-log-metric-label">{{ __('api_builder::api_builder.logs.success_rate') }}</div>
                        <div class="cb-log-metric-value">{{ number_format($logsOverview['successRate'], 2) }}%</div>
                        <div class="cb-log-metric-note">{{ __('api_builder::api_builder.logs.metric_note_success') }}</div>
                    </div>
                </div>

                <div class="cb-log-grid">
                    <div class="cb-log-panel" data-coach-target="logs-activity-panel">
                        <div class="cb-log-header">
                            <div class="cb-log-title">{{ __('api_builder::api_builder.logs.activity_title') }}</div>
                            <div class="cb-log-header-actions" data-coach-target="logs-activity-actions">
                                <button type="button" class="cb-log-btn-ghost" wire:click="exportLogsCsv">{{ __('api_builder::api_builder.actions.export_csv') }}</button>
                                <button type="button" class="cb-log-btn-ghost text-red-600 hover:text-red-800" wire:click="clearLogs">{{ __('api_builder::api_builder.actions.clear') }}</button>
                                <button type="button" class="btn btn-primary" wire:click="toggleStream">
                                    {{ $streamPaused ? __('api_builder::api_builder.actions.resume_stream') : __('api_builder::api_builder.actions.pause_stream') }}
                                </button>
                            </div>
                        </div>

                        <div class="cb-table-frame" data-coach-target="logs-activity-table">
                            <table class="cb-table">
                                <thead>
                                <tr>
                                    <th>{{ __('api_builder::api_builder.logs.headers.timestamp') }}</th>
                                    <th>{{ __('api_builder::api_builder.logs.headers.method') }}</th>
                                    <th>{{ __('api_builder::api_builder.logs.headers.endpoint') }}</th>
                                    <th>{{ __('api_builder::api_builder.logs.headers.status') }}</th>
                                    <th>{{ __('api_builder::api_builder.logs.headers.latency') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="text-slate-600 font-medium">{{ $log->created_at?->format('H:i:s.v') ?? __('api_builder::api_builder.misc.dash') }}</td>
                                        <td><span class="cb-log-method {{ strtoupper($log->method) }}">{{ strtoupper($log->method) }}</span></td>
                                        <td class="font-medium text-slate-700">{{ $log->endpoint }}</td>
                                        <td class="cb-log-status {{ $this->logStatusClass($log->status_code) }}">
                                            {{ $log->status_code }} {{ $log->status_text }}
                                        </td>
                                        <td class="text-slate-600 font-medium">{{ $log->latency_ms }}ms</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-slate-500">{{ __('api_builder::api_builder.logs.no_logs') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($hasMoreLogs)
                            <div class="text-center">
                                <button type="button" class="cb-load-more" wire:click="loadMoreLogs">{{ __('api_builder::api_builder.actions.load_more_logs') }}</button>
                            </div>
                        @endif
                    </div>

                    <div class="cb-log-panel" data-coach-target="logs-error-distribution">
                        <div class="cb-log-header">
                            <div class="cb-log-title">{{ __('api_builder::api_builder.logs.error_distribution') }}</div>
                        </div>

                        <div>
                            @forelse($errorDistribution as $row)
                                <div class="cb-error-item">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-medium text-slate-700">{{ $row['endpoint'] }}</div>
                                        <div class="font-medium text-rose-700">{{ $row['percent'] }}% ({{ $row['count'] }}x)</div>
                                    </div>
                                    <div class="cb-error-bar-wrap">
                                        <div class="cb-error-bar" style="width: {{ $row['bar_percent'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-slate-500">{{ __('api_builder::api_builder.logs.no_logs') }}</div>
                            @endforelse
                        </div>

                        <div class="cb-error-note">
                            {{ __('api_builder::api_builder.logs.error_distribution_note') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($showTokenModal)
        <div class="cb-token-modal-overlay">
            <div class="cb-token-modal">
                <div>
                    <div class="cb-token-modal-title">{{ __('api_builder::api_builder.modal.generate_token_title') }}</div>
                    <div class="cb-token-modal-subtitle">{{ __('api_builder::api_builder.modal.generate_token_subtitle') }}</div>
                </div>

                <div class="mt-4 space-y-3">
                    <div>
                        <label class="cb-form-label">{{ __('api_builder::api_builder.modal.token_name') }}</label>
                        <input type="text" class="cb-form-input" wire:model.defer="newTokenName" placeholder="{{ __('api_builder::api_builder.modal.placeholder_token_name') }}">
                        @error('name')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="cb-form-label">{{ __('api_builder::api_builder.modal.status') }}</label>
                        <select class="cb-form-select" wire:model.defer="newTokenStatus">
                            <option value="active">{{ __('api_builder::api_builder.credential.status.active') }}</option>
                            <option value="expired">{{ __('api_builder::api_builder.credential.status.expired') }}</option>
                            <option value="disabled">{{ __('api_builder::api_builder.credential.status.disabled') }}</option>
                        </select>
                        @error('status')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="cb-form-label">{{ __('api_builder::api_builder.modal.scope_endpoint') }}</label>
                        <input type="text" class="cb-form-input" wire:model.defer="newTokenScope" placeholder="{{ __('api_builder::api_builder.modal.placeholder_scope_endpoint') }}">
                        <div class="text-xs text-slate-500 mt-1">{{ __('api_builder::api_builder.modal.scope_endpoint_help') }}</div>
                        @error('scope')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="cb-token-modal-actions">
                    <button type="button" class="cb-token-modal-cancel" wire:click="closeTokenModal">{{ __('api_builder::api_builder.actions.cancel') }}</button>
                    <button type="button" class="btn btn-primary cb-token-modal-submit" wire:click="submitGenerateToken">{{ __('api_builder::api_builder.actions.generate_new_token') }}</button>
                </div>
            </div>
        </div>
    @endif

    @if($showNewApiModal)
        <div class="cb-token-modal-overlay" x-on:click.self="$wire.closeNewApiModal()" x-data="{ newApiMode: @entangle('newApiMode') }" x-init="$nextTick(() => { if ($root.showQuickCoachmark) { $root.updateQuickCoachmarkPosition(); } })">
            <div class="cb-token-modal">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <div class="cb-token-modal-title text-xl">{{ __('api_builder::api_builder.modal.new_api_title') }}</div>
                        <div class="cb-token-modal-subtitle">{{ __('api_builder::api_builder.modal.new_api_subtitle') }}</div>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" wire:click="closeNewApiModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <div class="cb-api-mode-grid">
                        <button type="button" 
                                class="cb-api-mode-card p-6 rounded-xl text-center"
                                :class="{ 'selected': newApiMode === 'quick' }"
                                x-ref="quickModeCard"
                                data-coach-target="quick-mode-card"
                                @click="newApiMode = 'quick'">
                            <div class="text-3xl mb-3">⚡</div>
                            <div class="font-bold text-gray-800">{{ __('api_builder::api_builder.modal.quick_mode') }}</div>
                            <div class="text-xs text-gray-500 mt-2 leading-relaxed px-2">{{ __('api_builder::api_builder.modal.quick_mode_desc') }}</div>
                        </button>
                        <button type="button" 
                                class="cb-api-mode-card p-6 rounded-xl text-center"
                                :class="{ 'selected': newApiMode === 'advanced' }"
                                @click="newApiMode = 'advanced'">
                            <div class="text-3xl mb-3">🔧</div>
                            <div class="font-bold text-gray-800">{{ __('api_builder::api_builder.modal.advanced_mode') }}</div>
                            <div class="text-xs text-gray-500 mt-2 leading-relaxed px-2">{{ __('api_builder::api_builder.modal.advanced_mode_desc') }}</div>
                        </button>
                    </div>

                    <div x-show="newApiMode === 'quick'" x-transition class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <label class="block text-sm font-bold text-slate-700 mb-3">{{ __('api_builder::api_builder.modal.select_table') }}</label>
                        <div class="cb-table-select">
                            <select wire:model.defer="quickModeTable" class="cb-form-select shadow-sm" x-ref="quickModeTableSelect" data-coach-target="quick-mode-table-select">
                                <option value="">-- {{ __('api_builder::api_builder.modal.select_table_placeholder') }} --</option>
                                @if(!empty($availableTables))
                                    @foreach($availableTables as $table)
                                        <option value="{{ $table }}">{{ $table }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No tables available</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div class="cb-token-modal-actions mt-8 pt-6 border-top border-slate-100">
                    <button type="button" class="cb-token-modal-cancel" wire:click="closeNewApiModal">{{ __('api_builder::api_builder.actions.cancel') }}</button>
                    <button type="button" class="btn btn-primary px-8" wire:click="proceedNewApi" x-ref="quickModeCreateButton" data-coach-target="quick-mode-create-button">
                        <span x-text="newApiMode === 'quick' ? '{{ __('api_builder::api_builder.actions.create_api') }}' : '{{ __('api_builder::api_builder.actions.continue') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        .cb-snippet-container {
            background: #0d1117;
            border-radius: 12px;
            border: 1px solid #30363d;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
        }
        .cb-snippet-tabs {
            display: flex;
            background: #161b22;
            padding: 8px 12px;
            gap: 8px;
            border-bottom: 1px solid #30363d;
        }
        .cb-snippet-tab {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #8b949e;
            border-radius: 8px;
            transition: all 0.2s;
            border: none;
            background: transparent;
            cursor: pointer;
        }
        .cb-snippet-tab:hover {
            color: #c9d1d9;
            background: #21262d;
        }
        .cb-snippet-tab.active {
            color: #ffffff;
            background: #3b82f6;
        }
        .cb-code-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #161b22;
            border-bottom: 1px solid #21262d;
        }
        .cb-code-dots {
            display: flex;
            gap: 8px;
        }
        .cb-code-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .cb-code-dot-red { background: #ff5f56; }
        .cb-code-dot-yellow { background: #ffbd2e; }
        .cb-code-dot-green { background: #27c93f; }
        
        .cb-code-copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #238636;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .cb-code-copy-btn:hover {
            background: #2ea043;
            transform: translateY(-1px);
        }
        .cb-code-copy-btn:active {
            transform: translateY(0);
        }

        .cb-code-content {
            padding: 0;
            overflow: auto;
            background: #0d1117;
            flex-grow: 1;
            min-width: 0;
        }
        .cb-snippet-container {
            min-width: 0;
        }
        #cb-test-modal-grid > div {
            min-width: 0;
        }
        .cb-code-content pre {
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
        }
        .cb-code-content code {
            font-family: 'JetBrains Mono', 'Fira Code', 'Monaco', 'Menlo', monospace !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
            background: transparent !important;
            padding: 20px !important;
            display: block;
            color: #e6edf3;
            white-space: pre-wrap !important;
            word-wrap: break-word !important;
        }

        /* Scrollbar Styling */
        .cb-code-content::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        .cb-code-content::-webkit-scrollbar-track {
            background: #0d1117;
        }
        .cb-code-content::-webkit-scrollbar-thumb {
            background: #30363d;
            border-radius: 10px;
            border: 2px solid #0d1117;
        }
        .cb-code-content::-webkit-scrollbar-thumb:hover {
            background: #484f58;
        }
    </style>

    @if($showSnippetModal)
        <div class="cb-token-modal-overlay" x-on:click.self="$wire.closeSnippetModal()">
            <div class="cb-token-modal cb-snippet-modal !max-w-3xl">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="cb-token-modal-title text-2xl font-bold text-slate-800">{{ __('api_builder::api_builder.modal.snippet_title') }}</div>
                        <div class="cb-token-modal-subtitle text-slate-500 mt-1">{{ __('api_builder::api_builder.modal.snippet_subtitle') }}</div>
                    </div>
                    <button type="button" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all" wire:click="closeSnippetModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

                <div x-data="{ 
                    activeTab: 'curl',
                    init() {
                        this.highlight();
                        this.$watch('activeTab', () => this.highlight());
                    },
                    highlight() {
                        this.$nextTick(() => {
                            if (typeof hljs !== 'undefined') {
                                document.querySelectorAll('.cb-snippet-container pre code').forEach((block) => {
                                    block.removeAttribute('data-highlighted');
                                    hljs.highlightElement(block);
                                });
                            } else {
                                // Retry if hljs is not yet loaded
                                setTimeout(() => this.highlight(), 100);
                            }
                        });
                    }
                }">
                    
                    <div class="cb-snippet-container">
                        <div class="cb-snippet-tabs">
                            <button type="button" class="cb-snippet-tab" :class="activeTab === 'curl' ? 'active' : ''" @click="activeTab = 'curl'">cURL</button>
                            <button type="button" class="cb-snippet-tab" :class="activeTab === 'python' ? 'active' : ''" @click="activeTab = 'python'">Python</button>
                            <button type="button" class="cb-snippet-tab" :class="activeTab === 'php' ? 'active' : ''" @click="activeTab = 'php'">PHP</button>
                        </div>

                        <div class="cb-code-header">
                            <div class="cb-code-dots">
                                <div class="cb-code-dot cb-code-dot-red"></div>
                                <div class="cb-code-dot cb-code-dot-yellow"></div>
                                <div class="cb-code-dot cb-code-dot-green"></div>
                            </div>
                            <button type="button" class="cb-code-copy-btn" wire:click="copySnippet(activeTab)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                <span>{{ __('api_builder::api_builder.actions.copy') }}</span>
                            </button>
                        </div>

                        <div class="cb-code-content">
                            <div x-show="activeTab === 'curl'">
                                <pre><code class="language-bash hljs">{!! e($snippetCurl) !!}</code></pre>
                            </div>
                            <div x-show="activeTab === 'python'" x-cloak>
                                <pre><code class="language-python hljs">{!! e($snippetPython) !!}</code></pre>
                            </div>
                            <div x-show="activeTab === 'php'" x-cloak>
                                <pre><code class="language-php hljs">{!! e($snippetPhp) !!}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showTestModal)
        <div class="cb-token-modal-overlay" 
             x-on:click.self="$wire.closeTestModal()" 
             x-cloak
             x-data="{
                loading: false,
                tokenGenerating: false,
                showTokenPrompt: false,
                statusCode: @entangle('testStatusCode'),
                statusText: @entangle('testStatusText'),
                response: @entangle('testResponse'),
                token: @entangle('testToken'),
                async generateTokenAndRun() {
                    this.tokenGenerating = true;
                    try {
                        await this.$wire.generateDefaultTestToken();
                        this.showTokenPrompt = false;
                        await this.runTest();
                    } finally {
                        this.tokenGenerating = false;
                    }
                },
                async runTest() {
                    if (!this.token) {
                        this.showTokenPrompt = true;
                        return;
                    }
                    this.showTokenPrompt = false;
                    this.loading = true;
                    this.response = null;
                    this.statusCode = null;

                    try {
                        const method = '{{ $testMethod }}';
                        let url = '{{ $testEndpoint }}';
                        const payload = @entangle('testPayload');

                        const options = {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        };

                        if (this.token) {
                            options.headers['Authorization'] = 'Bearer ' + this.token;
                        }
                        
                        if (method !== 'GET' && method !== 'HEAD') {
                            options.body = JSON.stringify(payload);
                        } else {
                            const params = new URLSearchParams();
                            for (const [key, value] of Object.entries(payload)) {
                                if (value) params.append(key, value);
                            }
                            const queryString = params.toString();
                            if (queryString) {
                                url += (url.includes('?') ? '&' : '?') + queryString;
                            }
                        }
                        
                        const res = await fetch(url, options);
                        this.statusCode = res.status;
                        this.statusText = res.statusText;
                        
                        const data = await res.json();
                        this.response = JSON.stringify(data, null, 4);
                        this.highlight();
                    } catch (err) {
                        this.statusCode = 500;
                        this.statusText = 'Error';
                        this.response = JSON.stringify({ error: err.message }, null, 4);
                        this.highlight();
                    } finally {
                        this.loading = false;
                    }
                },
                highlight() {
                    this.$nextTick(() => {
                        if (window.hljs) {
                            const block = this.$refs.testResponseCode;
                            if (block) {
                                block.removeAttribute('data-highlighted');
                                window.hljs.highlightElement(block);
                            }
                        }
                    });
                }
             }">
            <div class="cb-token-modal cb-test-api-modal" style="max-height: 90vh; overflow-y: auto;">
                <div x-show="showTokenPrompt" x-cloak class="absolute inset-0 bg-white/95 backdrop-blur-sm flex items-center justify-center rounded-xl" style="z-index: 50;">
                    <div class="text-center max-w-sm mx-auto p-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <div class="text-lg font-bold text-slate-800 mb-2">{{ __('api_builder::api_builder.modal.test_token_missing_title') }}</div>
                        <div class="text-sm text-slate-500 mb-6">{{ __('api_builder::api_builder.modal.test_token_missing_desc') }}</div>
                        <div class="flex items-center justify-center gap-3">
                            <button type="button" class="btn btn-secondary" @click="showTokenPrompt = false">{{ __('api_builder::api_builder.actions.cancel') }}</button>
                            <button type="button" class="btn btn-primary flex items-center gap-2" @click="generateTokenAndRun()" :disabled="tokenGenerating">
                                <span x-show="tokenGenerating" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent" x-cloak></span>
                                <span x-text="tokenGenerating ? '{{ __('api_builder::api_builder.modal.test_loading') }}' : '{{ __('api_builder::api_builder.actions.generate_default_token') }}'"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-start mb-6 pb-4 border-b border-slate-100 sticky top-0 bg-white z-20" style="margin-top: -24px; padding-top: 24px;">
                    <div>
                        <div class="cb-token-modal-title text-2xl font-bold text-slate-800">{{ __('api_builder::api_builder.modal.test_title') }}</div>
                        <div class="cb-token-modal-subtitle text-slate-500 mt-1">{{ __('api_builder::api_builder.modal.test_subtitle') }}</div>
                    </div>
                    <button type="button" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all" wire:click="closeTestModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div id="cb-test-modal-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    <div class="space-y-6 min-w-0">
                        <div>
                            <label class="cb-form-label">{{ __('api_builder::api_builder.modal.test_endpoint') }}</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-bold">
                                    {{ $testMethod }}
                                </span>
                                <input type="text" readonly value="{{ $testEndpoint }}" class="cb-form-input !rounded-l-none bg-slate-50 text-slate-600 font-mono text-[11px] overflow-hidden text-ellipsis">
                            </div>
                        </div>

                        @if(!empty($testPayload))
                            <div>
                                <label class="cb-form-label">Payload Parameters</label>
                                <div class="space-y-4 p-5 bg-slate-50 rounded-xl border border-slate-200 overflow-y-auto" style="max-height: 300px;">
                                    @foreach($testPayload as $key => $value)
                                        <div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ $key }}</div>
                                            <input type="text" wire:model.defer="testPayload.{{ $key }}" class="cb-form-input !py-2 shadow-sm" placeholder="Value...">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-2">
                            <button type="button" 
                                    class="btn btn-primary w-full py-3.5 flex items-center justify-center gap-2 shadow-lg shadow-blue-200" 
                                    @click="runTest()" 
                                    :disabled="loading">
                                <span x-show="!loading">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </span>
                                <span x-show="loading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent" x-cloak></span>
                                <span class="font-bold uppercase tracking-wide text-sm" x-text="loading ? '{{ __('api_builder::api_builder.modal.test_loading') }}' : '{{ __('api_builder::api_builder.modal.test_send') }}'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col min-w-0">
                        <label class="cb-form-label">{{ __('api_builder::api_builder.modal.test_response') }}</label>
                        <div class="flex flex-col cb-snippet-container relative min-w-0" style="height: 380px;">
                            
                            <div class="cb-code-header shrink-0">
                                <div class="flex items-center gap-3">
                                    <div class="cb-code-dots">
                                        <div class="cb-code-dot cb-code-dot-red"></div>
                                        <div class="cb-code-dot cb-code-dot-yellow"></div>
                                        <div class="cb-code-dot cb-code-dot-green"></div>
                                    </div>
                                    <template x-if="statusCode">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded" :class="statusCode >= 400 ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400'">
                                            <span x-text="statusCode"></span> <span x-text="statusText"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            
                            <div class="cb-code-content !p-0 flex-1 relative overflow-auto min-h-0 min-w-0">
                                <div x-show="loading" class="absolute inset-0 bg-slate-900/50 flex items-center justify-center z-10" x-cloak>
                                    <span class="animate-spin rounded-full h-8 w-8 border-4 border-white border-t-transparent"></span>
                                </div>

                                <template x-if="response">
                                    <pre class="!m-0 min-h-full"><code x-ref="testResponseCode" class="language-json hljs !p-5" x-text="response"></code></pre>
                                </template>
                                
                                <template x-if="!response && !loading">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 gap-3 opacity-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm font-medium">Ready to test</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
<script>
    function cbApiBuilderCoachmark() {
        return {
            showQuickCoachmark: false,
            coachTarget: { top: 0, left: 0, width: 0, height: 0 },
            coachReady: false,
            awaitingStep2: false,
            overviewQueued: false,
            credentialQueueTimer: null,
            logsQueueTimer: null,
            coachStep: 0,
            currentJourney: 'quick',
            journeys: {
                quick: {
                    key: 'cb_api_builder_quick_mode_coachmark_v1',
                    steps: [
                        {
                            targetRef: 'generateApiButton',
                            targetSelector: '[data-coach-target="generate-api-button"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_1_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_1_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="quick-mode-card"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_2_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_2_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="quick-mode-table-select"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_3_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_3_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="quick-mode-create-button"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_4_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_4_desc')),
                        }
                    ]
                },
                overview: {
                    key: 'cb_api_builder_overview_coachmark_v1',
                    steps: [
                        {
                            targetSelector: '[data-coach-target="stats-total-apis"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_5_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_5_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="stats-active-endpoints"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_6_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_6_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="stats-avg-response"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_7_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_7_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="stats-error-rate"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_8_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_8_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="list-row-actions"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_9_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_9_desc')),
                        }
                    ]
                },
                credential: {
                    key: 'cb_api_builder_credential_coachmark_v2',
                    steps: [
                        {
                            targetSelector: '[data-coach-target="tab-credential"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_10_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_10_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-security-module"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_11_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_11_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-generate-token-btn"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_12_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_12_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-security-insights"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_13_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_13_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-token-name"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_14_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_14_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-api-key"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_15_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_15_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-scope"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_16_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_16_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-status"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_17_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_17_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-created-date"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_18_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_18_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-last-used"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_19_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_19_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="credential-token-actions"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_20_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_20_desc')),
                        }
                    ]
                },
                logs: {
                    key: 'cb_api_builder_logs_coachmark_v1',
                    steps: [
                        {
                            targetSelector: '[data-coach-target="tab-logs"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_21_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_21_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="logs-metric-total"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_22_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_22_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="logs-metric-error"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_23_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_23_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="logs-metric-latency"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_24_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_24_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="logs-metric-success"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_25_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_25_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="logs-activity-actions"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_26_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_26_desc')),
                        },
                        {
                            targetSelector: '[data-coach-target="logs-error-distribution"]',
                            title: @js(__('api_builder::api_builder.coachmark.step_27_title')),
                            desc: @js(__('api_builder::api_builder.coachmark.step_27_desc')),
                        }
                    ]
                }
            },
            init() {
                try {
                    this.showQuickCoachmark = !window.localStorage.getItem(this.journeys.quick.key);
                } catch (e) {
                    this.showQuickCoachmark = false;
                }
                if (this.showQuickCoachmark) {
                    this.$nextTick(() => this.updateQuickCoachmarkPosition());
                    window.addEventListener('resize', () => this.updateQuickCoachmarkPosition());
                    window.addEventListener('scroll', () => this.updateQuickCoachmarkPosition(), true);
                }
            },
            get currentCoach() {
                const steps = this.journeys[this.currentJourney]?.steps || [];
                return steps[this.coachStep] || steps[0] || {};
            },
            get isLastCoachStep() {
                const steps = this.journeys[this.currentJourney]?.steps || [];
                return this.coachStep >= (steps.length - 1);
            },
            startJourney(journey) {
                if (!this.journeys[journey]) return;
                this.currentJourney = journey;
                this.coachStep = 0;
                this.coachReady = false;
                this.awaitingStep2 = false;
                this.showQuickCoachmark = true;
                this.$nextTick(() => this.waitAndUpdateCoachmarkPosition());
            },
            updateQuickCoachmarkPosition() {
                if (!this.showQuickCoachmark) return;
                let target = null;
                if (this.currentCoach.targetRef) {
                    target = this.$refs[this.currentCoach.targetRef] || null;
                }
                if (!target && this.currentCoach.targetSelector) {
                    target = document.querySelector(this.currentCoach.targetSelector);
                }
                if (!target) {
                    this.coachReady = false;
                    return;
                }
                const rect = target.getBoundingClientRect();
                this.coachTarget = {
                    top: rect.top,
                    left: rect.left,
                    width: rect.width,
                    height: rect.height
                };
                this.coachReady = true;
            },
            waitAndUpdateCoachmarkPosition(retries = 10) {
                this.updateQuickCoachmarkPosition();
                if (this.coachReady || retries <= 0) return;
                setTimeout(() => this.waitAndUpdateCoachmarkPosition(retries - 1), 120);
            },
            coachCardStyle() {
                const cardWidth = 320;
                const cardHeight = 190;
                const margin = 16;
                const viewportWidth = window.innerWidth || 1280;
                const viewportHeight = window.innerHeight || 720;

                let left = Math.max(margin, this.coachTarget.left + this.coachTarget.width - cardWidth);
                left = Math.min(left, viewportWidth - cardWidth - margin);

                let top = this.coachTarget.top + this.coachTarget.height + 16;
                if (top + cardHeight > viewportHeight - margin) {
                    top = this.coachTarget.top - cardHeight - 16;
                }
                top = Math.max(margin, top);

                return `top:${top}px;left:${left}px;`;
            },
            nextCoachStep() {
                if (this.currentJourney === 'quick' && this.coachStep === 0) {
                    this.awaitingStep2 = true;
                    this.$wire.openNewApiModal();
                    return;
                }

                const steps = this.journeys[this.currentJourney]?.steps || [];
                if (this.coachStep < (steps.length - 1)) {
                    this.coachStep++;
                    this.$nextTick(() => this.updateQuickCoachmarkPosition());
                    setTimeout(() => this.waitAndUpdateCoachmarkPosition(), 120);
                    return;
                }

                if (this.currentJourney === 'quick') {
                    this.dismissQuickCoachmark();
                    this.$wire.closeNewApiModal();
                    this.queueOverviewJourney(620);
                    return;
                }
                if (this.currentJourney === 'overview') {
                    this.dismissQuickCoachmark();
                    this.$wire.setTab('credential');
                    this.queueCredentialJourney(520);
                    return;
                }

                this.dismissQuickCoachmark();
            },
            prevCoachStep() {
                if (this.coachStep <= 0) return;
                this.coachStep--;
                this.$nextTick(() => this.updateQuickCoachmarkPosition());
                setTimeout(() => this.waitAndUpdateCoachmarkPosition(), 120);
            },
            dismissQuickCoachmark() {
                this.showQuickCoachmark = false;
                try {
                    const key = this.journeys[this.currentJourney]?.key;
                    if (key) window.localStorage.setItem(key, '1');
                } catch (e) {}
            },
            handleQuickGenerated() {
                this.queueOverviewJourney(420);
            },
            queueOverviewJourney(delay = 420) {
                if (this.overviewQueued) return;
                try {
                    const key = this.journeys.overview.key;
                    if (window.localStorage.getItem(key)) return;
                } catch (e) {}
                this.overviewQueued = true;
                setTimeout(() => {
                    this.overviewQueued = false;
                    this.startJourney('overview');
                }, delay);
            },
            queueCredentialJourney(delay = 420) {
                if (this.showQuickCoachmark) return;
                try {
                    const key = this.journeys.credential.key;
                    if (window.localStorage.getItem(key)) return;
                } catch (e) {}
                if (this.credentialQueueTimer) {
                    clearTimeout(this.credentialQueueTimer);
                }
                this.credentialQueueTimer = setTimeout(() => {
                    this.startCredentialJourneyWhenReady();
                }, delay);
            },
            queueLogsJourney(delay = 420) {
                if (this.showQuickCoachmark) return;
                try {
                    const key = this.journeys.logs.key;
                    if (window.localStorage.getItem(key)) return;
                } catch (e) {}
                if (this.logsQueueTimer) {
                    clearTimeout(this.logsQueueTimer);
                }
                this.logsQueueTimer = setTimeout(() => {
                    this.startLogsJourneyWhenReady();
                }, delay);
            },
            startCredentialJourneyWhenReady(retries = 10) {
                if (this.showQuickCoachmark) return;
                const target = document.querySelector('[data-coach-target="credential-security-module"]');
                if (!target) {
                    if (retries <= 0) return;
                    setTimeout(() => this.startCredentialJourneyWhenReady(retries - 1), 120);
                    return;
                }
                this.startJourney('credential');
            },
            startLogsJourneyWhenReady(retries = 10) {
                if (this.showQuickCoachmark) return;
                const target = document.querySelector('[data-coach-target="logs-metric-total"]');
                if (!target) {
                    if (retries <= 0) return;
                    setTimeout(() => this.startLogsJourneyWhenReady(retries - 1), 120);
                    return;
                }
                this.startJourney('logs');
            },
            reopenGuideFromStart() {
                this.coachStep = 0;
                this.showQuickCoachmark = true;
                this.$nextTick(() => this.updateQuickCoachmarkPosition());
            }
        };
    }
</script>
</div>
