<tr class="hover:bg-gray-50 transition">
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
        @if($level > 0)
            <div class="text-xs text-gray-500 mt-1">{{ str_repeat('└─ ', $level) }}Enfant</div>
        @endif
    </td>
    <td class="px-6 py-4">
        <span class="text-sm text-gray-700 break-all">{{ $category->full_path }}</span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @if($category->hasChildren())
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ $category->children->count() }} sous-cat{{ $category->children->count() > 1 ? 'égories' : 'égorie' }}
            </span>
        @else
            <span class="text-xs text-gray-500">—</span>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right">
        <div class="flex justify-end space-x-2">
            <a href="{{ route('categories.show', $category->id) }}" class="p-2 text-gray-600 hover:text-emerald-600 hover:bg-blue-50 rounded-lg transition" title="Voir">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
            @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('gestionnaire')))
                <a href="{{ route('categories.edit', $category->id) }}" class="p-2 text-gray-600 hover:text-amber-600 hover:bg-yellow-50 rounded-lg transition" title="Modifier">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>
@foreach($category->children as $child)
    @include('categories.partials.category-row', ['category' => $child, 'level' => $level + 1])
@endforeach
