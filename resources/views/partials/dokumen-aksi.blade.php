{{-- Param: $d (Dokumen), $jenis (slug), $u (User) --}}
<div class="flex items-center justify-end gap-1">
    @if ($d->link_dokumen)
        <button type="button" @click="$store.preview.open({url:@js($d->link_embed),raw:@js($d->link_dokumen),title:@js($d->nama_dokumen),id:{{ $d->id }}})" class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" title="Preview PDF"><i data-lucide="file-search" class="h-4 w-4"></i></button>
    @endif
    <a href="{{ route('dokumen.show', [$jenis, $d->id]) }}" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100" title="Detail"><i data-lucide="eye" class="h-4 w-4"></i></a>
    @if ($u->bisa('dokumen.ubah'))<a href="{{ route('dokumen.edit', [$jenis, $d->id]) }}" class="rounded-lg p-2 text-teal-600 hover:bg-teal-50" title="Ubah"><i data-lucide="pencil" class="h-4 w-4"></i></a>@endif
    @if ($u->bisa('dokumen.hapus'))
        <form method="POST" action="{{ route('dokumen.destroy', [$jenis, $d->id]) }}" onsubmit="return confirm('Hapus dokumen ini?')">@csrf @method('DELETE')
            <button class="rounded-lg p-2 text-rose-500 hover:bg-rose-50" title="Hapus"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
        </form>
    @endif
</div>
