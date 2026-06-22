@if ($paginator->hasPages())
<div style="display:flex;align-items:center;gap:8px;margin-top:16px;justify-content:center">
    @if ($paginator->onFirstPage())
        <span style="padding:6px 14px;border-radius:6px;font-size:13px;border:1px solid #e5e7eb;color:#d1d5db;background:#fff">&#8592; Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:6px 14px;border-radius:6px;font-size:13px;border:1px solid #e5e7eb;color:#6b7280;background:#fff;text-decoration:none">&#8592; Prev</a>
    @endif

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:6px 14px;border-radius:6px;font-size:13px;border:1px solid #e5e7eb;color:#6b7280;background:#fff;text-decoration:none">Next &#8594;</a>
    @else
        <span style="padding:6px 14px;border-radius:6px;font-size:13px;border:1px solid #e5e7eb;color:#d1d5db;background:#fff">Next &#8594;</span>
    @endif
</div>
@endif
