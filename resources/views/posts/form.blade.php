@extends('layouts.app')
@section('title', $post->exists ? 'Edit Artikel' : 'Artikel Baru')

@section('content')
@php $isEdit = $post->exists; @endphp

<div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#1a1a1a;margin-bottom:2px;">{{ $isEdit ? 'Edit Artikel' : 'Artikel Baru' }}</h1>
    </div>
    <a href="{{ route('posts.index') }}" style="font-size:13px;color:#9ca3af;text-decoration:none;">← Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-error" style="margin-bottom:16px;">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ $isEdit ? route('posts.update', $post) : route('posts.store') }}" id="post-form">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- ── INFORMASI DASAR ── --}}
    <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:22px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;margin-bottom:18px;">Informasi Dasar</div>

        <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label">Judul Artikel <span style="color:#CC0000">*</span></label>
            <input type="text" name="title" id="title-input" value="{{ old('title', $post->title) }}"
                placeholder="Tulis judul yang menarik..." class="inp" style="font-size:15px;font-weight:600;"
                oninput="autoSlug(this.value)">
        </div>

        <div style="display:grid;grid-template-columns:1fr 200px;gap:14px;margin-bottom:14px;">
            <div class="form-group">
                <label class="form-label">Slug URL <span style="color:#CC0000">*</span></label>
                <input type="text" name="slug" id="slug-input" value="{{ old('slug', $post->slug) }}"
                    placeholder="judul-artikel-saya" class="inp" style="font-family:monospace;font-size:12.5px;">
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="category" class="sel" style="width:100%;">
                    @foreach($categories as $c)
                    <option value="{{ $c }}" {{ old('category', $post->category) === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label">Ringkasan (Excerpt) <span style="color:#CC0000">*</span></label>
            <textarea name="excerpt" rows="2" placeholder="Ringkasan singkat untuk ditampilkan di halaman blog..." class="inp" style="resize:vertical;">{{ old('excerpt', $post->excerpt) }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 180px auto;gap:14px;align-items:end;">
            <div class="form-group">
                <label class="form-label">Penulis</label>
                <input type="text" name="author" value="{{ old('author', $post->author ?: 'Tim BJA Logistic') }}" class="inp">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Publikasi</label>
                <input type="date" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="inp">
            </div>
            <div class="form-group" style="padding-bottom:2px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 0;">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" id="pub-toggle"
                        {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                        style="width:16px;height:16px;accent-color:#16a34a;">
                    <span style="font-size:13px;font-weight:600;color:#1a1a1a;">Publikasikan</span>
                </label>
            </div>
        </div>
    </div>

    {{-- ── COVER ── --}}
    <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:22px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;margin-bottom:18px;">Cover</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group">
                <label class="form-label">Gambar Cover</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" name="cover_url" id="cover-url" value="{{ old('cover_url', $post->cover_url) }}"
                        placeholder="URL atau upload gambar →" class="inp" style="flex:1;">
                    <label style="display:inline-flex;align-items:center;gap:5px;padding:0 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12.5px;font-weight:600;color:#4b5563;cursor:pointer;white-space:nowrap;height:38px;background:#f9fafb;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Upload
                        <input type="file" accept="image/*" style="display:none;" onchange="uploadCover(this)">
                    </label>
                </div>
                @if($post->cover_url)
                <img src="{{ $post->cover_url }}" style="margin-top:8px;height:80px;border-radius:6px;object-fit:cover;" onerror="this.style.display='none'">
                @endif
            </div>
            <div class="form-group">
                <label class="form-label">Alt Text Cover</label>
                <input type="text" name="cover_alt" value="{{ old('cover_alt', $post->cover_alt) }}"
                    placeholder="Deskripsi gambar untuk aksesibilitas &amp; SEO" class="inp">
            </div>
        </div>
    </div>

    {{-- ── ISI ARTIKEL ── --}}
    <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:22px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;">Isi Artikel</div>

        {{-- Toolbar --}}
        <div style="display:flex;gap:2px;flex-wrap:wrap;padding:8px;background:#f8f9fa;border:1px solid #e5e7eb;border-bottom:none;border-radius:8px 8px 0 0;">
            @foreach(['h1','h2','h3','h4'] as $h)
            <button type="button" onclick="fmt('{{ $h }}')" class="editor-btn" style="font-weight:700;font-size:11.5px;width:30px;">{{ strtoupper($h) }}</button>
            @endforeach
            <div style="width:1px;background:#e5e7eb;margin:2px 4px;"></div>
            <button type="button" onclick="fmt('bold')" class="editor-btn" style="font-weight:900;">B</button>
            <button type="button" onclick="fmt('italic')" class="editor-btn" style="font-style:italic;">I</button>
            <button type="button" onclick="fmt('underline')" class="editor-btn" style="text-decoration:underline;">U</button>
            <button type="button" onclick="fmt('strikethrough')" class="editor-btn" style="text-decoration:line-through;">S</button>
            <div style="width:1px;background:#e5e7eb;margin:2px 4px;"></div>
            <button type="button" onclick="fmt('justifyLeft')" class="editor-btn" title="Rata kiri">≡</button>
            <button type="button" onclick="fmt('justifyCenter')" class="editor-btn" title="Rata tengah">☰</button>
            <button type="button" onclick="fmt('justifyRight')" class="editor-btn" title="Rata kanan">≡</button>
            <button type="button" onclick="fmt('justifyFull')" class="editor-btn" title="Rata penuh">▤</button>
            <div style="width:1px;background:#e5e7eb;margin:2px 4px;"></div>
            <button type="button" onclick="fmt('insertOrderedList')" class="editor-btn" title="Ordered list">1.</button>
            <button type="button" onclick="fmt('insertUnorderedList')" class="editor-btn" title="Unordered list">•</button>
            <button type="button" onclick="fmt('formatBlock','blockquote')" class="editor-btn" title="Blockquote">"</button>
            <button type="button" onclick="fmt('insertHorizontalRule')" class="editor-btn" title="Garis">—</button>
            <div style="width:1px;background:#e5e7eb;margin:2px 4px;"></div>
            <button type="button" onclick="insertLink()" class="editor-btn" title="Link">🔗</button>
            <button type="button" onclick="insertImage()" class="editor-btn" title="Gambar">🖼</button>
            <div style="width:1px;background:#e5e7eb;margin:2px 4px;"></div>
            <button type="button" onclick="fmt('undo')" class="editor-btn" title="Undo">↩</button>
            <button type="button" onclick="fmt('redo')" class="editor-btn" title="Redo">↪</button>
        </div>
        <div id="editor" contenteditable="true"
            style="min-height:320px;padding:16px;border:1px solid #e5e7eb;border-radius:0 0 8px 8px;font-size:14px;line-height:1.75;color:#1a1a1a;outline:none;overflow-y:auto;"
            oninput="syncContent()">
            {!! old('content', $post->content) !!}
        </div>
        <input type="hidden" name="content" id="content-input" value="{{ old('content', $post->content) }}">
    </div>

    {{-- ── SEO ── --}}
    <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:22px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;margin-bottom:18px;">SEO</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
            <div class="form-group">
                <label class="form-label" style="display:flex;justify-content:space-between;">
                    Meta Title <span id="mt-count" style="font-weight:400;color:#9ca3af;">0/60</span>
                </label>
                <input type="text" name="meta_title" maxlength="60" value="{{ old('meta_title', $post->meta_title) }}"
                    placeholder="Judul untuk Google (maks 60 karakter)" class="inp"
                    oninput="document.getElementById('mt-count').textContent=this.value.length+'/60';updateSeoPreview()">
            </div>
            <div class="form-group">
                <label class="form-label">Focus Keyword</label>
                <input type="text" name="focus_keyword" value="{{ old('focus_keyword', $post->focus_keyword) }}"
                    placeholder="contoh: cargo laut Jakarta Papua" class="inp">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label" style="display:flex;justify-content:space-between;">
                Meta Description <span id="md-count" style="font-weight:400;color:#9ca3af;">0/160</span>
            </label>
            <textarea name="meta_description" maxlength="160" rows="2" class="inp" style="resize:vertical;"
                placeholder="Deskripsi singkat untuk Google (maks 160 karakter)"
                oninput="document.getElementById('md-count').textContent=this.value.length+'/160';updateSeoPreview()">{{ old('meta_description', $post->meta_description) }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label">Tags</label>
            <input type="text" id="tags-display" value="{{ old('tags_raw', implode(', ', $post->tags ?? [])) }}"
                placeholder="Ketik tag, tekan Enter atau koma..." class="inp"
                oninput="syncTags()" onkeydown="handleTagKey(event)">
            <input type="hidden" name="tags_raw" id="tags-raw" value="{{ old('tags_raw', implode(', ', $post->tags ?? [])) }}">
            <div style="font-size:11px;color:#9ca3af;margin-top:3px;">Pisahkan dengan Enter atau koma</div>
        </div>

        {{-- Google Preview --}}
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:14px 16px;">
            <div style="font-size:9.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;">Google Preview</div>
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:6px;padding:12px 14px;max-width:520px;">
                <div style="font-size:11px;color:#5f6368;margin-bottom:2px;">bjalogistic.id › blog › <span id="prev-slug">slug-artikel</span></div>
                <div id="prev-title" style="font-size:16px;font-weight:600;color:#1a0dab;margin-bottom:3px;">Meta Title</div>
                <div id="prev-desc" style="font-size:13px;color:#4d5156;line-height:1.5;">Meta description akan tampil di sini...</div>
            </div>
        </div>
    </div>

    {{-- ── OG / SOCIAL ── --}}
    <div style="background:#fff;border:1px solid #f0f0f0;border-radius:12px;padding:22px 24px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;margin-bottom:18px;">OG / Social Media Tags</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
            <div class="form-group">
                <label class="form-label">OG Title</label>
                <input type="text" name="og_title" value="{{ old('og_title', $post->og_title) }}"
                    placeholder="Judul saat dibagikan di sosmed" class="inp" oninput="updateOgPreview()">
            </div>
            <div class="form-group">
                <label class="form-label">OG Image URL</label>
                <input type="text" name="og_image" value="{{ old('og_image', $post->og_image) }}"
                    placeholder="/blog/og-default.jpg" class="inp" oninput="updateOgPreview()">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" style="display:flex;justify-content:space-between;">
                OG Description <span id="og-count" style="font-weight:400;color:#9ca3af;">0/200</span>
            </label>
            <textarea name="og_description" maxlength="200" rows="2" class="inp" style="resize:vertical;"
                placeholder="Deskripsi saat dibagikan di sosmed"
                oninput="document.getElementById('og-count').textContent=this.value.length+'/200';updateOgPreview()">{{ old('og_description', $post->og_description) }}</textarea>
        </div>

        {{-- OG Preview --}}
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:14px 16px;">
            <div style="font-size:9.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;">Social / OG Preview</div>
            <div style="background:#fff;border:1px solid #dde3e9;border-radius:8px;overflow:hidden;max-width:380px;">
                <div id="og-img-prev" style="height:120px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#c4c4c4;font-size:12px;">OG Image Preview</div>
                <div style="padding:10px 12px;border-top:1px solid #dde3e9;">
                    <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">BJALOGISTIC.ID</div>
                    <div id="og-title-prev" style="font-size:14px;font-weight:700;color:#1a1a1a;margin-bottom:2px;">OG Title</div>
                    <div id="og-desc-prev" style="font-size:12.5px;color:#6b7280;">OG description...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;gap:10px;padding-bottom:32px;">
        <button type="submit" class="btn btn-primary">{{ $isEdit ? '💾 Simpan Perubahan' : '💾 Simpan Artikel' }}</button>
        <a href="{{ route('posts.index') }}" class="btn btn-outline">Batal</a>
    </div>
</form>
</div>

<style>
.editor-btn {
    padding:3px 7px;border:none;background:transparent;border-radius:4px;cursor:pointer;font-size:13px;color:#4b5563;line-height:1.4;
}
.editor-btn:hover { background:#e5e7eb; }
#editor h1{font-size:2em;font-weight:800;margin:.5em 0}
#editor h2{font-size:1.5em;font-weight:700;margin:.5em 0}
#editor h3{font-size:1.25em;font-weight:700;margin:.4em 0}
#editor h4{font-size:1.1em;font-weight:700;margin:.4em 0}
#editor blockquote{border-left:3px solid #CC0000;padding-left:12px;color:#6b7280;margin:8px 0;}
#editor a{color:#CC0000;text-decoration:underline;}
#editor ul{padding-left:20px;list-style:disc}
#editor ol{padding-left:20px;list-style:decimal}
</style>

@push('scripts')
<script>
// Rich text editor
function fmt(cmd, val) {
    document.getElementById('editor').focus();
    if (['h1','h2','h3','h4'].includes(cmd)) {
        document.execCommand('formatBlock', false, cmd);
    } else if (cmd === 'formatBlock') {
        document.execCommand('formatBlock', false, val);
    } else {
        document.execCommand(cmd, false, val || null);
    }
    syncContent();
}
function syncContent() {
    document.getElementById('content-input').value = document.getElementById('editor').innerHTML;
}
function insertLink() {
    const url = prompt('Masukkan URL:');
    if (url) { document.getElementById('editor').focus(); document.execCommand('createLink', false, url); syncContent(); }
}
function insertImage() {
    const url = prompt('Masukkan URL gambar:');
    if (url) { document.getElementById('editor').focus(); document.execCommand('insertImage', false, url); syncContent(); }
}

// Auto slug
let slugManual = {{ $isEdit ? 'true' : 'false' }};
document.getElementById('slug-input').addEventListener('input', () => slugManual = true);
function autoSlug(val) {
    if (slugManual) return;
    const slug = val.toLowerCase().trim()
        .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    document.getElementById('slug-input').value = slug;
    document.getElementById('prev-slug').textContent = slug || 'slug-artikel';
    updateSeoPreview();
}

// SEO preview
function updateSeoPreview() {
    const t = document.querySelector('[name=meta_title]').value;
    const d = document.querySelector('[name=meta_description]').value;
    const s = document.getElementById('slug-input').value;
    document.getElementById('prev-title').textContent = t || 'Meta Title';
    document.getElementById('prev-desc').textContent = d || 'Meta description akan tampil di sini...';
    document.getElementById('prev-slug').textContent = s || 'slug-artikel';
}

// OG preview
function updateOgPreview() {
    const t = document.querySelector('[name=og_title]').value;
    const d = document.querySelector('[name=og_description]').value;
    const img = document.querySelector('[name=og_image]').value;
    document.getElementById('og-title-prev').textContent = t || 'OG Title';
    document.getElementById('og-desc-prev').textContent = d || 'OG description...';
    if (img) {
        document.getElementById('og-img-prev').innerHTML = '<img src="'+img+'" style="width:100%;height:120px;object-fit:cover;" onerror="this.parentNode.textContent=\'OG Image Preview\'">';
    }
}

// Tags
function syncTags() {
    document.getElementById('tags-raw').value = document.getElementById('tags-display').value;
}
function handleTagKey(e) {
    if (e.key === 'Enter') { e.preventDefault(); const v = e.target.value.trim(); if (v && !v.endsWith(',')) { e.target.value = v + ', '; syncTags(); } }
}

// Cover upload
function uploadCover(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('cover-url').value = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Init counters
document.addEventListener('DOMContentLoaded', () => {
    const mt = document.querySelector('[name=meta_title]');
    const md = document.querySelector('[name=meta_description]');
    const og = document.querySelector('[name=og_description]');
    if (mt) document.getElementById('mt-count').textContent = mt.value.length + '/60';
    if (md) document.getElementById('md-count').textContent = md.value.length + '/160';
    if (og) document.getElementById('og-count').textContent = og.value.length + '/200';
    updateSeoPreview();
    updateOgPreview();

    // Sync content on form submit
    document.getElementById('post-form').addEventListener('submit', syncContent);
});
</script>
@endpush

@endsection
