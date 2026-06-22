@if ($paginator->hasPages())
<div class="pagination" style="margin-top:16px">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;border:1px solid #e5e7eb;color:#d1d5db;background:#fff">&#8592;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;border:1px solid #e5e7eb;color:#6b7280;background:#fff;text-decoration:none">&#8592;</a>
    @endif

    {{-- Page links --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:6px 8px;color:#6b7280;font-size:13px">...</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:600;border:1px solid #CC0000;color:#fff;background:#CC0000">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;border:1px solid #e5e7eb;color:#6b7280;background:#fff;text-decoration:none">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;border:1px solid #e5e7eb;color:#6b7280;background:#fff;text-decoration:none">&#8594;</a>
    @else
        <span style="padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;border:1px solid #e5e7eb;color:#d1d5db;background:#fff">&#8594;</span>
    @endif

    <span style="font-size:12px;color:#9ca3af;margin-left:8px">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }}</span>
</div>
@endif
